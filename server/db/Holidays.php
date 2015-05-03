<?php
require_once dirname ( __FILE__ ) . '/conf.php';
require_once dirname ( __FILE__ ) . '/../model/Holiday.php';
class Holidays {

	public static function getHolidays() {
		$conn = self::getDBConnection ();
		$sql = "SELECT name, day FROM Holidays";
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( "Could not query holidays table: " . $conn->error );
		}
		
		$holidays = array ();
		while ( $row = $result->fetch_assoc () ) {
			$h = new Holiday ( $row ["name"], $row ["day"] );
			array_push ( $holidays, $h );
		}
		
		$conn->close ();
		return $holidays;
	}

	public static function createHoliday($name, $day) {
		$conn = self::getDBConnection ();
		
		$sql = "INSERT INTO Holidays 
				(name, day) 
				VALUES 
				('" . $name . "', '" . $day . "');";
		
		$result = $conn->query ( $sql );
		if (! $result) {
			throw new Exception ( $conn->error );
		}
		
		$holiday = new Holiday ( $name, $day );
		return $holiday;
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