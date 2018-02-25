<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'manager_functions.php';

$id = $_GET['hosId'];

//set vars
try {
	checkPara($id);
    $hospitals = getPatServed($id);
	if($hospitals == null){
		throw new Exception('No Records found!');
    }
    else
    {
        $json_data = array(
            'success' => true,
            'days' => $hospitals
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
		throw new Exception('Hospital id is required!');

}

?>