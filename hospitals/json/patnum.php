<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$hid = $_POST['hospitalID'];
$rid = $_POST['roomID'];

//set vars

try {

	$conn = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// check id availability
    $row = $conn->prepare("SELECT * FROM EmergencyRoom where HospitalId=?");

	checkPara($hid);

    $row->bindParam(1, $hid);
    //$row->bindParam(2, $hid);

	//Check whether the query was successful or not
	$row->execute(); //execute the query

	if($row->rowCount() < 1)
		throw new Exception('The emirgency room is not found in the database!');

	$json_data = array(
		'success' => ($row->rowCount() === 0)? false: true,
		'room' => array()

		); //create the array

	foreach($row as $rec) //loop all selected rows
	{
		$json_array['hosId']=$rec['HospitalId'];
		$json_array['roomId']=$rec['EmergencyRoomId'];
		$json_array['patNum']=$rec['PaitientsNum'];

		//here pushing the values in to an array
		array_push($json_data['room'],$json_array);

	}


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

function checkPara($h){

	if($h == null || $h === '')
		throw new Exception('Parameters are needed!');

}

?>