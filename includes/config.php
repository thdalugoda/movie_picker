<?php
$host= "localhost";
$db_name="movie";
$db_username="root";
$db_password="";

try
{
$pdo= new PDO('mysql:host='. $host .';dbname='.$db_name, $db_username, $db_password);
}
catch(PDOException $e)
{
exit("Error Connecting to database");
}
?>