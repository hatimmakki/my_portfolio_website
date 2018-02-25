<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'manager_functions.php';

$name = $_POST['Name'];

//set vars
try {
	checkPara($name);
    $hospitals = searchHosByName($name);
	if($hospitals == null){
		throw new Exception('No Hospitals found!');
    }
    else
    {
        $json_data = array(
            'success' => true,
            'hospitals' => $hospitals
            );
    }
}
catch (PDOException $e){

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

echo json_encode($json_data);

}

function checkPara($str){

	if($str == null || $str === '')
		throw new Exception('Hospital name is required!');

}

?>