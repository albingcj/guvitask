<?php

require(__DIR__ . '/../configMongo.php');

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
	// Redirect to login page or handle the case where the user is not logged in
	header('Location: ../login.html');
	exit();
}

$email = $_SESSION['email'];

// Assuming $collection is your MongoDB collection
$document = $collection->findOne(['email' => $email]);
echo '<pre>';
var_dump($document);
echo '</pre>';
// Check if the user exists in the MongoDB collection
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
		echo 'New profile created successfully';
	} else {
		echo 'Failed to create a new profile';
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
		echo 'Profile updated successfully';
	} else {
		echo 'Failed to update profile';

		// Debugging: Print MongoDB error messages
		echo '<pre>';
		var_dump($updateResult->getModifiedCount(), $updateResult->getUpsertedId(), $updateResult->getUpsertedCount());
		echo '</pre>';
	}
}

// Note: You can use $document for further processing if needed
