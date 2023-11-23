<?php

include("vendor/autoload.php");

$client = new MongoDB\Client("mongodb://localhost:27017");

//connecting to mongoDB
$databaseConnection = $client->myDB;

$companydb=$client->companydb;


//connecting to specific database in mongoDB
$myDatabase = $databaseConnection->myDB;

//connecting to our mongoDB Collections
$userCollection = $myDatabase->users;

// if($userCollection){
// 	echo "Collection ".$userCollection." Connected";
// }
// else{
// 	echo "Failed to connect to Database/Collection";
// }