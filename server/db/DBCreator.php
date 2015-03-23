<?php
include 'conf.php';
class DBCreator {

	public static function createHolidayRequestsTable() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		$sql = "CREATE TABLE HolidayRequests (
				id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				start VARCHAR(30) NOT NULL,
				end VARCHAR(30) NOT NULL
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