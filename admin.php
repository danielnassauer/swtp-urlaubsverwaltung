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

if (isset ( $_POST ['create_users_table'] )) {
	try {
		DBCreator::createUsersTable ();
		echo '<div class="alert alert-success" role="alert">Users-Table erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_users_table'] )) {
	try {
		DBCreator::deleteUsersTable ();
		echo '<div class="alert alert-success" role="alert">Users-Table erfolgreich gelöscht!</div>';
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
<script src="js/client.js"></script>
<script src="js/model.js"></script>

<script type="text/javascript">

function showUsers(){
	var persons = getPersons();
	var html = "";
	for(var i=0;i<persons.length;i++){
		html += "<tr><td>"+persons[i].id+"</td><td>"+persons[i].role+"</td><td>"+persons[i].field_service+"</td><td>"+persons[i].is_admin+"</td></tr>";
	}
	$("#table_users").html(html);
	console.log(persons);
}

$(document).ready(function() {
	showUsers();	
});
</script>

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
					<h3 class="panel-title">Benutzer</h3>
				</div>
				<div class="panel-body">

					<button type="submit" class="btn btn-default"
						name="create_users_table">Tabelle erstellen</button>
					<button type="submit" class="btn btn-default"
						name="delete_users_table">Tabelle löschen</button>

					<table class="table">
						<thead>
							<tr>
								<th>User</th>
								<th>Rolle</th>
								<th>Außendienst</th>
								<th>Admin</th>
							</tr>
						</thead>
						<tbody id="table_users">

						</tbody>
					</table>
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