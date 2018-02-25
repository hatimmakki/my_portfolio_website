<?php





/**
 * Checks whether the manager is registered or not.
 * Returns true if the manager found. Return false
 * if the manager is not found, or has a wrong password.
 * @param mixed $email
 * @param mixed $password
 * @return boolean
 */
function validManager($email, $password){

	try {
		$connMngr = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
		$connMngr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// add a visit record
		$row = $connMngr->prepare("SELECT ManagerId FROM Manager WHERE Email=? AND Password=?");
		$row->bindParam(1, $email);
		$row->bindParam(2, $password);
		$row->execute(); //execute the query

		if($row->rowCount() > 0)
			return true;
		else
			return false;
	}
	catch (PDOException $e){
		return false;
	}
	catch (Exception $e){
		return false;
	}
	finally {
		$connMngr = NULL;
	}

}

/**
 * Returns the manager information (JSON object)
 * @param mixed $email
 * @param mixed $password
 * @return array
 */
function login($email, $password){

	try {
		$connMngr = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
		$connMngr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $rooms_array = array();
		// add a visit record
		$row = $connMngr->prepare("SELECT * FROM Manager WHERE Email=? AND Password=?");
		$row->bindParam(1, $email);
		$row->bindParam(2, $password);
		$row->execute(); //execute the query

        if($row->rowCount() < 1) {
            // user not found

            $json_data = null;

        } else {
            // user found

            foreach($row as $rec) //loop all selected rows
            {
                $json_data = array(); //create the array
                $json_data['FName']=$rec['FName'];
                $json_data['LName']=$rec['LName'];
                $json_data['Email']=$rec['Email'];
                $json_data['RegDate']=$rec['RegDate'];

                $id = $rec['ManagerId'];

                $row2 = $connMngr->prepare("SELECT EmergencyRoom.HospitalId, EmergencyRoom.EmergencyRoomId FROM EmergencyRoom, HospitalManager WHERE EmergencyRoom.EmergencyRoomId=HospitalManager.EmergencyRoomId AND HospitalManager.ManagerId = ?");
                $row2->bindParam(1, $id);
                $row2->execute(); //execute the query


                if($row2->rowCount() > 0){

                    foreach($row2 as $rec2) //loop all selected rows
                    {
                        $rooms['HospitalId']=$rec2['HospitalId'];
                        $rooms['EmergencyRoomId']=$rec2['EmergencyRoomId'];
                        array_push($rooms_array,$rooms);
                    }
                    $json_data['Rooms']=$rooms_array;

                }

            }
        }
	}
	catch (PDOException $e){
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
		$connMngr = NULL;
        return $json_data;
	}

}

/**
 * Checks whether the email is registered or not.
 * Returns true if the email found.
 * @param mixed $email
 * @return boolean
 */
function checkEmail($email){

	try {
		$connMngr = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
		$connMngr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// add a visit record
		$row = $connMngr->prepare("SELECT ManagerId FROM Manager WHERE Email=?");
		$row->bindParam(1, $email);
		$row->execute(); //execute the query

		if($row->rowCount() > 0)
			return true;
		else
			return false;
	}
	catch (PDOException $e){
		return false;
	}
	catch (Exception $e){
		return false;
	}
	finally {
		$connMngr = NULL;
	}
}

function searchHosByName($name){

	try {
		$connMngr = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
		$connMngr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// add a visit record
		$row = $connMngr->prepare("SELECT * FROM Hospital WHERE Name LIKE ? ");
		$row->bindParam(1, $name);
		$row->execute(array("%$name%")); //execute the query

        if($row->rowCount() < 1){
            // user not found

            $json_data = null;

        } else {
            // user found

            $json_data = array(); //create the array

            foreach($row as $rec) //loop all selected rows
            {
								$json_array['HospitalId']=$rec['HospitalId'];
                $json_array['Phone']=$rec['Phone'];

                $json_array['Name']=$rec['Name'];
                $json_array['Address']=$rec['Address'];
                $json_array['Lat']=$rec['Lat'];
                $json_array['Lng']=$rec['Lng'];
                array_push($json_data,$json_array);

            }
        }
	}
	catch (PDOException $e){
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
		$connMngr = NULL;
        return $json_data;
	}
}


function getPatServed($id){

	try {

        $keys = explode(" ",$id);

		$connMngr = new PDO('mysql:host=localhost;dbname=hatimma1_hospitals','hatimma1_hospu','N{fnSgE#h7.-');
		$connMngr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $query='SELECT days.day as dys, count(Visit.VisitId ) as cnt
                FROM
                  (select curdate() as day
                   union select curdate() - interval 1 day
                   union select curdate() - interval 2 day
                   union select curdate() - interval 3 day
                   union select curdate() - interval 4 day
                   union select curdate() - interval 5 day
                   union select curdate() - interval 6 day
                   union select curdate() - interval 7 day
                   union select curdate() - interval 8 day
                   union select curdate() - interval 9 day) days
                   left join Visit
                   on days.day = DATE(Visit.DateTime) AND Visit.HospitalId = ?

                group by
                  days.day';
		// add a visit record
		$row = $connMngr->prepare($query);
		$row->bindParam(1, $id);
		$row->execute(); //execute the query

        if($row->rowCount() < 1){
            // user not found

            $json_data = null;

        } else {
            // user found

            $json_data = array(); //create the array

            foreach($row as $rec) //loop all selected rows
            {

				$json_array['day']=$rec['dys'];
                $json_array['count']=$rec['cnt'];

                array_push($json_data,$json_array);

            }
        }
	}
	catch (PDOException $e){
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
		$connMngr = NULL;
        return $json_data;
	}
}


?>