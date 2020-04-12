<?php
//ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
include('dbconfig-mysql.php');
$connection = OpenCon();

$search = $_GET['search'];
$id = $_GET['id'];

function sanitize($string){
    return filter_var(trim($string),FILTER_SANITIZE_STRING);
}

if ($search&&!$id){
    $query = "SELECT * FROM contacts WHERE LOWER(name) LIKE " . "'%" . sanitize(strtolower($search)) . "%'" . " LIMIT 10";
}
else if($id&&!$search){
    $query = "SELECT * FROM contacts WHERE id = " . sanitize($id);
}
else {
    $query = "SELECT * FROM contacts LIMIT 10";
}

$result = mysqli_query($connection,$query) or die('La consulta fallo: ' . pg_last_error());

$response = array();

while ($row = mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    $response[] = $row;
}

echo json_encode($response);
?>