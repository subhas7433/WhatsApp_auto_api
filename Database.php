<?php
$hostname="localhost";
$database_name="database_name";
$database_user="database_user";
$password="password";

$conn= new mysqli($hostname,$database_user,$password,$database_name);

if ($conn-> connect_error)
{
die("Connection failed :".connect_error);
}
?>
