<?php
session_start ();
require_once dirname ( __FILE__ ) . '/../db/Authentication.php';
if (isset ( $_POST ["user"] ) && isset ( $_POST ["password"] )) {
	// sign in and set user id
	$_SESSION ["user"] = Authentication::signIn ( $_POST ["user"], $_POST ["password"] );
}

?>