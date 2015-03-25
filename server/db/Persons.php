<?php
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
		$servername = "localhost";
		$username = "root";
		$password = "orion";
		$db = "provider";
		
		self::$persons = array ();
		
		// Create connection
		$conn = new mysqli ( $servername, $username, $password, $db );
		
		// Check connection
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		
		$sql = "SELECT id, name, vorname, abteilung FROM vacation WHERE zugehoerig='intern'";
		$result = $conn->query ( $sql );
		
		while ( $row = $result->fetch_assoc () ) {
			$p = new Person ( $row ["id"], $row ["vorname"], $row ["name"], $row ["abteilung"], True, 25, 1 );
			array_push ( self::$persons, $p );
		}
		
		$conn->close ();
	}
}

?>