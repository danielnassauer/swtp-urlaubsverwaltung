<?php
require_once dirname ( __FILE__ ) . '/server/db/DBCreator.php';
require_once dirname ( __FILE__ ) . '/server/db/HolidayRequests.php';
require_once dirname ( __FILE__ ) . '/server/db/Holidays.php';
require_once dirname ( __FILE__ ) . '/server/session/UserRights.php';

if (isset ( $_POST ['create_holidayrequests_table'] )) {
	try {
		DBCreator::createHolidayRequestsTable ();
		echo '<div class="alert alert-success" role="alert">HolidayRequests-Table erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_holidayrequests_table'] )) {
	if (UserRights::deleteTables ()) {
		try {
			DBCreator::deleteHolidayRequestsTable ();
			echo '<div class="alert alert-success" role="alert">HolidayRequests-Table erfolgreich gelöscht!</div>';
		} catch ( Exception $e ) {
			echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
		}
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

if (isset ( $_POST ['create_holidays_table'] )) {
	try {
		DBCreator::createHolidaysTable ();
		echo '<div class="alert alert-success" role="alert">Holidays-Table erfolgreich erstellt!</div>';
	} catch ( Exception $e ) {
		echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
	}
}

if (isset ( $_POST ['delete_holidays_table'] )) {
	if (UserRights::deleteTables ()) {
		try {
			DBCreator::deleteHolidaysTable ();
			echo '<div class="alert alert-success" role="alert">Holidays-Table erfolgreich gelöscht!</div>';
		} catch ( Exception $e ) {
			echo '<div class="alert alert-danger" role="alert">' . $e->getMessage () . '</div>';
		}
	}
}

if (isset ( $_POST ['create_holiday'] )) {
	date_default_timezone_set ( 'UTC' );
	$day = gmmktime ( 0, 0, 0, $_POST ["holiday_month"], $_POST ["holiday_day"], $_POST ["holiday_year"] );
	Holidays::createHoliday ( htmlentities ( $_POST ["holiday_name"], ENT_QUOTES ), $day );
}

if (isset ( $_POST ['remove_holiday'] )) {
	$name = $_POST ['remove_holiday'];
	Holidays::removeHoliday ( $name );
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
<script src="js/HolidayRequestsFilter.js"></script>

<?php require_once dirname ( __FILE__ ) . '/server/session/user.php';?>

<script type="text/javascript">
var months = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];

function editSelectedPerson(id){
	var person = getPerson(id);
	var remaining_holiday = $("#edit_remhol_" + id).val();
	var role = $("#edit_role_" + id).val();
	var fieldservice = $("#edit_fieldservice_" + id).val() == "Ja";
	var admin = $("#edit_admin_" + id).val() == "Ja";
	editPerson(id, fieldservice, remaining_holiday, role, admin);
	showUsers();
}

function showUsers(){	
	var persons = getPersons();
	var html = "";
	for(var i=0;i<persons.length;i++){		
		html += "<tr id='user"+persons[i].id+"'>"+getUserRow(persons[i])+"</tr>";
	}
	$("#table_users").html(html);
}

function getUserRow(person){
	var button_ok = "<span class='btn btn-default' onclick='editSelectedPerson("+person.id+")'><span class='ion-checkmark-round'></span></span>";
	var button_cancel = "<span class='btn btn-default' onclick='showUsers()'><span class='ion-ios-undo'></span></span>";
	var role = "<select class='form-control' id='edit_role_"+person.id+"'>";
	var rem_hol = "<input type='text' value='"+person.remaining_holiday+"' id='edit_remhol_"+person.id+"'>";
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
	return "<td>"+person.id+"</td><td>"+person.forename+" "+person.lastname+"</td><td>"+rem_hol+"</td><td>"+role+"</td><td>"+fieldservice+"</td><td>"+admin+"</td><td>"+button_ok+" "+button_cancel+"</td>";
}

function showHolidays(){
	var holidays = getHolidays();
	var html = "";
	for(var i=0;i<holidays.length;i++){	
		var button_remove = "<button type='submit' class='btn btn-default' name='remove_holiday' value='"+holidays[i].name+"'><span class='ion-trash-a'></span></button>";	
		var date = new Date(holidays[i].day*1000);
		html += "<tr><td>"+holidays[i].name+"</td><td>"+date.getDate()+". "+months[date.getMonth()]+" "+date.getFullYear()+"</td><td>"+button_remove+"</td></tr>";
	}
	$("#table_holidays").html(html);
}

function removeHolidayRequest(id){
	deleteHolidayRequest(id);
	showHolidayRequests();
}

function TsToDate(timestamp){
	var date = new Date(timestamp * 1000);
	return date.getDate()+"."+(date.getMonth()+1)+"."+date.getFullYear();
}

function showHolidayRequests(){
	var requests = getHolidayRequests();
// 	var filter_canceled = {"filter":canceledFilter, "attachment":null};
// 	var filter_accepted = {"filter":acceptedStatusFilter, "attachment":null};
// 	var filter_declined = {"filter":declinedStatusFilter, "attachment":null};
// 	var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};		
// 	var requests_accepted = filterHolidayRequests(requests,[filter_accepted]);
// 	var requests_declined = filterHolidayRequests(requests,[filter_declined]);
// 	var requests_waiting = filterHolidayRequests(requests,[filter_waiting]);		
// 	var requests_canceled = filterHolidayRequests(requests,[filter_canceled]);

	var colors = {
		1 : "success",
		2 : "warning",
		3 : "danger",
		4 : "active"
	}
	var status = {
			1 : "angenommen",
			2 : "wartend",
			3 : "abgelehnt",
			4 : "storniert"
		}
	var html = "";
	
	for(var i=0; i<requests.length; i++){
		var request = requests[i];	
		var requester = getPerson(request.person);	
		var remove_button = "<span class='btn btn-default' onclick='removeHolidayRequest("+request.id+")'><span class='ion-trash-a'></span></span>"
		html += "<tr class='"+colors[request.status]+"'><td>"+request.id+"</td><td>"+request.type+"</td><td>"+status[request.status]+"</td><td>"+requester.forename + " " + requester.lastname +"</td><td>"+TsToDate(request.start)+"</td><td>"+TsToDate(request.end)+"</td><td>"+remove_button+"</td></tr>"
	}
	$("#table_requests").html(html);
	
	
}

$(document).ready(function() {
	showHolidayRequests();
	showUsers();
	showHolidays();	
	if(user == null){
		window.location = "login.php";
	}
});
</script>

</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<span class="navbar-brand">Urlaubsverwaltung</span>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a href="index.php"><span class="ion-grid"> Übersicht</a></li>
					<li><a href="my.php"><span class="ion-home"></span> Mein Kalender</a></li>
					<li><a href="requests.php"><span class="ion-clipboard"> Anfragen</a></li>
					<li><a href="help.php"><span class="ion-help-circled"> Hilfe</a></li>
					<li class="active"><a href="admin.php"><span class="ion-gear-b"
							id="admin_ion"> Admin</a></li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
					<li id="loginPerson"></li>
				</ul>
			</div>
		</div>
	</nav>
	<script type="text/javascript">
		rows= "<a href='#'>angemeldet als ";
		rows += "<b>"+user.forename + " " + user.lastname+"</b></a>";
		console.log(user);
		$("#loginPerson").html(rows);
	</script>
	<div style="padding-top: 70px"></div>


	<div class="container-fluid">
		<div class="row">

			<nav class="col-xs-2">
				<div style="position: fixed">
					<ul class="nav nav-stacked fixed" id="sidebar">
						<li>
							<ul class="nav nav-stacked">
								<li><a href="#requests">Urlaubsanträge</a></li>
								<li><a href="#users">Benutzer</a></li>
								<li><a href="#holidays">Feiertage</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>

			<div class="col-xs-10">
				<form action="admin.php" method="POST">

					<!-- HOLIDAYREQUESTS TABLE -->
					<section id="requests">
						<h1 class="page-header">Urlaubsanträge</h1>

						<div class="panel panel-default">
							<div class="panel-body">
								<button type="submit" class="btn btn-default"
									name="create_holidayrequests_table">Tabelle erstellen</button>
								<button type="submit" class="btn btn-default"
									name="delete_holidayrequests_table">Tabelle löschen</button>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-body">
								<table class="table table-condensed">
									<thead>
										<tr>
											<th>ID</th>
											<th>Art</th>
											<th>Status</th>
											<th>Antragsteller</th>
											<th>von</th>
											<th>bis</th>
											<th>&nbsp</th>
										</tr>
									</thead>
									<tbody id="table_requests">
									</tbody>
								</table>
							</div>
						</div>
					</section>

					<!-- USERS TABLE -->
					<section id="users">
						<h1 class="page-header">Benutzer</h1>
						<div class="panel panel-default">
							<div class="panel-body">
								<button type="submit" class="btn btn-default"
									name="create_users_table">Tabelle erstellen</button>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-body">
								<table class="table table-condensed table-striped">
									<thead>
										<tr>
											<th>ID</th>
											<th>Name</th>
											<th>Verbleibende Urlaubstage</th>
											<th>Rolle</th>
											<th>Außendienst</th>
											<th>Admin</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									<tbody id="table_users">

									</tbody>
								</table>
							</div>
						</div>
					</section>

					<!-- HOLIDAYS TABLE -->
					<section id="holidays">
						<h1 class="page-header">Feiertage</h1>

						<div class="panel panel-default">
							<div class="panel-body">
								<button type="submit" class="btn btn-default"
									name="create_holidays_table">Tabelle erstellen</button>
								<button type="submit" class="btn btn-default"
									name="delete_holidays_table">Tabelle löschen</button>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-body">
								<table>
									<tr>
										<td>Name</td>
										<td><input type="text" name="holiday_name"></td>
									</tr>
									<tr>
										<td>Tag.Monat.Jahr</td>
										<td><input type="text" name="holiday_day" maxlength="2"
											size="2">.<input type="text" name="holiday_month"
											maxlength="2" size="2">.<input type="text"
											name="holiday_year" maxlength="4" size="4"></td>
									</tr>
								</table>
								<button type="submit" class="btn btn-default"
									name="create_holiday">Feiertag erstellen</button>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-body">
								<table class="table table-condensed table-striped">
									<thead>
										<tr>
											<th>Feiertag</th>
											<th>Datum</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									<tbody id="table_holidays">

									</tbody>
								</table>
							</div>
						</div>
					</section>

				</form>
			</div>
		</div>
	</div>


</body>

</html>