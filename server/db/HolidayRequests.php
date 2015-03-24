<?php
require_once 'conf.php';
require_once dirname(__FILE__).'/../model/HolidayRequest.php';
class HolidayRequests {
	private static $persons;

	public static function getRequests() {		
		$conn = self::getDBConnection ();
		$sql = "SELECT id, start, end, person, substitute1, substitute2, substitute3, type, status, comment FROM HolidayRequests";
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( "Could not query holiday requests table: " . $conn->error );
		}
		
		$requests = array ();
		while ( $row = $result->fetch_assoc () ) {
			$substitutes = array ();
			$r = new HolidayRequest ( $row ["id"], $row ["start"], $row ["end"], $row ["person"], $substitutes, $row ["type"], $row ["status"], $row ["comment"] );
			array_push ( $requests, $r );
		}
		
		$conn->close ();
		return $requests;
	}

	public static function getRequest($id) {
		$conn = self::getDBConnection ();
		$sql = "SELECT id, name, vorname, abteilung FROM vacation WHERE id=" . $id;
		$result = $conn->query ( $sql );
		
		$substitutes = array ();
		$request = new HolidayRequest ( $row ["id"], $row ["start"], $row ["end"], $row ["person"], $substitutes, $row ["type"], $row ["status"], $row ["comment"] );
		
		$conn->close ();
		return $request;
	}

	public static function createRequest($start, $end, $person, $substitutes, $type, $status, $comment) {
		$conn = self::getDBConnection ();
		$subs1 = 42;
		$subs2 = 42;
		$subs3 = 42;
		$sql = "INSERT INTO HolidayRequests 
				(start, end, person, substitute1, substitute2, substitute3, type, status, comment) 
				VALUES 
				(".$start.",".$end.",".$person.",".$subs1.",".$subs2.",".$subs3.",".$type.",".$status.",\"".$comment."\");";
				
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( $conn->error );
		}
		echo $result->insert_id;
		
		$result = $conn->query ( "SELECT LAST_INSERT_ID();" );
		if (! $result) {
			throw new Exception ( $conn->error );
		}

		return;
		$r = new HolidayRequest ( $id, $start, $end, $person, $substitutes, $type, $status, $comment );

		return $holReq->toJSON ();
	}

	private static function getDBConnection() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if (mysqli_connect_errno ()) {
			throw new Exception ( "MySQL connection failed: " . mysqli_connect_error () );
		}
		
		return $conn;
	}
}

?>