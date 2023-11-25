<?php

//mongo connection
require_once '../vendor/autoload.php';


$redis = new Predis\Client();
// $emailKey = "logged-mail";
// $userData = $redis->hgetall($redisKey);

$email = $redis->get("logged-mail");
$name = $redis->get("logged-name");
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
		$res = [
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
		$res = [
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

	$imagePath1 = "images/" . $email . '.' . 'jpg';
	$imagePath2 = "images/" . $email . '.' . 'png';
	$add = '../';
	if (file_exists($add . $imagePath1)) {
		$res['image'] = $imagePath1;
	} elseif (file_exists($add . $imagePath2)) {
		$res['image'] = $imagePath2;
	} else {
		$res['image'] = "https://www.placeholder.com/400";
	}

	echo json_encode($res);
} elseif (($_SERVER['REQUEST_METHOD'] === 'POST') && isset($_POST['action'])) {
	if ($_POST['action'] === 'logout') {
		$redis->del("logged-mail");
		$redis->del("logged-name");
		$redis->del("logged-pass");

		echo json_encode(['status' => 200, 'message' => 'Logout successful']);
	} elseif ($_POST['action'] == 'updatePass') {

		if (($_POST['currPass']) !== '' && ($_POST['updPass1']) !== '' && isset($_POST['updPass2']) != '') {
			if ($_POST['currPass'] !== $redis->get("logged-pass")) {

				echo json_encode(['status' => 400, 'message' => 'Incorrect current password']);
				return;
			}
			if ($_POST['updPass1'] !== $_POST['updPass2']) {

				echo json_encode(['status' => 400, 'message' => 'New passwords do not match']);
				return;
			} else {
				//sql connection
				define('DB_SERVER', 'localhost');
				define('DB_USERNAME', 'root');
				define('DB_PASSWORD', '');
				define('DB_DATABASE', 'guvitask');
				$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

				if ($db->connect_error) {
					die("Connection failed: " . $db->connect_error);
				}

				$email = $redis->get("logged-mail");
				$hashedPassword = password_hash($_POST['updPass1'], PASSWORD_DEFAULT);
				$query = "UPDATE register SET password = ? WHERE email = ?";
				$stmt = $db->prepare($query);
				$stmt->bind_param('ss', $hashedPassword, $email);
				$stmt->execute();
				$stmt->close();
				$db->close();

				$redis->set("logged-pass", $_POST['updPass1']);
				$res = [
					'status' => 200,
					'message' => 'Password updated succesfully',
				];
				echo json_encode($res);
			}
		} else {
			echo json_encode(['status' => 400, 'message' => 'Enter all the fields']);
		}
	} elseif ($_POST['action'] == 'updatePic') {
		if (isset($_FILES['profilepic'])) {
			// Assuming $email is your email variable
			$email = $redis->get("logged-mail");

			$targetDir = "../images/";
			$targetFile = $targetDir . $email . '.' . pathinfo($_FILES["profilepic"]["name"], PATHINFO_EXTENSION);
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

			// Check if image file is a actual image or fake image
			$check = getimagesize($_FILES["profilepic"]["tmp_name"]);
			if ($check !== false) {
				move_uploaded_file($_FILES["profilepic"]["tmp_name"], $targetFile);
				echo json_encode(['status' => 200, 'message' => 'Image uploaded successfully']);
			} else {
				echo json_encode(['status' => 400, 'message' => 'File is not an image.']);
			}
		} else {
			echo json_encode(['status' => 400, 'message' => 'No file selected.']);
		}
	} else if ($_POST['action'] === 'update') {
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
			$redis->set("logged-name", $_POST['profName'] !== '' ? $_POST['profName'] : $document['name']);

			if ($updateResult->getModifiedCount() > 0) {
				echo json_encode(['message' => 'Profile updated successfully']);
			} else {
				echo json_encode(['error' => 'Failed to update profile']);
			}
		}
	} else {
		echo json_encode(['status' => 400, 'message' => 'Invalid action.']);
	}
} else {
	echo json_encode(['error' => 'Unsupported request method']);
}
