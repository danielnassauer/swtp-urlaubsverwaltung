<?php
require_once dirname ( __FILE__ ) . '/conf.php';
require_once dirname ( __FILE__ ) . '/../model/HolidayRequest.php';
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
			if ($row ["substitute1"] != "") {
				array_push ( $substitutes, $row ["substitute1"] );
			}
			if ($row ["substitute2"] != "") {
				array_push ( $substitutes, $row ["substitute2"] );
			}
			if ($row ["substitute3"] != "") {
				array_push ( $substitutes, $row ["substitute3"] );
			}
			$r = new HolidayRequest ( $row ["id"], $row ["start"], $row ["end"], $row ["person"], $substitutes, $row ["type"], $row ["status"], $row ["comment"] );
			array_push ( $requests, $r );
		}
		
		$conn->close ();
		return $requests;
	}

	public static function getRequest($id) {
		$conn = self::getDBConnection ();
		$sql = "SELECT id, start, end, person, substitute1, substitute2, substitute3, type, status, comment FROM  HolidayRequests WHERE id=" . $id . ";";
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( "Could not query holiday requests table: " . $conn->error );
		}
		
		$row = $result->fetch_assoc ();
		$substitutes = array ();
		if ($row ["substitute1"] != "") {
			array_push ( $substitutes, $row ["substitute1"] );
		}
		if ($row ["substitute2"] != "") {
			array_push ( $substitutes, $row ["substitute2"] );
		}
		if ($row ["substitute3"] != "") {
			array_push ( $substitutes, $row ["substitute3"] );
		}
		$request = new HolidayRequest ( $row ["id"], $row ["start"], $row ["end"], $row ["person"], $substitutes, $row ["type"], $row ["status"], $row ["comment"] );
		
		$conn->close ();
		return $request;
	}

	public static function createRequest($start, $end, $person, $substitutes, $type) {
		$conn = self::getDBConnection ();
		
		$sql_substitutes = "";
		$sql_substitutes_ids = "";
		if (sizeof ( $substitutes ) == 1) {
			$sql_substitutes = "substitute1,";
			$sql_substitutes_ids = $substitutes [0] . ",";
		} elseif (sizeof ( $substitutes ) == 2) {
			$sql_substitutes = "substitute1, substitute2,";
			$sql_substitutes_ids = $substitutes [0] . "," . $substitutes [1] . ",";
		} elseif (sizeof ( $substitutes ) == 3) {
			$sql_substitutes = "substitute1, substitute2, substitute3,";
			$sql_substitutes_ids = $substitutes [0] . "," . $substitutes [1] . "," . $substitutes [2] . ",";
		}
		
		$sql = "INSERT INTO HolidayRequests 
				(start, end, person, " . $sql_substitutes . " type, status, comment) 
				VALUES 
				(" . $start . "," . $end . "," . $person . "," . $sql_substitutes_ids . $type . ",2,\"\");";
		
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( $conn->error );
		}
		
		$id = mysqli_insert_id ( $conn );
		$request = new HolidayRequest ( $id, $start, $end, $person, $substitutes, $type, 2, "" );
		return $request;
	}

	public static function editRequest($id, $start, $end, $substitutes, $status, $comment) {
		$conn = self::getDBConnection ();
		
		$subs1 = "NULL";
		$subs2 = "NULL";
		$subs3 = "NULL";
		if (sizeof ( $substitutes ) > 0) {
			$subs1 = "'" . $substitutes [0] . "'";
			if (sizeof ( $substitutes ) > 1) {
				$subs2 = "'" . $substitutes [1] . "'";
				if (sizeof ( $substitutes ) > 2) {
					$subs3 = "'" . $substitutes [2] . "'";
				}
			}
		}
		
		$sql = "UPDATE HolidayRequests
				SET start=" . $start . ", end=" . $end . ", substitute1=" . $subs1 . ", substitute2=" . $subs2 . ", substitute3=" . $subs3 . ", status=" . $status . ", comment='" . $comment . "' 
				WHERE id=" . $id;
		
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( $conn->error );
		}
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