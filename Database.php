<?php
$hostname="localhost";
$database_name="admin_what_gpt";
$database_user="admin_whats";
$password="5l9taWh5mm";

$conn= new mysqli($hostname,$database_user,$password,$database_name);

if ($conn-> connect_error)
{
die("Connection failed :".connect_error);
}
?>
