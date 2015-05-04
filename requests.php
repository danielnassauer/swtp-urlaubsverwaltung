
<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
<title>Urlaubsverwaltung</title>

<link rel="stylesheet" href="lib/ionicons/css/ionicons.min.css" />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" />
<!-- <link rel="stylesheet" href="lib/datepicker/css/datepicker.css" />
<link rel="stylesheet" href="lib/datepicker/less/datepicker.less" /> -->
<link rel="stylesheet" href="lib/jquery-datepicker/jquery-ui.css" />

<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>

<script src="js/client.js"></script>
<script src="js/model.js"></script>
<script src="js/HolidayRequestsFilter.js"></script>

<script src="lib/less/less.js" type="text/javascript"></script>
<!-- <script src="lib/datepicker/js/bootstrap-datepicker.js"></script> -->
<script src="lib/jquery-datepicker/jquery-ui.min.js"></script>


<?php require_once dirname ( __FILE__ ) . '/server/session/user.php';?>

<script type="text/javascript">
	var persons = getPersons();
	
	function loginPerson(){
		rows= "Hallo Hr/Fr: ";
		rows += "<b>"+user.lastname+"</b>";
		console.log(user);
		$("#loginPerson").html(rows);
	}
	
	function restUrlaub(){
	var rows=0;
	rows = user.remaining_holiday;
		$("#resttage").html(rows);
	}
	
	
	function onHolidayRequestEdit(id) {
		$("#btn_accept_substitute").attr("onclick",
				"onSubstituteFinished(" + id + ")")
		showSubstitutes();
		$("#sub_popup").modal("show");
		

	}
	

	function onSubstituteFinished(id) {
		$("#sub_popup").modal("hide");
		var request = getHolidayRequest(id);
		var accepted;
		if ($("#sub_accept").prop("checked")){
			accepted = 2;
		} else if ($("#sub_decline").prop("checked")){
				accepted = 3;
			} else if ($("#sub_change").prop("checked")){
				var newSubID = $("#substitutes_Menu").val();
				var newSubDic = request.substitutes;
				delete newSubDic[user.id];
				newSubDic[newSubID] = "1";
				console.log(newSubDic);
				editHolidayRequest(id, request.start, request.end, newSubDic, request.status, request.comment); 
				var req = getHolidayRequest(id);
				console.log(req);
				updateMySubstituteTable();
				updateDepartmentTable();
				return;
				}
				
				
		var request = getHolidayRequest(id);
		var subs = request.substitutes;
		subs[user.id] = accepted;
		editHolidayRequest(id, request.start, request.end, subs,
				request.status, request.comment);
		updateMySubstituteTable();
		updateDepartmentTable();
	}
	
	function showSubstitutes(){
		var persons = getPersons();
		var rows = "<option >---</option>";
		for (var i = 0; i < persons.length; i++) {
			var person = persons[i];
				 rows += "<option value="+person.id+">"+person.forename+" "+ person.lastname +"</option>";		
		}
		
		$("#substitutes_Menu").html(rows);
	}
	
	
		function onDepartmentHolidayRequestEdit(id){
		$("#btn_accept_holiday").attr("onclick",
				"onAllowingFinished(" + id + ")")
		$("#department_popup").modal("show");
		
		
		}

	function onAllowingFinished(id){
		
		$("#department_popup").modal("hide");
		var request = getHolidayRequest(id);
		var accepted;
		var comment = $("#holiday_decline_text").val();
		if($("#holiday_accept").prop("checked")){
			var accepted = 1;
			editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, request.comment);
		} else if($("#holiday_decline").prop("checked")){
			var accepted = 3;
			editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, comment);
			}else {
				$("#editHoliday").modal("show");
				$('#change_holiday_button').on('click', function () {
					offerNewHoliday(id);
				});
				
			}
		updateDepartmentTable();
		updateMySubstituteTable();
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
		
		var state = 2;
		
		var comment = $('#change_comment').val();
		
		editHolidayRequest(id, newStart, newEnd, req.substitutes, state, comment);
		
		updateMySubstituteTable();
		updateDepartmentTable();

		
	}
	

	
	
	function updateDepartmentTable(){
		
		var requests = getHolidayRequests()
		var persons = getPersons();
		
		var filter_dep = {"filter" :departmentFilter, "attachment": {"department": user.department, "persons" : persons}};
		var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_dep, filter_waiting]);
		
		var rows = "";
		for (i = 0; i < requests.length; i++){
			
			request = requests[i];
			var start = new Date(request.start * 1000);
			var end = new Date(request.end * 1000);
			
			var persons = getPerson(request.person); // Speichert die Person die Urlaub haben will in persons
			var colleague = persons.forename + " " + persons.lastname;
			
			var keys = Object.keys(request.substitutes); //holt IDs der eingetragenen Vertretungen
			
			var subs = []; // Vertretungen als String in Array subs, damit sie nicht mit IDs angezeigt werden
			var subStatus = "";
			
			
			for (var j = 0; j < keys.length; j++) {
				var names = getPerson(keys[j]); //Namen der Vertretungen aus Array keys, in dem Vertretungen als Person Objekt gespeichert sind
				var id = names.id;
				var check = []
				check[j] = request.substitutes[keys[j]]; // checken, ob Vertretung zugesagt oder abgelehnt hat
				if (check[j] == 1){
					subStatus = "noch keine Antwort ";
				}
				if(check[j] == 2){
					subStatus = "Vertretung angenommen ";
				} else if (check[j] == 3){
					subStatus = "Vertretung abgelehnt ";
				}
				subs[j] = names.forename + " " + names.lastname + ": "
						+ subStatus;
				if (id == user.id) {
					subs[j] = "<b>"+subs[j]+"</b>"; // eigenen Namen wird dick geschrieben, damit man sofort darauf aufmerksam wird
				}
			}		
			
			var getState = request.status;
			var state = "";
			if(getState == 1){
					state = "angenommen";
			} else if(getState == 2){
					state = "wartend"
				} else if(getState == 3){
					state = "abgelehnt"
				} else{
					state = "storniert"
				}
			
			rows += "<tr onclick='onDepartmentHolidayRequestEdit(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + state + "</td></tr>";
			
			}
		
		$("#department_request_list").html(rows);

		
		}

	function updateMySubstituteTable(){
		
		var requests = getHolidayRequests();

		
		var filter_my_subs = {"filter": isSubstituteFilter, "attachment":user.id}
		
		requests = filterHolidayRequests(requests,[filter_my_subs]);
		
		var rows = "";
		for (var i = 0; i < requests.length; i++){
			
			var request;
			request = requests[i];
			var start = new Date(request.start * 1000);
			var end = new Date(request.end * 1000);
			
			var persons = getPerson(request.person); // Speichert die Person die Urlaub haben will in persons
			var colleague = persons.forename + " " + persons.lastname;
			
			var keys = Object.keys(request.substitutes); //holt IDs der eingetragenen Vertretungen
			
			var subs = []; // Vertretungen als String in Array subs, damit sie nicht mit IDs angezeigt werden
			var subStatus = "";
			
			
			for (var j = 0; j < keys.length; j++) {
				var names = getPerson(keys[j]); //Namen der Vertretungen aus Array keys, in dem Vertretungen als Person Objekt gespeichert sind
				var id = names.id;
				var check = []
				check[j] = request.substitutes[keys[j]]; // checken, ob Vertretung zugesagt oder abgelehnt hat
				if (check[j] == 1){
					subStatus = "noch keine Antwort ";
				}
				if(check[j] == 2){
					subStatus = "Vertretung angenommen ";
				} else if (check[j] == 3){
					subStatus = "Vertretung abgelehnt ";
				}
				subs[j] = names.forename + " " + names.lastname + ": "
						+ subStatus;
				if (id == user.id) {
					subs[j] = "<b>"+subs[j]+"</b>"; // eigenen Namen wird dick geschrieben, damit man sofort darauf aufmerksam wird
				}
			}			
			
			
			var getState = request.status;
			var state = "";
			if(getState == 1){
					state = "angenommen";
			} else if(getState == 2){
					state = "wartend"
				} else if(getState == 3){
					state = "abgelehnt"
				} else{
					state = "storniert"
				}
			
			rows += "<tr onclick='onHolidayRequestEdit(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + state + "</td></tr>";
			
			}
			
			$("#request_list").html(rows);
			
		
		}


