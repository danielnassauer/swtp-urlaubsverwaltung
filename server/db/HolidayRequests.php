<?php
require_once dirname ( __FILE__ ) . '/conf.php';
require_once dirname ( __FILE__ ) . '/../model/HolidayRequest.php';
class HolidayRequests {
	private static $persons;

	public static function getRequests() {
		$conn = self::getDBConnection ();
		$sql = "SELECT id, start, end, person, substitute1, substitute2, substitute3, substitute1_accepted, substitute2_accepted, substitute3_accepted, type, status, comment FROM HolidayRequests";
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( "Could not query holiday requests table: " . $conn->error );
		}
		
		$requests = array ();
		while ( $row = $result->fetch_assoc () ) {
			$substitutes = array ();
			if ($row ["substitute1"] != "") {
				$substitutes [$row ["substitute1"]] = $row ["substitute1_accepted"];
			}
			if ($row ["substitute2"] != "") {
				$substitutes [$row ["substitute2"]] = $row ["substitute2_accepted"];
			}
			if ($row ["substitute3"] != "") {
				$substitutes [$row ["substitute3"]] = $row ["substitute3_accepted"];
			}
			$r = new HolidayRequest ( $row ["id"], $row ["start"], $row ["end"], $row ["person"], $substitutes, $row ["type"], $row ["status"], $row ["comment"] );
			array_push ( $requests, $r );
		}
		
		$conn->close ();
		return $requests;
	}

	public static function getRequest($id) {
		$conn = self::getDBConnection ();
		$sql = "SELECT id, start, end, person, substitute1, substitute2, substitute3, substitute1_accepted, substitute2_accepted, substitute3_accepted, type, status, comment FROM  HolidayRequests WHERE id=" . $id . ";";
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( "Could not query holiday requests table: " . $conn->error );
		}
		
		$row = $result->fetch_assoc ();
		$substitutes = array ();
		if ($row ["substitute1"] != "") {
			$substitutes [$row ["substitute1"]] = $row ["substitute1_accepted"];
		}
		if ($row ["substitute2"] != "") {
			$substitutes [$row ["substitute2"]] = $row ["substitute2_accepted"];
		}
		if ($row ["substitute3"] != "") {
			$substitutes [$row ["substitute3"]] = $row ["substitute3_accepted"];
		}
		$request = new HolidayRequest ( $row ["id"], $row ["start"], $row ["end"], $row ["person"], $substitutes, $row ["type"], $row ["status"], $row ["comment"] );
		
		$conn->close ();
		return $request;
	}

	public static function createRequest($start, $end, $person, $substitutes, $type) {
		$conn = self::getDBConnection ();
		
		$sql_substitutes = "";
		$sql_substitutes_ids = "";
		$i = 1;
		foreach ( $substitutes as $subs_id => $subs_accepted ) {
			$sql_substitutes .= "substitute" . $i . ",";
			$sql_substitutes_ids .= $subs_id . ",";
			$i ++;
		}
		
		$sql = "INSERT INTO HolidayRequests 
				(start, end, person, " . $sql_substitutes . "substitute1_accepted, substitute2_accepted, substitute3_accepted, type, status, comment) 
				VALUES 
				(" . $start . "," . $end . "," . $person . "," . $sql_substitutes_ids . "'1', '1', '1',\"" . $type . "\",2,\"\");";
		
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
		
		// Ersetzt ein Substitute einen anderen, muss zuvor die Zeile in der Tabelle umgebaut werden
		$orig_request = self::getRequest ( $id );
		foreach ( $orig_request->getSubstitutes () as $orig_sub => $orig_status ) {
			if (! array_key_exists ( $orig_sub, $substitutes )) {
				foreach ( $substitutes as $sub => $stat ) {
					if (! array_key_exists ( $sub, $orig_request->getSubstitutes () )) {
						// $sub ersetzt $orig_sub
						// Frage alte substitutes ab
						$sql = "SELECT substitute1, substitute2, substitute3 FROM  HolidayRequests WHERE id=" . $id . ";";
						$result = $conn->query ( $sql );
						if (! $result) {
							throw new Exception ( "Could not query holiday requests table: " . $conn->error );
						}
						$row = $result->fetch_assoc ();
						// welche substitute ist die ersetzte?
						$subs_nr = null;
						if ($row ["substitute1"] == $orig_sub) {
							$subs_nr = "substitute1";
						} elseif ($row ["substitute2"] == $orig_sub) {
							$subs_nr = "substitute2";
						} elseif ($row ["substitute3"] == $orig_sub) {
							$subs_nr = "substitute3";
						}
						// Ersetzte die substitute in der Tabelle. Status = 1
						$sql = "UPDATE HolidayRequests
						SET " . $subs_nr . "='" . $sub . "', " . $subs_nr . "_accepted='1' 
						WHERE id=" . $id;
						$result = $conn->query ( $sql );
						if (! $result) {
							throw new Exception ( $conn->error );
						}
						break;
					}
				}
			}
		}
		
		// Unveränderten HolidayRequest abfragen, um herauszufinden, welche ID zu welcher substitute gehört
		$sql = "SELECT substitute1, substitute2, substitute3 FROM  HolidayRequests WHERE id=" . $id . ";";
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( "Could not query holiday requests table: " . $conn->error );
		}
		$row = $result->fetch_assoc ();
		
		$sql_subs = "";
		foreach ( $substitutes as $subs_id => $subs_status ) {
			if ($subs_status != 1) {
				if ($subs_id == $row ["substitute1"]) {
					$sql_subs .= "substitute1_accepted='" . $subs_status . "',";
				} elseif ($subs_id == $row ["substitute2"]) {
					$sql_subs .= "substitute2_accepted='" . $subs_status . "',";
				} elseif ($subs_id == $row ["substitute3"]) {
					$sql_subs .= "substitute3_accepted='" . $subs_status . "',";
				}
			}
		}
		
		$sql = "UPDATE HolidayRequests
				SET start=" . $start . ", end=" . $end . "," . $sql_subs . "status=" . $status . ", comment='" . $comment . "' 
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