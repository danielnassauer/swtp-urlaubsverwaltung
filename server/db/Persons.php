<?php
require_once dirname ( __FILE__ ) . '/conf.php';
require_once dirname ( __FILE__ ) . '/../model/Person.php';
// TODO Außendienst, Resturlaub, Mitarbeiter-Position
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

	private static function connect() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_provider;
		
		self::$persons = array ();
		$users = self::getUsers ();
		
		// Create connection
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_provider );
		
		// Check connection
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		
		$sql = "SELECT id, name, vorname, abteilung FROM vacation WHERE zugehoerig='intern' AND login!=''";
		$result = $conn->query ( $sql );
		
		while ( $row = $result->fetch_assoc () ) {
			$fieldservice = false;
			$role = 1;
			$id = $row ["id"];
			if (array_key_exists ( $id, $users )) {
				$fieldservice = $users [$id] ["fieldservice"];
				$role = $users [$id] ["role"];
			}
			$p = new Person ( $id, $row ["vorname"], $row ["name"], $row ["abteilung"], $fieldservice, 25, $role );
			array_push ( self::$persons, $p );
		}
		
		$conn->close ();
	}

	private static function getUsers() {
		global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
		
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_holiday );
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		$sql = "SELECT user, role, fieldservice FROM Users";
		$result = $conn->query ( $sql );
		
		$users = array ();
		while ( $row = $result->fetch_assoc () ) {
			$users [$row ["user"]] = array (
					"role" => $row ["role"],
					"fieldservice" => $row ["fieldservice"] == 1 
			);
		}
		
		$conn->close ();
		return $users;
	}
}

?>