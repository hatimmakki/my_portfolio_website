<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

include 'manager_functions.php';

$Email = $_POST['Email'];
$Password = $_POST['Password'];

//set vars

try {

	checkPara($Email, $Password);
    $mngr = login($Email, $Password);
	if($mngr == null){
		throw new Exception('Invalid account!');
    }
    else
    {
        $json_data = array(
            'success' => true,
            'manager' => $mngr
            );
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

	echo json_encode($json_data);

}

function checkPara($Email, $Password){

	if($Email == null || $Email === '' || $Password == null || $Password === ''
		)
		throw new Exception('All fields are required!');

}

?>