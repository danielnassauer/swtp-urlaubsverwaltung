<?php
require_once dirname ( __FILE__ ) . '/conf.php';
class DBCreator {

	public static function createHolidayRequestsTable() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		$sql = "CREATE TABLE HolidayRequests (
				id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				start INT(11) NOT NULL,
				end INT(11) NOT NULL,
				person INT(11),
				substitute1 INT(11),
				substitute2 INT(11),
				substitute3 INT(11),
				type INT(1),
				status INT(1),
				comment VARCHAR(1000)
				)";
		
		self::createHolidayDB ();
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if (mysqli_connect_errno ()) {
			throw new Exception ( "MySQL connection failed: " . mysqli_connect_error () );
		}
		
		if (! $conn->query ( $sql )) {
			throw new Exception ( "Could not create holiday requests table: " . $conn->error );
		}
		
		$conn->close ();
	}

	public static function deleteHolidayRequestsTable() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if (! $conn->query ( "DROP TABLE IF EXISTS HolidayRequests" )) {
			throw new Exception ( "Table creation failed: (" . $mysqli->errno . ") " . $mysqli->error );
		}
	}

	private static function createHolidayDB() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		$sql = "CREATE DATABASE IF NOT EXISTS " . $db_holiday;
		
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password );
		if (mysqli_connect_errno ()) {
			throw new Exception ( "MySQL connection failed: " . mysqli_connect_error () );
		}
		
		if (! $conn->query ( $sql )) {
			throw new Exception ( "Could not create holiday database: " . $conn->error );
		}
		$conn->close ();
	}
}

?>