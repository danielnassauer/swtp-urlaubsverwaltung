<?php
require_once dirname ( __FILE__ ) . '/conf.php';
class Authentication {
	
	// TODO check password and return right user id
	public static function signIn($user, $password) {
		global $mysql_servername, $mysql_username, $mysql_password, $db_provider;
		
		// Create connection
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_provider );
		
		// Check connection
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		
		$sql = "SELECT id, password FROM vacation WHERE login='" . $user . "'";
		$result = $conn->query ( $sql );
		$row = $result->fetch_assoc ();
		$conn->close ();
		
		if ($row ["password"] == $password) {
			return $row ["id"];
		}
	}
}
?>