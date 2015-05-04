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
<script src="js/HolidayRequestsFilter.js"></script>


<?php require_once dirname ( __FILE__ ) . '/server/session/user.php';?>

<script type="text/javascript">
	var calendar;
	var persons = getPersons();
	
	function loginPerson(){
			rows= "Hallo Hr/Fr: ";
			rows += "<b>"+user.lastname+"</b>";
			console.log(user);
			$("#loginPerson").html(rows);
	}
	function restUrlaub(){
		var rows=0;
		user = getPerson(user.id);
		rows = user.remaining_holiday;
			$("#resttage").html(rows);
	}
	function onHolidayRequestCreation() {
		$("#popup").modal("hide");
			var substitutes = {};
			var start = new Date(calendar.selected_start).getTime() / 1000;
			var end = new Date(calendar.selected_end).getTime() / 1000;

			// substitutes bestimmen
			if($("#substitutes_Menu1").val() != "-1"){
				substitutes[$("#substitutes_Menu1").val()] = 1;
			}
			if($("#substitutes_Menu2").val() != "-1"){
				substitutes[$("#substitutes_Menu2").val()] = 1;
			}
			if($("#substitutes_Menu3").val() != "-1"){
				substitutes[$("#substitutes_Menu3").val()] = 1;
			}

			// type bestimmen
			if ($("#radio_ua").prop("checked")) {
				var type = "Urlaub"
			} else if ($("#radio_fa").prop("checked")) {
				var type = "Freizeit"
			} else {
				var type = $("#text_su").val();
			}
	
			if(createHolidayRequest(start, end, user.id, substitutes, type) != null){
				updatePage();
			}else {
			$("#noHolidays").modal("show");
		}
	}

	function updatePage(){
		showOwnHolidayRequests();
		restUrlaub();
		$('#calendar').fullCalendar("removeEvents");
		$('#calendar').fullCalendar("addEventSource", holidays());
		$('#calendar').fullCalendar("addEventSource", getCalendarEvents());
	}
	
	/*
	 *	Vertretungen werden für die Tabelle gesetzt
	 */	
	function dictInSub(sub){
		var name= new Array;
		var j = 0;
		var test="";
		for(id in sub){
			for (var i = 0; i < persons.length; i++) {
				var person = persons[i];
				if (person.id == id){
				name[j] = person.lastname;
					console.log(name[j]);
				j++;
				}
			}	
		}			
	return name;
	}
	
	/*
	 *	Listet alle Vertretungen aus Abteilung auf.
	 */
	function showSubstitutesMenu() {
		var rows = "<option value='-1'>keiner</option>";
		rows += "<option role='presentation' disabled>Abteilung:</option>";
		for (var i = 0; i < persons.length; i++) {
			var person = persons[i];
			if (person.department == user.department && person.id != user.id){
				 rows += "<option value="+person.id+">"+person.forename+" "+ person.lastname +"</option>";		
		}
		}
		rows += "<option role='presentation' disabled>Alle Mitarbeiter:</option>";
		for (var x = 0; x < persons.length; x++) {
		var person = persons[x];
			if (person.department != user.department && person.id != user.id){
				 rows += "<option value="+person.id+">"+person.forename+" "+ person.lastname +"</option>";		
		}
		}
				
		$("#substitutes_Menu1").html(rows);
		$("#substitutes_Menu2").html(rows);
		$("#substitutes_Menu3").html(rows);
	}
	
	function onHolidayRequestSelection(start, end, allDay) {
		var liveDate = new Date();
		if(liveDate > start){
			$('#wrongDate').modal("show");
			} else{
		$('#popup').modal("show");
		showSubstitutesMenu();
		calendar.fullCalendar('unselect');
		calendar.selected_start = start;
		calendar.selected_end = end;
	}
	}
	
	function changeHoliday(id){
			$('#editHoliday').modal("show");
			$('#offer_new_holiday_button').attr("onclick",
					"offerNewHoliday(" + id +")");
					

		}
		
	function showAlert(){
		$('#wrongDate').modal("show");
		
	}
		
	function offerNewHoliday(id){
			
		var req = getHolidayRequest(id);
		
		var startDay = $('#start_day').val();
		var startMonth = $('#start_month').val();
		var startYear = $('#start_year').val();
		
		var endDay = $('#end_day').val();
		var endMonth = $('#end_month').val();
		var endYear = $('#end_year').val();
		
		var startDate = startMonth + "/" + startDay + "/" + startYear + " 06:00:00";
		var endDate = endMonth + "/" + endDay + "/" + endYear + " 06:00:00";
		
		var newStart = (Date.parse(startDate))/1000;
		var newEnd = (Date.parse(endDate))/1000;
		
		if(newStart > newEnd){
			console.log("HIER");
			showAlert();
			return;
		}
		
		var state = 2;
		
		editHolidayRequest(id, newStart, newEnd, req.substitutes, state, req.comment);
		
		updatePage();	
	}
	
	function cancelRequest(id){
		var request = getHolidayRequest(id);
		
		var status = 4;
		
		editHolidayRequest(id, request.start, request.end, request.substitutes, status, request.comment);
		
		updatePage();
		
	}
		
	function addRequest(request){
		var html = $("#my_requests").html();
		var colors = {
				1 : "alert-success",
				2 : "alert-warning",
				3 : "alert-danger"
		}
		var headers = {
				1 : "Bestätigter Urlaubsantrag",
				2 : "Nicht bestätigter Urlaubsantrag",
				3 : "Abgelehnter Urlaubsantrag"
		}
		var subs_status = {
				1 : "hat noch nicht geantwortet",
				2 : "hat zugestimmt",
				3 : "hat abgelehnt"
		}

		var substitutes = "";
		if(Object.keys(request.substitutes).length > 0){
			console.log("JA");
			for(var substitute in request.substitutes){
				var person = getPerson(substitute);
				substitutes += "<br>" + person.forename + " " + person.lastname + " " + subs_status[request.substitutes[substitute]];
			}	
		}

		var comment = "";
		if(request.status == 3){
			comment = "<br>Kommentar: " + request.comment;
		}

		var cancel = "";
		if(request.status == 1 || request.status == 2){
			cancel = "<a href='#' onclick='cancelRequest("+request.id+")'>stornieren</a> - ";
		}

		var edit = "";
		if(request.status == 2){
			edit = "<a href='#' onclick='changeHoliday("+request.id+")'>bearbeiten</a><br>";
		}

		var pdf = "";
		if(request.status == 1){
			pdf = "<form style='display: inline;' action='PDFCreator.php' method='POST' target='_blank' id='pdf_form'><a href='#' onclick='$(\"#pdf_form\").submit();'>PDF Bestätigung</a><input type='hidden' name='pdf_holidayrequest' value='"+request.id+"'></form><br>";
		}
		
		
		var start = new Date(request.start * 1000);
		var end = new Date(request.end * 1000);		
		html += "<div class='alert "+colors[request.status]+"'><h4>"+headers[request.status]+"</h4>"+cancel+edit+pdf+request.type+" vom "+start.getDate()
		+ "." + (start.getMonth() + 1) + "." + start.getFullYear()+" bis "+end.getDate()
		+ "." + (end.getMonth() + 1) + "." + end.getFullYear()+substitutes+comment+"</div>";
		$("#my_requests").html(html);
	}

	function clearRequests(){
		$("#my_requests").html("");
	}
	
	function showOwnHolidayRequests() {
		clearRequests();
		
		// Requests filtern
		var requests = getHolidayRequests();
		var filter_own = {"filter":isRequesterFilter, "attachment":user.id};
		var filter_accepted = {"filter":acceptedStatusFilter, "attachment":null};
		var filter_declined = {"filter":declinedStatusFilter, "attachment":null};
		var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};		
		requests_accepted = filterHolidayRequests(requests,[filter_own, filter_accepted]);
		requests_declined = filterHolidayRequests(requests,[filter_own, filter_declined]);
		requests_waiting = filterHolidayRequests(requests,[filter_own, filter_waiting]);

		// Requests anzeigen
		for(var i=0; i<requests_waiting.length; i++){
			addRequest(requests_waiting[i]);
		}
		for(var i=0; i<requests_accepted.length; i++){
			addRequest(requests_accepted[i]);
		}
		for(var i=0; i<requests_declined.length; i++){
			addRequest(requests_declined[i]);
		}
	}
	
	function unixTS2calendarTS(timestamp) {
		var date = new Date(timestamp * 1000);
		return date.getFullYear() + "-" + (date.getMonth() + 1) + "-"
				+ date.getDate();
	}
	
	function getCalendarEvents() {
		var events = [];
		function notCanceledRequestFilter(request){
		return (request.status == 1 || request.status == 2);
		}
		
		var requests = getHolidayRequests();
		
		var filter_canceled = {"filter":notCanceledRequestFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_canceled]);
		console.log(requests);
		var own_requests = [];
		for (var i = 0; i < requests.length; i++) {
			var request = requests[i];
			if (request.person == user.id) {
				own_requests.push(request);
			}
		}
		
		for (var i = 0; i < own_requests.length; i++) {
			var request = own_requests[i];
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
	
			events.push({
					title : title,
					start : start,
					end : end,
				});
		}
		return events;
	}
	
	/*
	*	Füllt den Kalender mit Feiertagen.
	*/
	function holidays(){
		var events = [];		
		var holidays = getHolidays();
		for (var i = 0; i < holidays.length; i++) {
			var holiday = holidays[i];
			var title = holiday.name;
			var start = unixTS2calendarTS(holiday.day);
			events.push({
			title : title,
			start : start,
			//end : start,
			color: '#ff9f89',
			overlap: false,
			rendering: 'background'
		});
	}
	return events;
	}

	$(document).ready(function() {
		loginPerson();
		showOwnHolidayRequests();
		restUrlaub();

		calendar = $('#calendar').fullCalendar({
			lang : 'de', 
			selectable : true,
			editable : true,
			header : {
				left : 'prev,next',
				center : 'title',
				right : 'year,month'
			},
			defaultView : 'month',
			//weekends: false,
			firstDay : 0,

			selectHelper : true,
			select : onHolidayRequestSelection,
			editable : true,
			events : [ {} ],
			eventColor : '#338005'
		});
		$('#calendar').fullCalendar("addEventSource", holidays());
		$('#calendar').fullCalendar("addEventSource", getCalendarEvents());
	});
