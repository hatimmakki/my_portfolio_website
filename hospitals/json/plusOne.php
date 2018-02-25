<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'manager_functions.php';

$hid = $_POST['hospitalID'];
$rid = $_POST['roomID'];
$mngrEmail = $_POST['mngrEmail'];
$mngrPass = $_POST['mngrPass'];

//set vars

try {

	checkPara($rid, $hid, $mngrEmail, $mngrPass);

	if(!validManager($mngrEmail,$mngrPass ))
		throw new Exception('invalid manager!');

	$conn = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// add a visit record
    $row = $conn->prepare("INSERT INTO Visit (HospitalId, EmergencyRoomId, 	DateTime ) VALUES (?,?, now() )");


    $row->bindParam(1, $hid);
    $row->bindParam(2, $rid);


	// edit patients' number in the room. PaitientsNum++
	$row2 = $conn->prepare("UPDATE EmergencyRoom SET PaitientsNum=PaitientsNum+1 WHERE HospitalId=? AND EmergencyRoomId=?");
	$row2->bindParam(1, $hid);
    $row2->bindParam(2, $rid);

	$row2->execute(); //execute the query

	if($row2->rowCount() > 0)
		$row->execute(); //execute the query

	$json_data = array(
		'success' => ($row2->rowCount() === 0)? 'patient not added': 'patient added',
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

function checkPara($h, $r, $e, $p){

	if($h == null || $h === '' || $r == null || $r === '' ||
		$e == null || $e === '' || $p == null || $p === '')
		throw new Exception('Parameters are needed!');

}

?>