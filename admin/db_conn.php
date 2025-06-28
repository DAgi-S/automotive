<?php

function validate($data){
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  
if (in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1'])) {
    $sname = 'localhost';
    $uname = 'root';
    $password = '';
    $dbname = 'automotive2';
} else {
    $sname = 'localhost'; // or your host's DB host if different
    $uname = 'nati';
    $password = 'Nati-0911';
    $dbname = 'automotive';
}
$conn = mysqli_connect($sname,$uname,$password,$dbname);
if(!$conn){
    echo"connection failed";
}
?>