</script>
</head>

<body>	
	
	<div id="popup" class="modal fade">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaub beantragen</h4>
				</div>
				<!-- /modal-header -->
				<div class="modal-body">

					<form role="form">

						<div class="radio">
							<label> <input checked type="radio" name="optradio" id="radio_ua">
								Urlaubsantrag
							</label>
						</div>


						<div class="radio">
							<label> <input type="radio" name="optradio" id="radio_fa">
								Freizeitantrag
							</label>
						</div>


						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"
								name="optradio" id="radio_su" aria-label="...">
							</span> <input type="text"
								placeholder="Antrag für Sonderurlaub wegen" class="form-control"
								aria-label="..." id="text_su">
						</div>
						<!-- /input-group -->
					</form>
					<div class="panel-body">
						<form class="form-inline">
							<h4>Vertretungen:</h4>
							<div class="dropdown">
								<label for="substitutes_Menu1">1.</label> <select
									id="substitutes_Menu1" class="form-control">
									<option>---</option>
								</select> <span class="form-group"> <label
									for="substitutes_Menu2">2.</label> <select
									id="substitutes_Menu2" class="form-control">
										<option>---</option>
								</select>
								</span> <span class="form-group"> <label for="substitutes_Menu3">3.</label>
									<select id="substitutes_Menu3" class="form-control">
										<option>---</option>
								</select>
								</span>
							</div>
						</form>
					</div>
				</div>
				<!-- /modal-body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-lg btn-block"
						onclick="onHolidayRequestCreation()">Abschicken</button>
				</div>
			</div>
			<!-- /modal-content -->
		</div>
		<!-- /modal-dialog -->
	</div>
	<!-- /popup -->

	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<span id="loginPerson" class="navbar-brand"></span>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a href="index.php"><span class="ion-home">Übersicht</a></li>
					<li class="active"><a href="#"><span class="ion-person"></span>
							Mein Kalender</a></li>
					<li><a href="requests.php"><span class="ion-clipboard">Anfragen</a></li>
					<li><a href="admin.php"><span class="ion-clipboard">Admin</a></li>
				</ul>
			</div>
			<div>
				<span style="margin-left: 6em" class="navbar-brand">Restliche
					Urlaubstage: <span id='resttage' style="margin-left: 1em"
					class="badge alert-danger"></span>
				</span>
			</div>
		</div>
	</nav>
	<div style="padding-top: 50px"></div>
	<div class="container">
		<div class="row">
			<div class="col-xs-7">
				<div class="panel panel-default">
					<div class="panel-heading">Kalender</div>
					<div class="panel-body">
						<div id='calendar'></div>
					</div>
				</div>
			</div>
			<div class="col-xs-5">
				<div id="my_requests"></div>
			</div>
		</div>
		<!-- /navbar -->


		<div id="deleteHoliday" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4>Urlaubsanträge löschen</h4>
					</div>
					<!-- /modal-header -->
					<div class="modal-body">
						<p>Möchten Sie ihren Urlaubsantrag wirklich stornieren?</p>
					</div>
					<!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Nein,
							doch nicht</button>
						<button type="button" class="btn btn-primary" id="cancel_holiday">Urlaubsantrag
							stornieren</button>
					</div>
					<!-- /modal-footer -->

				</div>
				<!-- /modal-content -->
			</div>
			<!-- /modal-dialog -->
		</div>
		<!-- /deleteHoliday -->

		<div id="noHolidays" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4>Sie Haben zu wenig Urlaubstage</h4>
						<div class="modal-footer">
							<button type="button" class="btn btn-default"
								data-dismiss="modal">Schließen</button>
						</div>
						<!-- /modal-footer -->
					</div>
					<!-- /modal-header -->
				</div>
				<!-- /modal-content -->
			</div>
			<!-- /modal-dialog -->
		</div>
		<!-- /noHlidays-->

		<div id="editHoliday" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4>Urlaubsanträge bearbeiten</h4>
					</div>
					<!-- /modal-header -->
					<div class="modal-body">

						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Neuen Urlaubstermin vorschlagen:</h3>
							</div>
							<div class="panel-body">
								<table>
									<tr>
										<td>Anfangsdatum: Tag.Monat.Jahr</td>
										<td><input type="text" id="start_day" maxlength="2" size="2">.<input
											type="text" id="start_month" maxlength="2" size="2">.<input
											type="text" id="start_year" maxlength="4" size="4"></td>
									</tr>
									<tr>
										<td>Enddatum: Tag.Monat.Jahr</td>
										<td><input type="text" id="end_day" maxlength="2" size="2">.<input
											type="text" id="end_month" maxlength="2" size="2">.<input
											type="text" id="end_year" maxlength="4" size="4"></td>
									</tr>
								</table>
							</div>
							<!-- /panel-body -->
						</div>
						<!-- /panel -->

					</div>
					<!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal"
							id="offer_new_holiday_button">Urlaubsantrag ändern</button>
					</div>
					<!-- /modal-footer -->

				</div>
				<!-- /modal-content -->
			</div>
			<!-- /modal-dialog -->
		</div>
		<!-- /changeHoliday -->

		<div id="wrongDate" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal"
							aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4>Fehler</h4>
					</div>
					<!-- /modal-header -->
					<div class="modal-body">
						<p>Sie haben ein Datum gewählt welches bereits vergangen ist</p>
					</div>
					<!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Zurück</button>
					</div>
				</div>
				<!-- /modal-content -->
			</div>
			<!-- /modal-dialog -->
		</div>
		<!-- /changeHoliday -->

</body>
</html>
