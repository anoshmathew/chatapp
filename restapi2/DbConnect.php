<?php
//these are the server details
//the username is root by default in case of xampp
//password is nothing by default
//and lastly we have the database named android. if your database name is different you have to change it 
$servername = "localhost";
$username = "root";
//$username = "u274893100_anosh";
$password = "";
//$password = "Assasd123!";
$database = "chatbot";

//$database = "u274893100_a4album_db";
//creating a new connection object using mysqli 
$conn = new mysqli($servername, $username, $password, $database);
 
//if there is some error connecting to the database
//with die we will stop the further execution by displaying a message causing the error 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}