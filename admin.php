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

function editSelectedPerson(id){
	var person = getPerson(id);
	var role = $("#edit_role_" + id).val();
	var fieldservice = $("#edit_fieldservice_" + id).val() == "Ja";
	var admin = $("#edit_admin_" + id).val() == "Ja";
	editPerson(id, fieldservice, person.remaining_holiday, role, admin);
	showUsers();
}

function showUsers(){	
	var persons = getPersons();
	var html = "";
	for(var i=0;i<persons.length;i++){		
		html += "<tr id='user"+persons[i].id+"'>"+getUserRow(persons[i])+"</tr>";
	}
	$("#table_users").html(html);
	console.log(persons);
}

function getUserRow(person){
	var button_ok = "<span class='btn btn-default' onclick='editSelectedPerson("+person.id+")'><span class='ion-checkmark-round'></span></span>";
	var button_cancel = "<span class='btn btn-default' onclick='showUsers()'><span class='ion-close-round'></span></span>";
	var role = "<select class='form-control' id='edit_role_"+person.id+"'>";
	if(person.role == 1){
		role += "<option value='1' selected>Mitarbeiter</option><option value='2'>Abteilungsleiter</option><option value='3'>Geschäftsleitung</option></select>";
	}
	else if(person.role == 2){
		role += "<option value='1'>Mitarbeiter</option><option value='2' selected>Abteilungsleiter</option><option value='3'>Geschäftsleitung</option></select>";
	}
	else if(person.role == 3){
		role += "<option value='1'>Mitarbeiter</option><option value='2'>Abteilungsleiter</option><option value='3' selected>Geschäftsleitung</option></select>";
	}
	var fieldservice = "<select class='form-control' id='edit_fieldservice_"+person.id+"'>";
	if(person.field_service){
		fieldservice += "<option selected>Ja</option><option>Nein</option></select>";
	}else{
		fieldservice += "<option>Ja</option><option selected>Nein</option></select>";
	}
	var admin = "<select class='form-control' id='edit_admin_"+person.id+"'>";
	if (person.is_admin){
		admin += "<option selected>Ja</option><option>Nein</option></select>";
	}else{
		admin += "<option>Ja</option><option selected>Nein</option></select>";
	}
	return "<td>"+person.id+"</td><td>"+role+"</td><td>"+fieldservice+"</td><td>"+admin+"</td><td>"+button_ok+" "+button_cancel+"</td>";
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

					<table class="table table-condensed table-striped">
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