<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
include('dbconfig-mysql.php');

$DBconnection = OpenCon();

function sanitize($string)
{
    return filter_var(trim($string), FILTER_SANITIZE_STRING);
}

function serverError()
{
    echo "this request won't work";
}

function getContacts($DBconnection)
{
    try {

        $search = $_GET['search'];
        $id = $_GET['id'];

        if ($search && !$id) {
            $query = "SELECT * FROM contacts WHERE LOWER(name) LIKE " . "'%" . sanitize(strtolower($search)) . "%'" . " LIMIT 10";
        } else if ($id && !$search) {
            $query = "SELECT * FROM contacts WHERE id = " . sanitize($id);
        } else {
            $query = "SELECT * FROM contacts LIMIT 10";
        }

        $result = mysqli_query($DBconnection, $query) or die('La consulta fallo');

        $response = array();

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $response[] = $row;
        }

        echo json_encode($response);

    } catch (Exception $error) {
        serverError();
    }
}

function postContacts(){
    echo "posted!";
}

function updateContacts(){
    echo "updated!";
}

function deleteContact(){
    echo "deleted!";
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getContacts($DBconnection);
        break;
    case 'POST':
        postContacts();
    break;
    case 'PUT':
        updateContacts();
    break;
    case 'DELETE':
        deleteContact();
    break;
    default:
        serverError();
}