function updateManagementTable(){
	
		function abteilungsleiterFilter(request, attachmet) {;
			persons = attachment["persons"];
			for(var i = 0; i < persons.length; i++){
				return persons[i].role == 2;
				}
			}
			
		
		
		var requests = getHolidayRequests()
		var persons = getPersons();
		
		var filter_dep = {"filter" :departmentFilter, "attachment": {"department": user.department, "persons" : persons}};
		var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};
		var filter_leitung = {"filter":abteilunsleiterFilter, "attachment": {"persons": persons}};
		
		requests = filterHolidayRequests(requests,[filter_dep, filter_waiting, filter_leitung]);
		
		var rows = "";
		for (i = 0; i < requests.length; i++){
			
			request = requests[i];
			var start = new Date(request.start * 1000);
			var end = new Date(request.end * 1000);
			
			var persons = getPerson(request.person); // Speichert die Person die Urlaub haben will in persons
			var colleague = persons.forename + " " + persons.lastname;
			
			var keys = Object.keys(request.substitutes); //holt IDs der eingetragenen Vertretungen
			
			var subs = []; // Vertretungen als String in Array subs, damit sie nicht mit IDs angezeigt werden
			var subStatus = "";
			
			
			for (var j = 0; j < keys.length; j++) {
				var names = getPerson(keys[j]); //Namen der Vertretungen aus Array keys, in dem Vertretungen als Person Objekt gespeichert sind
				var id = names.id;
				var check = []
				check[j] = request.substitutes[keys[j]]; // checken, ob Vertretung zugesagt oder abgelehnt hat
				if (check[j] == 1){
					subStatus = "noch keine Antwort ";
				}
				if(check[j] == 2){
					subStatus = "Vertretung angenommen ";
				} else if (check[j] == 3){
					subStatus = "Vertretung abgelehnt ";
				}
				subs[j] = names.forename + " " + names.lastname + ": "
						+ subStatus;
				if (id == user.id) {
					subs[j] = "<b>"+subs[j]+"</b>"; // eigenen Namen wird dick geschrieben, damit man sofort darauf aufmerksam wird
				}
			}		
			
			var getState = request.status;
			var state = "";
			if(getState == 1){
					state = "angenommen";
			} else if(getState == 2){
					state = "wartend"
				} else if(getState == 3){
					state = "abgelehnt"
				} else{
					state = "storniert"
				}
			
			rows += "<tr onclick='onDepartmentHolidayRequestEdit(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + state + "</td></tr>";
			
			}
		
		$("#management_request_list").html(rows);

		
		}

	/*function showOwnHolidayRequests() {
		var requests = getHolidayRequests();
		var own_requests = [];
		for (var i = 0; i < requests.length; i++) {
			var request = requests[i];
			if (user.id in request.substitutes) { // Filtert angezeigte Requests danach ob man selber als Vertretung eingetragen ist
				own_requests.push(request);

			}

		}
				
		
		var rows = "";
		for (var i = 0; i < own_requests.length; i++) {
			var request = own_requests[i];
			var start = new Date(request.start * 1000);
			var end = new Date(request.end * 1000);

			var persons = getPerson(request.person); // Speichert die Person die Urlaub haben will in persons
			var colleague = persons.forename + " " + persons.lastname; // Variable colleague in der Name des Antragstellers und nicht ID in Tabelle eingetragen werden kann

			var keys = Object.keys(request.substitutes); //holt IDs der eingetragenen Vertretungen

			var subs = []; // Vertretungen als String in Array subs, damit sie nicht mit IDs angezeigt werden
			for (var j = 0; j < keys.length; j++) {
				var names = getPerson(keys[j]); //Namen der Vertretungen aus Array keys, in dem Vertretungen als Person Objekt gespeichert sind
				var id = names.id;
				var check = []
				check[j] = request.substitutes[keys[j]]; // checken, ob Vertretung zugesagt oder abgelehnt hat
				subs[j] = names.forename + " " + names.lastname + ": "
						+ (check[j] ? "Vertretung angenommen " : "Vertretung abgelehnt ");
				if (id == user.id) {
					subs[j] = "<b>"+subs[j]+"</b>"; // eigenen Namen wird dick geschrieben, damit man sofort darauf aufmerksam wird
				}
			}			

			rows += "<tr onclick='onHolidayRequestEdit(" + request.id
					+ ")'><td>" + request.type + "</td><td>" + colleague
					+ "</td><td>" + start.getDate() + "."
					+ (start.getMonth() + 1) + "." + start.getFullYear()
					+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
					+ "." + end.getFullYear() + "</td><td>" + subs
					+ "</td><td>" + request.status + "</td></tr>";
		}

		$("#request_list").html(rows);

	} */

	$(document).ready(function() {
		loginPerson();
		if(user.role == 1){
			$("#departmentTable").addClass('hidden');
			$("#managementTable").addClass('hidden');
		}
		if(user.role == 2){
			$("#managementTable").addClass('hidden');
			updateDepartmentTable();
		} else if(user.role == 3){
			$("#departmentTable").addClass('hidden');
			updateManagementTable();
		}
		restUrlaub();
		updateMySubstituteTable();
		
	})
