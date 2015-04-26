<?php
require_once dirname ( __FILE__ ) . '/../db/conf.php';

function getID($username) {
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

// get username
define ( "IN_LOGIN", true );
define ( 'IN_PHPBB', true );
$phpbb_root_path = '/opt/lampp/htdocs/phpbb/';
$php_aktuell = './';
include ($phpbb_root_path . 'extension.inc');
include ($phpbb_root_path . 'common.' . $phpEx);
$userdata = session_pagestart ( $user_ip, PAGE_LOGIN );
init_userprefs ( $userdata );

if ((isset ( $userdata ['session_logged_in'] )) && ($userdata ['session_logged_in'] == "1")) {
	$username = $userdata ["username"];
	
	echo '<script type="text/javascript">
	var user = getPerson(' . getID ( $username ) . ');
</script>';
}

?>