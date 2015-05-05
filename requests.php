
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
				"onSubstituteDecision(" + id + ")");
		
		showSubstitutes();
		$("#sub_popup").modal("show");
		

	}
	

	function onSubstituteDecision(id) {
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
				updateManagementTable();
				return;
				}
				
				
		var request = getHolidayRequest(id);
		var subs = request.substitutes;
		subs[user.id] = accepted;
		editHolidayRequest(id, request.start, request.end, subs,
				request.status, request.comment);
		updateMySubstituteTable();
		updateDepartmentTable();
		updateManagementTable();
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
	
	
	function onEditDepartmentRequests(id){
		$("#btn_accept_holiday").attr("onclick",
				"onDecisionOnHoliday(" + id + ")")

		$("#department_popup").modal("show");
		
		
		}

	function onDecisionOnHoliday(id){
		
		$("#department_popup").modal("hide");
		var request = getHolidayRequest(id);
		var accepted;
		var commentDecline = $("#holiday_decline_text").val();
		var commentChange = $("#holiday_change_text").val();
		if($("#holiday_accept").prop("checked")){
			var accepted = 1;
			editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, request.comment);
		} else if($("#holiday_decline").prop("checked")){
			var accepted = 3;
			editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, commentDecline);
			}else {
				/*$("#editHoliday").modal("show");
				$('#change_holiday_button').attr("onclick",
					"offerNewHoliday(" + id +")");*/
				var accepted = 3;
				editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, commentChange);
				};
				
			
		updateDepartmentTable();
		updateMySubstituteTable();
		updateManagementTable();
	}
	
	function updateMySubstituteTable(){
		
		var requests = getHolidayRequests();

		
		var filter_my_subs = {"filter": isSubstituteFilter, "attachment":user.id};
		var filter_ready = {"filter": readyStatusFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_my_subs, filter_ready]);
		
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
						+ subStatus + "<br></br>";
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
	
	
	
	function updateDepartmentTable(){
		
		var requests = getHolidayRequests()
		var persons = getPersons();
		
		var filter_dep = {"filter" :departmentFilter, "attachment": {"department": user.department, "persons" : persons}};
		var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};
		var filter_employee = {"filter":employeeFilter, "attachment":{"persons": persons}};
		var filter_me = {"filter":withoutMeFilter, "attachment": user.id};
		var filter_sub_accepted = {"filter": substituteAcceptedFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_employee, filter_dep, filter_waiting, filter_sub_accepted, filter_me]);
		
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
						+ subStatus + "<br></br>";
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
			
			rows += "<tr onclick='onEditDepartmentRequests(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + state + "</td></tr>";
			
			}
		
		$("#department_request_list").html(rows);

		
	}




	function updateManagementTable(){		
		
		var requests = getHolidayRequests()
		var persons = getPersons();
		
		
		var filter_waiting = {"filter":waitingStatusFilter, "attachment":null};
		var filter_leitung = {"filter":abteilungsleiterFilter, "attachment": {"persons": persons}};
		var filter_me = {"filter":withoutMeFilter, "attachment": user.id};
		var filter_sub_accepted = {"filter": substituteAcceptedFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_me, filter_waiting, filter_leitung, filter_sub_accepted]);
		
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
						+ subStatus + "<br></br>";
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
			
			rows += "<tr onclick='onEditDepartmentRequests(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + state + "</td></tr>";
			
			}
		
		$("#management_request_list").html(rows);

		
		}

	

	$(document).ready(function() {
		loginPerson();
		if(user.role == 1){
			$("#departmentTable").addClass('hidden');
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
	<nav class="navbar navbar-default navbar-fixed-top">
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
					<li><a href="admin.php"><span class="ion-clipboard" id="admin_ion">Admin</a></li>
				</ul>
			</div>
			<div>
				<span style="margin-left: 6em" class="navbar-brand">Restliche Urlaubstage:	
				<span id='resttage' style="margin-left: 1em" class="badge alert-danger"></span></span>
			</div>
		</div>
	</nav>
	<div style="padding-top:50px"></div>
	
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

						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"  name="optradio" id="holiday_change" aria-label="...">
							</span> 
							<input type="text" placeholder="Antrag ablehnen und Ausweichtermin vorschlagen" class="form-control" aria-label="..." id="holiday_change_text">
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
	
</body>
</html>
