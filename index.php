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
	var calendar;
	var dep_new = new Array;
	// Beim Start werden einmalig alle Personen und Urlaubsanträge abgefragt.
	// Es ist nicht nötig, eine Person oder einen Urlaubsantrag erneut abzufragen.
	var persons = getPersons();
	var requests = getHolidayRequests();

	/*
	 *	Entfernt doppelte array Eintraege.
	 */
	function array_unique(arrayName) {
		var newArray = new Array();
		label: for (var i = 0; i < arrayName.length; i++) {
			for (var j = 0; j < newArray.length; j++) {
				if (newArray[j] == arrayName[i])
					continue label;
			}
			newArray[newArray.length] = arrayName[i];
		}
		return newArray;
	}

	/*
	 *	Füllt das department_menu mit allen Abteilungen.
	 */
	function showDepartmentMenu() {
		var dep = new Array;
		var rows = "<option>Alle</option>";

		for (var i = 0; i < persons.length; i++) {
			var person = persons[i];
			dep[i] = person.department;
		}
		var dep_new = array_unique(dep);

		for (var i = 0; i < dep_new.length; i++) {
			rows += "<option>" + dep_new[i] + "</option>";
		}
		$("#filter_department").html(rows);
	}

	/* Filtert HolidayRequests anhand von Filterfunktionen.
	 * @param filters Array von Filterfunktionen. Eine Filterfunktion bekommt 
	 *        einen HolidayRequest übergeben und gibt true zurück, wenn dieser 
	 *        übernommen werden soll. Bei einem leeren Array werden alle 
	 *        HolidayRequests übernommen.
	 * @return Array mit gefilterten HolidayRequests.
	 */
	function getFilteredHolidayRequests(filters) {
		var requ = [];
		for (var i = 0; i < requests.length; i++) {
			var accept = true;
			for (var j = 0; j < filters.length; j++) {
				if (!filters[j](requests[i])) {
					accept = false;
					break;
				}
			}
			if (accept) {
				requ.push(requests[i]);
			}
		}
		return requ;
	}

	/* Erzeugt ein Array mit Filterfunktionen für die aktuell ausgewählten
	 * Filter.
	 * @return Array mit Filterfunktionen.
	 */
	function getActualFilters() {
		var filters = [];

		// Department Filter
		var department = $("#filter_department").val();
		if (department != "Alle") {
			//Filterfunktion für Departments
			filters.push(function(request) {
				return getDepartment(request.person) == department;
			});
		}

		return filters;
	}

	/*
	 * Gibt das Department zu einer Personen-ID zurück.
	 * @return Department zu Personen-ID.
	 */
	function getDepartment(person_id) {
		for (var i = 0; i < persons.length; i++) {
			if (persons[i].id == person_id) {
				return persons[i].department;
			}
		}
	}

	function unixTS2calendarTS(timestamp) {
		var date = new Date(timestamp * 1000);
		return date.getFullYear() + "-" + (date.getMonth() + 1) + "-"
				+ date.getDate();
	}

	/*
	 * Erzeugt ein Array mit Fullcalendar-Events für alle HolidayRequest nach
	 * Anwendung der ausgewählten Filter.
	 * @return Array von Fullcalendar-Events für HolidayRequests.
	 */
	function getCalendarEvents() {
		var events = [];
		var requests = getFilteredHolidayRequests(getActualFilters());
		for (var i = 0; i < requests.length; i++) {
			var request = requests[i];
			var person;
			for (var j = 0; j < persons.length; j++) {
				if (persons[j].id == request.person) {
					person = persons[j];
					break;
				}
			}
			var title = person.forename + " " + person.lastname;
			var start = unixTS2calendarTS(request.start);
			var end = unixTS2calendarTS(request.end);
			if(person.field_service){
					title = person.forename + " " + person.lastname + " Außendienst";
					events.push({
						title : title,
						start : start,
						end : end,
						color: 'red'
					});
			}else{
			events.push({
					title : title,
					start : start,
					end : end,
				});
				}
		}
		return events;
	}

	/*
	 * Alle HolidayRequests (gefiltert) werden in den Kalendar eingetragen.
	 */
	function updateCalendar() {
		$("#calendar").fullCalendar("removeEvents");
		$('#calendar').fullCalendar("addEventSource", getCalendarEvents());
	}

	/*
	 * Initialisiert den Kalendar.
	 */
	function initCalendar() {
		calendar = $('#calendar').fullCalendar({
			lang : 'de',
			header : {
				left : 'prev,next',
				center : 'title',
				right : 'year,month'
			},
			defaultView : 'year',
			//weekends: false,
			firstDay : 0
		});
	}

	$(document).ready(function() {
		showDepartmentMenu();
		initCalendar();
		updateCalendar();
	});
</script>
</head>

<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<span class="navbar-brand">Urlaubsverwaltung</span>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li class="active"><a href="#"><span class="ion-home">Übersicht</a></li>
					<li><a href="my.php"><span class="ion-person"></span>
							Mein Kalender</a></li>
					<li><a href="requests.php"><span class="ion-clipboard">Anfragen</a></li>
					<li><a href="admin.php"><span class="ion-clipboard">Admin</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-body">
					<form class="form-inline">
						<div class="form-group">
							<label for="filter_department">Abteilung</label> <select
								id="filter_department" class="form-control">
								<option>Alle</option>
							</select> <span class='btn btn-default' onclick="updateCalendar()">Filter
								anwenden</span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row">

			<div class="panel panel-default">
				<div class="panel-heading">Kalender</div>
				<div class="panel-body">
					<div id='calendar'></div>
				</div>
			</div>
		</div>
	</div>


</body>

</html>
