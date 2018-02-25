<?php
include 'manager_functions.php';

$FName = $_GET['FName'];
$LName = $_GET['LName'];
$Email = $_GET['Email'];
$Password = $_GET['Password'];

//set vars

try {

	checkPara($FName, $LName, $Email, $Password);

	if(checkEmail($Email))
		throw new Exception('The email is already registered! please try another one!');


	$conn = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// add a visit record
    $row = $conn->prepare("INSERT INTO Manager (FName, LName, RegDate, Password) VALUES (?,?, now(), ?)");
    $row->bindParam(1, $FName);
    $row->bindParam(2, $LName);
    $row->bindParam(3, $Password);

	// edit patients' number in the room. PaitientsNum++
	$row2 = $conn->prepare("UPDATE EmergencyRoom SET PaitientsNum=PaitientsNum-1 WHERE HospitalId=? AND EmergencyRoomId=?");
	$row2->bindParam(1, $hid);
    $row2->bindParam(2, $rid);

	$row2->execute(); //execute the query

	if($row2->rowCount() > 0)
		$row->execute(); //execute the query
	else
		throw new Exception('Manager is not registered!');
	$json_data = array(
		'success' => 'Manager is registered successfully!',
		);
}
catch (PDOException $e) {

	//echo 'Connection failed: ' . $e->getMessage();
	$json_data = array(
		'error' => $e->getMessage()
		);
}
catch (Exception $e){
	$json_data = array(
	'error' => $e->getMessage()
	);

}
finally {

	$conn = NULL;
	echo json_encode($json_data);

}

function checkPara($FName, $LName, $Email, $Password){

	if($FName == null || $FName === '' || $LName == null || $LName === '' ||
		$Email == null || $Email === '' || $Password == null || $Password === ''
		)
		throw new Exception('All fields are required!');

}

?>