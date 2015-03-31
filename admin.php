<?php
require_once dirname(__FILE__).'/server/db/DBCreator.php';
require_once dirname(__FILE__).'/server/db/HolidayRequests.php';

if (isset ( $_POST ['create_holidayrequests_table'] )) {
	try {
		DBCreator::createHolidayRequestsTable ();
		echo '<div class="alert alert-success" role="alert">Urlaubsverwaltung DB erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_holidayrequests_table'] )) {
	try {
		DBCreator::deleteHolidayRequestsTable ();
		echo '<div class="alert alert-success" role="alert">Urlaubsverwaltung DB erfolgreich gelöscht!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
<title>Urlaubsverwaltung</title>

<link rel="stylesheet" href="lib/ionicons/css/ionicons.min.css" />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" />

<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>

</head>

<body>
	<div class="container">


		<!-- HOLIDAYREQUESTS DB -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Urlaubsverwaltung DB</h3>
			</div>
			<div class="panel-body">
				<form action="admin.php" method="POST">
					<button type="submit" class="btn btn-default"
						name="create_holidayrequests_table">Tabelle erstellen</button>
						<button type="submit" class="btn btn-default"
						name="delete_holidayrequests_table">Tabelle löschen</button>
				</form>
				
				<table>
				<tr><th>HolidayRequests</th></tr>
				<?php 				
					foreach (HolidayRequests::getRequests() as $request){
						echo "<tr>";
						echo "<td>".$request->toJSON()."</td>";
						echo "</tr>";
					}
				?>
				</table>
			</div>
		</div>

	</div>

</body>

</html>