<?php
require_once dirname ( __FILE__ ) . '/server/session/session.php';
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
<title>Urlaubsverwaltung</title>

<link rel="stylesheet" href="lib/ionicons/css/ionicons.min.css" />
<link rel='stylesheet' href='lib/fullcalendar/fullcalendar.css' />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" />

<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
<script src="lib/fullcalendar/lib/moment.min.js"></script>
<script src="lib/fullcalendar/fullcalendar.js"></script>

<script src="js/client.js"></script>
<script src="js/model.js"></script>

<?php require_once dirname ( __FILE__ ) . '/server/session/user.php';?>

<script type="text/javascript">
	function createNewHolidayRequest() {
		createHolidayRequest(42, 4711, 80, [ 1, 2 ], 2);

		showHolidayRequests();
	}

	function showHolidayRequests() {
		var requests = getHolidayRequests();
		var rows = "";
		for (i = 0; i < requests.length; i++) {
			var request = requests[i];
			rows += "<tr><td>" + request.id + "</td><td>" + request.person
					+ "</td><td>" + request.start + "</td><td>" + request.end
					+ "</td><td>" + request.substitutes + "</td><td>"
					+ request.type + "</td><td>" + request.status + "</td><td>"
					+ request.comment + "</td></tr>";
		}
		$("#table_holidayrequests").html(rows);
	}

	function showHolidayRequest() {
		var id = $("#request_id").val();
		var request = getHolidayRequest(id);
		var row = "<tr><td>" + request.id + "</td><td>" + request.person
				+ "</td><td>" + request.start + "</td><td>" + request.end
				+ "</td><td>" + request.substitutes + "</td><td>"
				+ request.type + "</td><td>" + request.status + "</td><td>"
				+ request.comment + "</td></tr>";
		$("#table_holidayrequest").html(row);
	}

	function showPersons() {
		var persons = getPersons();
		var rows = "";
		for (i = 0; i < persons.length; i++) {
			var person = persons[i];
			rows += "<tr><td>" + person.id + "</td><td>" + person.forename
					+ "</td><td>" + person.lastname + "</td><td>"
					+ person.department + "</td><td>" + person.field_service
					+ "</td><td>" + person.remaining_holiday + "</td><td>"
					+ person.role + "</td></tr>";
		}
		$("#table_persons").html(rows);
	}

	function showPerson() {
		var id = $("#person_id").val();
		var person = getPerson(id);
		var row = "<tr><td>" + person.id + "</td><td>" + person.forename
				+ "</td><td>" + person.lastname + "</td><td>"
				+ person.department + "</td><td>" + person.field_service
				+ "</td><td>" + person.remaining_holiday + "</td><td>"
				+ person.role + "</td></tr>";
		$("#table_person").html(row);
	}

	$(document).ready(function() {
		showPersons();
		showHolidayRequests();
	});
</script>
</head>

<body>
	<div class="container">

		<h1 class="page-header">Persons</h1>

		<h2>Einzelne Personen Abfragen</h2>
		<p>Mit getPerson(id) kann eine einzelne Person anhand ihrer ID
			abgefragt werden.</p>
		<p>
			Person ID: <input type="text" id="person_id"> <input
				type="button" value="abfragen" onClick="showPerson()">
		</p>
		<table class="table table-striped table-condensed table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Forename</th>
					<th>Lastname</th>
					<th>Department</th>
					<th>Field Service</th>
					<th>Remaining Holiday</th>
					<th>Role</th>
				</tr>
			</thead>
			<tbody id="table_person">
			</tbody>
		</table>

		<h2>Alle Personen abfragen</h2>
		<p>Mit getPersons() werden alle Personen abgefragt.</p>
		<table class="table table-striped table-condensed table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Forename</th>
					<th>Lastname</th>
					<th>Department</th>
					<th>Field Service</th>
					<th>Remaining Holiday</th>
					<th>Role</th>
				</tr>
			</thead>
			<tbody id="table_persons">
			</tbody>
		</table>


		<h1 class="page-header">HolidayRequests</h1>

		<h2>Neuen Reuquest erzeugen</h2>
		<p>
			<input type="button" value="erzeugen"
				onClick="createNewHolidayRequest()">
		</p>

		<h2>Einzelne Requests Abfragen</h2>
		<p>Mit getHolidayRequest(id) kann ein einzelner HolidayRequest
			anhand seiner ID abgefragt werden.</p>
		<p>
			HolidayRequest ID: <input type="text" id="request_id"> <input
				type="button" value="abfragen" onClick="showHolidayRequest()">
		</p>
		<table class="table table-striped table-condensed table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Person ID</th>
					<th>Start</th>
					<th>End</th>
					<th>Substitutes</th>
					<th>Type</th>
					<th>Status</th>
					<th>Comment</th>
				</tr>
			</thead>
			<tbody id="table_holidayrequest">
			</tbody>
		</table>

		<h2>Alle Abfragen</h2>
		<p>Mit getHolidayRequests() werden alle HolidayRequests abgefragt.</p>
		<table class="table table-striped table-condensed table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Person ID</th>
					<th>Start</th>
					<th>End</th>
					<th>Substitutes</th>
					<th>Type</th>
					<th>Status</th>
					<th>Comment</th>
				</tr>
			</thead>
			<tbody id="table_holidayrequests">
			</tbody>
		</table>

	</div>


</body>

</html>