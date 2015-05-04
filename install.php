<?php
require_once dirname ( __FILE__ ) . '/server/db/conf.php';
require_once dirname ( __FILE__ ) . '/server/db/DBCreator.php';
require_once dirname ( __FILE__ ) . '/server/db/Persons.php';

error_reporting(0);

function dbExists() {
	global $mysql_servername, $mysql_username, $mysql_password, $db_holiday;
	
	$conn = mysql_connect($mysql_servername, $mysql_username, $mysql_password);
	$db_selected = mysql_select_db($db_holiday, $conn);
	if ($db_selected) {
		die ( "Urlaubsverwaltung ist bereits installiert" );
	}
}

dbExists ();

if (isset ( $_POST ["admin"] )) {
	$admin = $_POST ["admin"];
	DBCreator::createHolidayRequestsTable ();
	DBCreator::createHolidaysTable ();
	DBCreator::createUsersTable ();
	Persons::editPerson ( $admin, false, 25, 1, true );
}

?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
<title>Installation</title>

<link rel="stylesheet" href="lib/ionicons/css/ionicons.min.css" />
<link rel='stylesheet' href='lib/fullcalendar/fullcalendar.css' />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" />

<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>

<script src="js/client.js"></script>
<script src="js/model.js"></script>

<script type="text/javascript">
function showUsers(){
	var options = "";
	var users = getPersons();
	console.log(users);
	for(var i=0; i< users.length; i++){
		options += "<option value='"+users[i].id+"'>"+users[i].id+": "+users[i].forename+ " "+users[i].lastname+"</option>";
	}
	$("#select_users").html(options);
}

$(document).ready(function() {
	showUsers();
});
</script>
</head>
<body>
	<form action="install.php" method="POST">
		<h3>Admin wählen</h3>
		<p>
			User-ID: <input type="text" name="admin">
			<button type="submit">Installieren</button>
		</p>
	</form>
</body>
</html>