</script>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<span id ="loginPerson"class="navbar-brand"></span>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a href="index.php"><span class="ion-home">Übersicht</a></li>
					<li><a href="my.php"><span class="ion-person"></span>
							Mein Kalender</a></li>
					<li class="active"><a href="#"><span class="ion-clipboard">Anfragen</a></li>
					<li><a href="admin.php"><span class="ion-clipboard">Admin</a></li>
				</ul>
			</div>
			<div>
				<span style="margin-left: 6em" class="navbar-brand">Restliche Urlaubstage:	
				<span id='resttage' style="margin-left: 1em" class="badge alert-danger"></span></span>
			</div>
		</div>
	</nav>
	
	<div class="container">   <!-- Tabelle für Mitarbeiter -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Anträge in denen Ich als Vertretung angegeben wurde</h3>
		</div>
	<div class="panel-body">	
	<div> 
		<table class="table table-hover">
			<tr>
				<th>Art</th>
				<th>Mitarbeiter der Antrag gestellt hat</th>
				<th>Start</th>
				<th>Ende</th>
				<th>Vertretungen</th>
				<th>Status</th>
			</tr>
			<tbody id="request_list">

			</tbody>
		</table>
	</div> <!-- /Tabelle -->
	</div> <!-- /Panel-body -->
	</div> <!-- /panel -->
	</div> <!-- /container -->
	
	
	<div class="container" id="departmentTable">     <!-- Tabelle für Abteilungsleiter -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Anträge der Mitarbeiter</h3>
		</div>
	<div class="panel-body">	
	<div> 
		<table class="table table-hover">
			<tr>
				<th>Art</th>
				<th>Mitarbeiter der Antrag gestellt hat</th>
				<th>Start</th>
				<th>Ende</th>
				<th>Vertretungen</th>
				<th>Status</th>
			</tr>
			<tbody id="department_request_list">

			</tbody>
		</table>
	</div> <!-- /Tabelle -->
	</div> <!-- /Panel-body -->
	</div> <!-- /panel -->
	</div> <!-- /container -->
	
	
	<div class="container" id="managementTable">     <!-- Tabelle für Abteilungsleiter -->
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Anträge der Abteilungsleiter</h3>
		</div>
	<div class="panel-body">	
	<div> 
		<table class="table table-hover">
			<tr>
				<th>Art</th>
				<th>Mitarbeiter der Antrag gestellt hat</th>
				<th>Start</th>
				<th>Ende</th>
				<th>Vertretungen</th>
				<th>Status</th>
			</tr>
			<tbody id="management_request_list">

			</tbody>
		</table>
	</div> <!-- /Tabelle -->
	</div> <!-- /Panel-body -->
	</div> <!-- /panel -->
	</div> <!-- /container -->
	
