<?php
require_once dirname ( __FILE__ ) . '/conf.php';
class DBCreator {

	public static function createHolidayRequestsTable() {
		$sql = "CREATE TABLE HolidayRequests (
				id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
				start INT(11) NOT NULL,
				end INT(11) NOT NULL,
				person INT(11),
				substitute1 INT(11),
				substitute2 INT(11),
				substitute3 INT(11),
				substitute1_accepted BOOLEAN,
				substitute2_accepted BOOLEAN,
				substitute3_accepted BOOLEAN,
				type VARCHAR(100),
				status INT(1),
				comment VARCHAR(1000)
				)";
		self::createTable ( $sql );
	}

	public static function deleteHolidayRequestsTable() {
		self::deleteTable ( "HolidayRequests" );
	}

	public static function createUsersTable() {
		$sql = "CREATE TABLE Users (
				user INT(11) UNSIGNED PRIMARY KEY,
				role INT(11),
				fieldservice BOOLEAN				
				)";
		self::createTable ( $sql );
	}

	public static function deleteUsersTable() {
		self::deleteTable ( "Users" );
	}

	public static function createRemainingHolidayTable() {
		$sql = "CREATE TABLE RemainingHoliday (
				user INT(11) UNSIGNED PRIMARY KEY,
				remaining_holiday INT(11)
				)";
		self::createTable ( $sql );
	}

	public static function deleteRemainingHolidayTable() {
		self::deleteTable ( "RemainingHoliday" );
	}

	private static function createTable($sql_query) {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		self::createHolidayDB ();
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if (mysqli_connect_errno ()) {
			throw new Exception ( "MySQL connection failed: " . mysqli_connect_error () );
		}
		
		if (! $conn->query ( $sql_query )) {
			throw new Exception ( "Could not create table: " . $conn->error );
		}
		
		$conn->close ();
	}

	private static function deleteTable($table) {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if (! $conn->query ( "DROP TABLE IF EXISTS " . $table )) {
			throw new Exception ( "Table deletion failed: (" . $mysqli->errno . ") " . $mysqli->error );
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