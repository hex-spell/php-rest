<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
include('dbconfig-mysql.php');

$DBconnection = OpenCon();

function sanitize(string $string): string
{
    return filter_var(trim($string), FILTER_SANITIZE_STRING);
}

function validate(string $name, string $phone): bool
{
    $contentisvalid = false;
    $lengthisvalid = false;
    if (strlen($name) >= 5 && strlen($phone) >= 5) {
        $lengthisvalid = true;
    }
    if (is_numeric($phone)) {
        $contentisvalid = true;
    }
    if ($contentisvalid && $lengthisvalid) {
        return true;
    } else {
        return false;
    }
}

function serverError()
{
    echo "this request won't work";
}

function getContacts(mysqli $DBconnection)
{  
    try {
        $search = $_GET['search'];
        $id = $_GET['id'];
        $offset = $_GET['offset'];

        if ($search && !$id) {
            $query = "SELECT * FROM contacts WHERE LOWER(name) LIKE " . "'%" . sanitize(strtolower($search)) . "%'" . " LIMIT 10";
        } else if ($id && !$search) {
            $query = "SELECT * FROM contacts WHERE id = " . sanitize($id);
        } else {
            $query = "SELECT * FROM contacts LIMIT 10";
        }

        if(is_numeric($offset)&&!$id){
            $query = $query . " OFFSET " . sanitize($offset);
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

function postContacts(mysqli $DBconnection)
{
    $body = file_get_contents("php://input");
    $data = json_decode($body, true);
    $name = sanitize($data['name']);
    $phone = sanitize($data['phone']);
    if (validate($name, $phone)) {
        try {
            $query = "INSERT INTO contacts (name,phone) VALUES ('$name',$phone)";
            $result = mysqli_query($DBconnection, $query) or die('El posteo fallo');
            if ($result) {
                echo $name . " " . $phone . " posteado";
            }
            else echo "algo salio mal";
        } catch (Exception $error) {
            echo "Algo salio mal";
        }
    } else {
        echo "los inputs son invalidos!";
    }
}

function updateContacts(mysqli $DBconnection)
{
    $body = file_get_contents("php://input");
    $data = json_decode($body, true);
    $name = sanitize($data['name']);
    $phone = sanitize($data['phone']);
    $id = sanitize($data['id']);
    if (validate($name, $phone) && is_numeric($id)) {
        try {
            $query = "UPDATE contacts SET name = '$name', phone = $phone WHERE id = $id";
            $result = mysqli_query($DBconnection, $query) or die('La actualización fallo');
            if ($result) {
                echo $name . " " . $phone . " actualizado";
            }
            else echo "algo salio mal";
        } catch (Exception $error) {
            echo "Algo salio mal";
        }
    } else {
        echo "los inputs son invalidos!";
    }
}

function deleteContact(mysqli $DBconnection)
{
    $id = sanitize($_GET['id']);
    if(is_numeric($id)){
        try{
            $query = "DELETE FROM contacts WHERE id = $id";
            $result = mysqli_query($DBconnection,$query) or die('La eliminación fallo');
            if($result){
                echo "El contacto ($id) ha sido eliminado con exito";
            }
            else echo "Algo salio mal";
        }
        catch (Exception $error) {
            echo "Algo salio mal";
        }
    }
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getContacts($DBconnection);
        break;
    case 'POST':
        postContacts($DBconnection);
        break;
    case 'PUT':
        updateContacts($DBconnection);
        break;
    case 'DELETE':
        deleteContact($DBconnection);
        break;
    default:
        serverError();
}
