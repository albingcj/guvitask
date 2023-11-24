<?php
require_once 'vendor/autoload.php'; // Adjust the path based on your project structure

// MongoDB configuration
$mongoHost = 'localhost';  // Your MongoDB host
$mongoPort = 27017;        // Your MongoDB port
$mongoDB = 'guvitask';      // Your MongoDB database name

// Create a MongoDB connection
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");

// Check if the connection was successful
if ($mongoClient) {
    // Select the database
    $database = $mongoClient->$mongoDB;

    // Select the collection (table) within the database
    $collection = $database->profile; // Assuming your collection is named 'profile'
} else {
    // Handle connection errors
    echo "Error connecting to MongoDB";
    exit();
}
