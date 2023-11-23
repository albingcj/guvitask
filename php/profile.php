<?php


if(isset($_POST['signup'])){

	$fname = $_POST['proName'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$phoneNo = $_POST['phoneNo'];
    $password = sha1($_POST['password']);
}

$data = array(
	"Firstname" => $fname,
	"Lastname" => $lname,
	"Email" => $email,
	"Phone Number" => $phoneNo,
	"Password" => $password
);

//insert into MongoDB Users Collection
$insert = $userCollection->insertOne($data);

if($insert){
	?>
		<center><h4 style="color: green;">Successfully Registered</h4></center>
		<center><a href="../index.php">Login</a></center>
	<?php
}
else{
	?>
		<center><h4 style="color: red;">Registration Failed</h4></center>
		<center><a href="../signup.php">Try Again</a></center>
	<?php
}

