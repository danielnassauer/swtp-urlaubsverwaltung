<?php
session_start ();
// TEST DATA
if (! isset ( $_SESSION ['user'] )) {
	$_SESSION ['user'] = 42;
}
?>