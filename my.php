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
	
	function setFieldService(){
		var checked = $('#field_service_check').is(':checked');
		editPerson(user.id, checked, user.remaining_holiday, user.role, user.is_admin);	
		user = getPerson(user.id);		
	}

	function updateFieldserviceCheckbox(){
		$('#field_service_check').prop('checked', user.field_service);
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
			} else if ($("#radio_ill").prop("checked")) {
				var type = "Krankheit";
			} else {
				var type = $("#text_su").val();
			}
			$("#text_su").val('');
			
			if(createHolidayRequest(start, end, user.id, substitutes, type) != null){
				console.log("h")
				updatePage();
			}else {
			$("#noHolidays").modal("show");
				updatePage();
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
		liveDate.setHours(0,0,0,0)
		var fill = "Sie haben ein bereits vergangenes Datum ausgewählt";

		if(liveDate > start){
			$('#failure_text').html(fill);
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
			var request = getHolidayRequest(id);
			var start = new Date(request.start*1000);
			var end = new Date(request.end*1000);
			
			var startDay = start.getDate();
			var startMonth = (start.getMonth() + 1);
			var startYear = start.getFullYear();
			
			var endDay = end.getDate();
			var endMonth = (end.getMonth() + 1);
			var endYear = end.getFullYear();
			
			$('#start_day').val(startDay);
			$('#start_month').val(startMonth);
			$('#start_year').val(startYear);
			
			$('#end_day').val(endDay);
			$('#end_month').val(endMonth);
			$('#end_year').val(endYear);
			
			$('#offer_new_holiday_button').attr("onclick",
					"offerNewHoliday(" + id +")");
					

		}
		
	function showAlert(){
		var fill = "Sie haben einen ungültigen Urlaubstermin ausgewählt";
		$('#failure_text').html(fill);
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
		
		var liveDate = new Date();
		var heute = (Date.parse(liveDate))/1000;
		
		
		if(newStart > newEnd || heute > newStart){
			showAlert();
			return;
		}
		
		var state = 2;
		
		editHolidayRequest(id, newStart, newEnd, req.substitutes, state, req.comment);
		
		updatePage();	
	}
	
	function cancelRequest(id){
		if(confirm("Möchten Sie diesen Urlaubsantrag wirklich stornieren?")){
			var request = getHolidayRequest(id);
			editHolidayRequest(id, request.start, request.end, request.substitutes, 4, request.comment);		
			updatePage();	
		}	
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
		
		if (request.type == "Krankheit"){
				colors[1] = "alert-info";
 				colors[2] = "alert-info";
				headers[2] = "Krankheitsfall";
				headers[1] = "Krankheitsfall";
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
		
		var liveDate = new Date();
		var today = (Date.parse(liveDate))/1000;
		
		// Requests filtern
		var requests = getHolidayRequests();
		var filter_own = {"filter":isRequesterFilter, "attachment":user.id};
		var filter_accepted = {"filter":acceptedStatusFilter, "attachment":null};
		var filter_declined = {"filter":declinedStatusFilter, "attachment":null};
		var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};
		var filter_expired = {"filter": expiredHoliday,"attachment":{"today":today}};		
		requests_accepted = filterHolidayRequests(requests,[filter_own, filter_accepted, filter_expired]);
		requests_declined = filterHolidayRequests(requests,[filter_own, filter_declined, filter_expired]);
		requests_waiting = filterHolidayRequests(requests,[filter_own, filter_waiting, filter_expired]);

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
			var title = "";
			var start = unixTS2calendarTS(request.start);
			var end = unixTS2calendarTS(request.end);
				if(request.type == "Krankheit"){
					title= "Krankheitsfall";
							events.push({
							editable: false,
							title : title,
							start : start,
							end : end,
							color : '#d9edf7',
							textColor:'#333333',
							borderColor : '#333333'
						});
				}else if(request.status == 1){
					title= "Bestätigt";
						events.push({
							editable: false,
							title : title,
							start : start,
							end : end,
							color : '#dff0d8',
							textColor:'#333333',
							borderColor : '#333333'
						});
				}else if(request.status == 2){
					title= "Nicht bestätigt";
						events.push({
							editable: false,
							title : title,
							start : start,
							end : end,
							color : '#fcf8e3',
							textColor:'#333333',
							borderColor : '#333333'
						});
				}else if(request.status == 3){
					title= "Abgelehnt";
						events.push({
							editable: false,
							title : title,
							start : start,
							end : end,
							color : '#f2dede',
							textColor:'#333333',
							borderColor : '#333333'
						});
				}
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
			editable: false,
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
		if(user == null){
			window.location = "login.php";
		}
		showOwnHolidayRequests();
		restUrlaub();
		updateFieldserviceCheckbox();
		if(!user.is_admin){
			$("#admin_ion").addClass('hidden');
		}

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

						<div class="radio">
							<label> <input type="radio" name="optradio" id="radio_ill">
								Krankheitsfall
							</label>
						</div>


						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"
								name="optradio" id="radio_su" aria-label="...">
							</span> <input type="text"
								placeholder="Antrag für Sonderurlaub wegen" class="form-control"
								aria-label="..." id="text_su">
						</div>


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
				<span class="navbar-brand">Urlaubsverwaltung</span>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a href="index.php"><span class="ion-grid"> Übersicht</a></li>
					<li class="active"><a href="my.php"><span class="ion-home"></span>
							Mein Kalender</a></li>
					<li><a href="requests.php"><span class="ion-clipboard"> Anfragen</a></li>
					<li><a href="help.php"><span class="ion-help-circled"> Hilfe</a></li>
					<li><a href="admin.php"><span class="ion-gear-b" id="admin_ion">
								Admin</a></li>
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


	<form>
		<div class="container">
			<div class="panel panel-default">
				<div class="checkbox">
					<div class="panel-body">
						<table>
							<tr>
								<td>Restliche Urlaubstage</td>
								<td><span id='resttage' class="badge alert-danger"></span></td>
							</tr>
							<tr>
								<td>Zurzeit im Außendienst&nbsp;&nbsp;&nbsp;</td>
								<td><label></label><input type="checkbox" id="field_service_check"
									onclick="setFieldService()"></label></td>
							</tr>
						</table>

					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="container">
		<div class="row">
			<div class="col-xs-7">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title" style="color: #aaaaaa">Kalender</h3>
					</div>
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
					<div class="modal-body" id="failure_text"></div>
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
