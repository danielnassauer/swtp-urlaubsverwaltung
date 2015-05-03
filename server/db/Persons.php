<?php
require_once dirname ( __FILE__ ) . '/conf.php';
require_once dirname ( __FILE__ ) . '/../model/Person.php';
class Persons {
	private static $persons;

	public static function getPersons() {
		if (! isset ( $persons )) {
			self::connect ();
		}
		return self::$persons;
	}

	public static function getPerson($id) {
		if (! isset ( $persons )) {
			self::connect ();
		}
		foreach ( self::$persons as $person ) {
			if ($person->getID () == $id) {
				return $person;
			}
		}
		return null;
	}

	public static function editPerson($id, $field_service, $remaining_holiday, $role, $is_admin) {
		$conn = self::getUserDBConnection ();
		if ($is_admin) {
			$is_admin = "TRUE";
		} else {
			$is_admin = "FALSE";
		}
		if ($field_service) {
			$field_service = "TRUE";
		} else {
			$field_service = "FALSE";
		}
		// Erst versuchen, neuen Eintrag zu erstellen
		$sql = "INSERT INTO Users (user, role, fieldservice, is_admin, remaining_holiday) 
				VALUES ('" . $id . "', '1', FALSE, FALSE, '25')";
		$conn->query ( $sql );
		// Dann bestehenden Eintrag abändern
		$sql = "UPDATE Users
				SET fieldservice=" . $field_service . ", role='" . $role . "', is_admin=" . $is_admin . ", remaining_holiday='" . $remaining_holiday . "' 
				WHERE user=" . $id;
		$conn->query ( $sql );
		$conn->close ();
	}

	public static function getEMail($id) {
		$conn = self::getConnection ();
		$sql = "SELECT email FROM vacation WHERE id='" . $id . "'";
		$result = $conn->query ( $sql );
		$row = $result->fetch_assoc ();
		$conn->close ();
		return $row ["email"];
	}

	private static function getConnection() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_provider;
		
		// Create connection
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_provider );
		
		// Check connection
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		
		return $conn;
	}

	private static function connect() {
		$conn = self::getConnection ();
		
		self::$persons = array ();
		$users = self::getUsers ();
		
		$sql = "SELECT id, name, vorname, abteilung FROM vacation WHERE zugehoerig='intern' AND login!=''";
		$result = $conn->query ( $sql );
		
		while ( $row = $result->fetch_assoc () ) {
			$fieldservice = false;
			$remaining_holiday = 25;
			$role = 1;
			$is_admin = false;
			$id = $row ["id"];
			if (array_key_exists ( $id, $users )) {
				$fieldservice = $users [$id] ["fieldservice"];
				$role = $users [$id] ["role"];
				$remaining_holiday = $users [$id] ["remaining_holiday"];
				$is_admin = $users [$id] ["is_admin"];
			}
			$p = new Person ( $id, $row ["vorname"], $row ["name"], $row ["abteilung"], $fieldservice, $remaining_holiday, $role, $is_admin );
			array_push ( self::$persons, $p );
		}
		
		$conn->close ();
	}

	private static function getUsers() {
		$conn = self::getUserDBConnection ();
		$sql = "SELECT user, role, remaining_holiday, fieldservice, is_admin FROM Users";
		$result = $conn->query ( $sql );
		
		$users = array ();
		while ( $row = $result->fetch_assoc () ) {
			$users [$row ["user"]] = array (
					"role" => $row ["role"],
					"remaining_holiday" => $row ["remaining_holiday"],
					"fieldservice" => $row ["fieldservice"] == 1,
					"is_admin" => $row ["is_admin"] == 1 
			);
		}
		
		$conn->close ();
		return $users;
	}

	private static function getUserDBConnection() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		return $conn;
	}
}

?>