<?php



//mongo connection
require_once '../vendor/autoload.php'; // Adjust the path based on your project structure

// MongoDB configuration
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




session_start();


$_SESSION['email'] = 'b@b.b';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
	// Redirect to login page or handle the case where the user is not logged in
	header('Location: ../login.html');  // Adjust the path based on your file structure
	exit();
}


$email = $_SESSION['email'];

// Assuming $collection is your MongoDB collection
$document = $collection->findOne(['email' => $email]);
// echo '<pre>';
// var_dump($document);
// echo '</pre>';
// Check if the user exists in the MongoDB collection
// Check if the request is for profile retrieval or update/insert
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	// Retrieve the user's profile information
	$document = $collection->findOne(['email' => $email]);

	// Check if the user exists in the MongoDB collection
	if (!$document) {
		// echo json_encode(['error' => 'User profile not found']);
		$responseData = [
			'name' => '',
			'mail' => $email,
			'address' => '',
			'mobile_number' => '',
			'state' => '',
			'pincode' => '',
			'date_of_birth' => '',
			'gender' => ''
		];
	} else {
		// Prepare the data to be sent as a JSON response
		$responseData = [
			'name' => $document['name'] ?? '',
			'mail' => $email,
			'address' => $document['address'] ?? '',
			'mobile_number' => $document['mobile_number'] ?? '',
			'state' => $document['state'] ?? '',
			'pincode' => $document['pincode'] ?? '',
			'date_of_birth' => $document['date_of_birth'] ?? '',
			'gender' => $document['gender'] ?? ''
		];
	}

	// Send the data as a JSON response
	echo json_encode($responseData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Handle form submission (update/insert)

	// Check if the user exists in the MongoDB collection
	$document = $collection->findOne(['email' => $email]);

	if (!$document) {
		// If the user doesn't exist, insert a new document with form data
		$insertResult = $collection->insertOne([
			'email' => $email,
			'name' => $_POST['profName'] ?? '',
			'mobile_number' => $_POST['profNum'] ?? '',
			'address' => $_POST['profAdd'] ?? '',
			'state' => $_POST['profSta'] ?? '',
			'pincode' => $_POST['profPin'] ?? 0,
			'date_of_birth' => $_POST['profDate'] ?? '',
			'gender' => $_POST['profGen'] ?? ''
			// Add more fields as needed
		]);

		if ($insertResult->getInsertedCount() > 0) {
			echo json_encode(['message' => 'New profile created successfully']);
		} else {
			echo json_encode(['error' => 'Failed to create a new profile']);
		}
	} else {
		// If the user exists, update the document with form data
		$updateResult = $collection->updateOne(
			['email' => $email],
			[
				'$set' => [
					'name' => $_POST['profName'] !== '' ? $_POST['profName'] : $document['name'],
					'mobile_number' => $_POST['profNum'] !== '' ? $_POST['profNum'] : $document['mobile_number'],
					'address' => $_POST['profAdd'] !== '' ? $_POST['profAdd'] : $document['address'],
					'state' => $_POST['profSta'] !== '' ? $_POST['profSta'] : $document['state'],
					'pincode' => $_POST['profPin'] !== '' ? $_POST['profPin'] : $document['pincode'],
					'date_of_birth' => $_POST['profDate'] !== '' ? $_POST['profDate'] : $document['date_of_birth'],
					'gender' => $_POST['profGen'] !== '' ? $_POST['profGen'] : $document['gender'],
					// Add more fields as needed
				]
			]
		);

		if ($updateResult->getModifiedCount() > 0) {
			echo json_encode(['message' => 'Profile updated successfully']);
		} else {
			echo json_encode(['error' => 'Failed to update profile']);
		}
	}
} else {
	// Handle unsupported request method
	echo json_encode(['error' => 'Unsupported request method']);
}