<!--	<div class="input-group input-group-lg">
		<span class="input-group-addon" id="sizing-addon1">Startdatum</span>
		<input type="text" class="form-control " name="test" placeholder="Klicken um neues Startdatum zu wählen" aria-describedby="sizing-addon1" id="datepickerTest">
	</div>
	
	<div>
		<button type="button" class="btn btn-primary" onclick="test()">Geparstes Datum in Konsole</button>
	</div> -->
	
	<!-- Popup um Vertretung zuzustimmen oder abzulehnen -->
	<div id="sub_popup" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaubsanträge bearbeiten</h4>
				</div> <!-- /modal-header -->
				<div class="modal-body">

						<form role="form">
							<div class="radio">
								<label> <input type="radio" name="optradio"
									id="sub_accept" checked=""> Vertretung zustimmen
								</label>
							</div>


						<div class="radio">
							<label> <input type="radio" name="optradio" id="sub_decline">
								Vertretung ablehnen
							</label>
						</div>
						
						<form class="form-inline">
					<div class="radio">
							<label> <input type="radio" name="optradio" id="sub_change">
								Ich bin krank und gebe meine Vertretung ab an:
							</label>
						</div>
						<div class="dropdown" >
							<label for="substitutes_Menu">Vertretung</label> <select
								id="substitutes_Menu" class="form-control">
								<option>---</option>
							</select> 
						</div>
					</form>
						

					</form>

				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-lg btn-block"
							id="btn_accept_substitute">Abschicken</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /sub_popup -->
	
	
	<!-- Popup für Abteilungsleiter zum bearbeiten der Urlaubsanträge -->
	<div id="department_popup" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaubsanträge bearbeiten</h4>
				</div> <!-- /modal-header -->
				<div class="modal-body">

						<form role="form">
							<div class="radio">
								<label> <input type="radio" name="optradio"
									id="holiday_accept" checked=""> Einverstanden wie beantragt
								</label>
							</div>

						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"  name="optradio" id="holiday_decline" aria-label="...">
							</span> 
							<input type="text" placeholder="Antrag abgelehnt wegen" class="form-control" aria-label="..." id="holiday_decline_text">
						</div>

						<div class="radio">
							<label> <input type="radio" name="optradio" id="holiday_change">
								Änderung an Antrag vornehmen
							</label>
						</div>

					</form>

				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-lg btn-block"
							id="btn_accept_holiday">Abschicken</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /department_popup -->
	
	
	
	<!-- Popup für die Geschäftsleitung zum bearbeiten der Urlaubsanträge der Abteilungsleiter -->
	<div id="management_popup" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaubsanträge bearbeiten</h4>
				</div> <!-- /modal-header -->
				<div class="modal-body">

						<form role="form">
							<div class="radio">
								<label> <input type="radio" name="optradio"
									id="department_holiday_accept" checked=""> Einverstanden wie beantragt
								</label>
							</div>

						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"  name="optradio" id="department_holiday_decline" aria-label="...">
							</span> 
							<input type="text" placeholder="Antrag abgelehnt wegen" class="form-control" aria-label="..." id="holiday_decline_text">
						</div>

						<div class="radio">
							<label> <input type="radio" name="optradio" id="department_holiday_change">
								Änderung an Antrag vornehmen
							</label>
						</div>

					</form>

				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-lg btn-block"
							id="btn_accept__department_holiday">Abschicken</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /management_popup -->
	
	
	<div id="editHoliday"class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaubsanträge bearbeiten</h4>
				</div> <!-- /modal-header -->
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
											type="text" id="start_year" maxlength="4" size="4">
											</td>
									</tr>
									<tr>
										<td>Enddatum: Tag.Monat.Jahr</td>
										<td><input type="text" id="end_day" maxlength="2" size="2">.<input
											type="text" id="end_month" maxlength="2" size="2">.<input
											type="text" id="end_year" maxlength="4" size="4"></td>
									</tr>
								</table>
								<div class="input-group">
									<input type="text" class="form-control" placeholder="Begründung der Änderung" aria-describedby="sizing-addon2" id="change_comment">
								</div>
							</div><!-- /panel-body -->
						</div><!-- /panel -->


				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal" id="change_holiday_button">Urlaubsantrag ändern</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /changeHoliday -->
	

					
	


</body>
</html>
