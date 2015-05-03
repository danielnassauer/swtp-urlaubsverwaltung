<?php
require_once dirname ( __FILE__ ) . '/../db/conf.php';
define ( "IN_LOGIN", true );
define ( 'IN_PHPBB', true );
$phpbb_root_path = '/opt/lampp/htdocs/phpbb/';
$php_aktuell = './';
include ($phpbb_root_path . 'extension.inc');
include ($phpbb_root_path . 'common.' . $phpEx);
$userdata = session_pagestart ( $user_ip, PAGE_LOGIN);
init_userprefs ( $userdata );

class Session {
	
	public static function getUserID() {		
		global $userdata;
		
		if ((isset ( $userdata ['session_logged_in'] )) && ($userdata ['session_logged_in'] == "1")) {
			$username = $userdata ["username"];
			return self::getIdOfUser ( $username );
		}
		return null;
	}
	
	public static function getRights(){
		//TODO
	}

	private static function getIdOfUser($username) {
		global $mysql_servername, $mysql_username, $mysql_password, $db_provider;
		
		// Create connection
		$conn = new mysqli ( $mysql_servername, $mysql_username, $mysql_password, $db_provider );
		
		// Check connection
		if ($conn->connect_error) {
			die ( "Connection failed: " . $conn->connect_error );
		}
		
		$sql = "SELECT id FROM vacation WHERE login='" . $username . "'";
		$result = $conn->query ( $sql );
		$row = $result->fetch_assoc ();
		$conn->close ();
		return $row ["id"];
	}
}
?>