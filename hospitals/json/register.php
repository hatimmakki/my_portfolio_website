<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'manager_functions.php';

$FName = $_POST['FName'];
$LName = $_POST['LName'];
$Email = $_POST['Email'];
$Password = $_POST['Password'];

//set vars

try {

	checkPara($FName, $LName, $Email, $Password);

	if(checkEmail($Email))
		throw new Exception('The email is already registered! please try another one!');


	$conn = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// add a visit record
    $row = $conn->prepare("INSERT INTO Manager (FName, LName, RegDate, Password, Email) VALUES (?,?, now(), ?, ?)");
    $row->bindParam(1, $FName);
    $row->bindParam(2, $LName);
    $row->bindParam(3, $Password);
    $row->bindParam(4, $Email);
    $row->execute(); //execute the query
    if($row->rowCount() < 1)
		throw new Exception('Error! Staff member is not registered!');
    else
	    $json_data = array(
		    'success' => 'Staff member is registered successfully!',
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