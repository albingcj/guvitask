<?php

//mongo connection
require_once '../vendor/autoload.php';


$redis = new Predis\Client();
// $emailKey = "user:email";
// $userData = $redis->hgetall($redisKey);

$email = $redis->get("user:email");
$name = $redis->get("user:name");
if (!$email) {
	header('Location: index.html');
	exit();
}




// MongoDB 
$mongoDB = 'guvitask';
$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
if ($mongoClient) {
	$database = $mongoClient->$mongoDB;
	$collection = $database->profile;
} else {
	echo "Error connecting to MongoDB";
	exit();
}

$document = $collection->findOne(['email' => $email]);
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$document = $collection->findOne(['email' => $email]);

	if (!$document) {
		$responseData = [
			'name' => $name,
			'mail' => $email,
			'address' => '',
			'mobile_number' => '',
			'state' => '',
			'pincode' => '',
			'date_of_birth' => '',
			'gender' => ''
		];
	} else {
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

	echo json_encode($responseData);
} elseif (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['action']) ) {
	$redis->del("user:email");
	$redis->del("user:name");
  
	echo json_encode(['status' => 200, 'message' => 'Logout successful']);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$document = $collection->findOne(['email' => $email]);

	if (!$document) {
		$insertResult = $collection->insertOne([
			'email' => $email,
			'name' => $_POST['profName'] ?? '',
			'mobile_number' => $_POST['profNum'] ?? '',
			'address' => $_POST['profAdd'] ?? '',
			'state' => $_POST['profSta'] ?? '',
			'pincode' => $_POST['profPin'] ?? 0,
			'date_of_birth' => $_POST['profDate'] ?? '',
			'gender' => $_POST['profGen'] ?? ''
		]);

		if ($insertResult->getInsertedCount() > 0) {
			echo json_encode(['message' => 'New profile created successfully']);
		} else {
			echo json_encode(['error' => 'Failed to create a new profile']);
		}
	} else {
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
				]
			]
		);

		// update name inside $redis
		$redis->set("user:name", $_POST['profName'] !== '' ? $_POST['profName'] : $document['name']);

		if ($updateResult->getModifiedCount() > 0) {
			echo json_encode(['message' => 'Profile updated successfully']);
		} else {
			echo json_encode(['error' => 'Failed to update profile']);
		}
	}
} else {
	echo json_encode(['error' => 'Unsupported request method']);
}
