<?php
require_once dirname ( __FILE__ ) . '/server/db/DBCreator.php';
require_once dirname ( __FILE__ ) . '/server/db/HolidayRequests.php';

if (isset ( $_POST ['create_holidayrequests_table'] )) {
	try {
		DBCreator::createHolidayRequestsTable ();
		echo '<div class="alert alert-success" role="alert">HolidayRequests-Table erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_holidayrequests_table'] )) {
	try {
		DBCreator::deleteHolidayRequestsTable ();
		echo '<div class="alert alert-success" role="alert">HolidayRequests-Table erfolgreich gelöscht!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['create_userrights_table'] )) {
	try {
		DBCreator::createUserRightsTable ();
		echo '<div class="alert alert-success" role="alert">UserRights-Table erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_userrights_table'] )) {
	try {
		DBCreator::deleteUserRightsTable ();
		echo '<div class="alert alert-success" role="alert">UserRights-Table erfolgreich gelöscht!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['create_remainingholiday_table'] )) {
	try {
		DBCreator::createRemainingHolidayTable ();
		echo '<div class="alert alert-success" role="alert">RemainingHoliday-Table erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_remainingholiday_table'] )) {
	try {
		DBCreator::deleteRemainingHolidayTable ();
		echo '<div class="alert alert-success" role="alert">RemainingHoliday-Table erfolgreich gelöscht!</div>';
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
	<form action="admin.php" method="POST">
		<div class="container">


			<!-- HOLIDAYREQUESTS TABLE -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Urlaubsanträge</h3>
				</div>
				<div class="panel-body">
					<button type="submit" class="btn btn-default"
						name="create_holidayrequests_table">Tabelle erstellen</button>
					<button type="submit" class="btn btn-default"
						name="delete_holidayrequests_table">Tabelle löschen</button>

					<table>
						<tr>
							<th>HolidayRequests</th>
						</tr>
				<?php
				try {
					foreach ( HolidayRequests::getRequests () as $request ) {
						echo "<tr>";
						echo "<td>" . $request->toJSON () . "</td>";
						echo "</tr>";
					}
				} catch ( Exception $e ) {
					echo '<tr><td colspan="10">' . $e->getMessage () . '</td></tr>';
				}
				?>
				</table>
				</div>
			</div>

			<!-- USERRIGHTS TABLE -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Benutzerrechte</h3>
				</div>
				<div class="panel-body">

					<button type="submit" class="btn btn-default"
						name="create_userrights_table">Tabelle erstellen</button>
					<button type="submit" class="btn btn-default"
						name="delete_userrights_table">Tabelle löschen</button>

				</div>
			</div>

			<!-- REMAININGHOLIDAY TABLE -->
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Verbleibende Urlaubstage</h3>
				</div>
				<div class="panel-body">

					<button type="submit" class="btn btn-default"
						name="create_remainingholiday_table">Tabelle erstellen</button>
					<button type="submit" class="btn btn-default"
						name="delete_remainingholiday_table">Tabelle löschen</button>

				</div>
			</div>



		</div>

	</form>
</body>

</html>