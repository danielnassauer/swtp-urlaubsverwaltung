
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
	
	function restUrlaub(){
	var rows=0;
	rows = user.remaining_holiday;
		$("#resttage").html(rows);
	}
	
	
	function onHolidayRequestEdit(id) {
		$("#btn_accept_substitute").attr("onclick",
				"onSubstituteDecision(" + id + ")");
		
		showSubstitutes(id);
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
			} else if ($("#sub_change").prop("checked"))
			{
				var newSubID = $("#substitutes_Menu").val();
				var newSubDic = request.substitutes;
				delete newSubDic[user.id];
				newSubDic[newSubID] = "1";
				

				editHolidayRequest(id, request.start, request.end, newSubDic, request.status, request.comment); 
				var req = getHolidayRequest(id);

				updateMySubstituteTable();
				updateDepartmentTable();
				updateManagementTable();
				  $.post( "./server/model/EmailArt.php", {email_holidayrequest:id,type:1}).done(function(data){
				  });
				return;
				}
				
				
		var request = getHolidayRequest(id);
		var subs = request.substitutes;
		subs[user.id] = accepted;
		var email = "";
		if(accepted == 2){
			//email = "<form style='display: inline;' action='' method='POST' target='_blank' id='pdf_form'><a href='#' onclick='$(\"#pdf_form\").submit();'>PDF Bestätigung</a><input type='hidden' name='email_holidayrequest' value='"+request.id+"'></form><br>";
		    $.post( "./server/model/EmailArt.php", {email_holidayrequest:id,type:2,sub:user.id}).done(function(data){
				});
		}
		editHolidayRequest(id, request.start, request.end, subs,
				request.status, request.comment);
		updateMySubstituteTable();
		updateDepartmentTable();
		updateManagementTable();
	}
	
	function showSubstitutes(id){
		var request = getHolidayRequest(id);
		var persons = getPersons();
		var rows = "<option >---</option>";
		for (var i = 0; i < persons.length; i++) {
			if(persons[i].id != user.id){
			var person = persons[i];
				 rows += "<option value="+person.id+">"+person.forename+" "+ person.lastname +"</option>";
			}		
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
				var accepted = 3;
				editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, commentChange);
				};
		$("#holiday_decline_text").val('');
		$("#holiday_change_text").val('');
			
		updateDepartmentTable();
		updateMySubstituteTable();
		updateManagementTable();
	}
	
	function updateMySubstituteTable(){
		
		var requests = getHolidayRequests();
		var liveDate = new Date();
		var today = (Date.parse(liveDate))/1000;
		
		var filter_my_subs = {"filter": isSubstituteFilter, "attachment":user.id};
		var filter_ready = {"filter": readyStatusFilter, "attachment":null};
		var filter_declined = {"filter": substituteDeclinedFilter, "attachment":user.id};
		var filter_expired = {"filter": expiredHoliday,"attachment":{"today":today}};
		
		
		requests = filterHolidayRequests(requests,[filter_my_subs, filter_ready, filter_declined, filter_expired]);
		console.log(requests);
		
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
			
			var edit = "";
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
					if(check[j] == 1){
					edit = "<a href='#' onclick='onHolidayRequestEdit(" + request.id
						+ ")'>antworten</a><br>";}
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
			
			rows += "<tr><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + state + "</td><td>" + edit + "</td></tr>";
			
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
		var filter_no_illness = {"filter": noIllnessFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_employee, filter_dep, filter_waiting, filter_sub_accepted, filter_me, filter_no_illness]);
;
		
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
			var edit = "";
			if(getState == 2){
					edit = "<a href='#' onclick='onEditDepartmentRequests(" + request.id
						+ ")'>antworten</a><br>";
			}
			
			rows += "<tr><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + edit + "</td></tr>";
			
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
		var filter_no_illness = {"filter": noIllnessFilter, "attachment":null};
		
		requests = filterHolidayRequests(requests,[filter_me, filter_waiting, filter_leitung, filter_sub_accepted, filter_no_illness]);
		
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
			var edit = "";
			if(getState == 2){
					edit = "<a href='#' onclick='onEditDepartmentRequests(" + request.id
						+ ")'>antworten</a><br>";
			}
			
			rows += "<tr><td>" + request.type + "</td><td>" + colleague
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + subs
						+ "</td><td>" + edit + "</td></tr>";
			
			}
		
		$("#management_request_list").html(rows);

		
		}

	function showDepartmentTable(){
		$("#departmentTable").removeClass('hidden');
		$("#substitute_link").addClass('hidden');
	}

	function hideDepartmentTable(){
		$("#departmentTable").addClass('hidden');
		if(user.role != 3){
			$("#substitute_link").removeClass('hidden');			
		}
	}

	$(document).ready(function() {
		if(user == null){
			window.location = "login.php";
		}
		showDepartmentTable();
		if(!user.is_admin){
			$("#admin_ion").addClass('hidden');
		}
		if(user.role == 1){
			hideDepartmentTable();
			$("#managementTable").addClass('hidden');
		}
		if(user.role == 2){
			$("#managementTable").addClass('hidden');
			updateDepartmentTable();
		} else if(user.role == 3){
			hideDepartmentTable();
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
				<span class="navbar-brand">Urlaubsverwaltung</span>
			</div>
			<div>
				<ul class="nav navbar-nav">
					<li><a href="index.php"><span class="ion-grid"> Übersicht</a></li>
					<li><a href="my.php"><span class="ion-home"></span> Mein Kalender</a></li>
					<li class="active"><a href="requests.php"><span
							class="ion-clipboard"> Anfragen</a></li>
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
		$("#loginPerson").html(rows);
	</script>
	<div style="padding-top: 70px"></div>

	<div class="container">
		<!-- Tabelle für Mitarbeiter -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title" style="color: #aaaaaa">Meine
					Vertretungsanfragen</h3>
			</div>
			<div class="panel-body">					
				Bitte bearbeiten Sie die an Sie gerichteten Vertretungsanfragen.
				<!-- /Tabelle -->
			</div>
			<table class="table">
						<tr>
							<th>Art</th>
							<th>Mitarbeiter der Antrag gestellt hat</th>
							<th>Start</th>
							<th>Ende</th>
							<th>Vertretungen</th>
							<th>Status</th>
							<th></th>
						</tr>
						<tbody id="request_list">

						</tbody>
					</table>
			<!-- /Panel-body -->
		</div>
		<!-- /panel -->
	</div>
	<!-- /container -->


	<div class="container">
		<!-- Tabelle für Abteilungsleiter -->
		<div class="panel panel-default" id="departmentTable">
			<div class="panel-heading">
				<h3 class="panel-title" style="color: #aaaaaa">Urlaubsanfragen der
					Mitarbeiter</h3>
			</div>
			<div class="panel-body">
				Bitte bearbeiten Sie die Urlaubsanfragen ihrer Mitarbeiter.
				<!-- /Tabelle -->
			</div>
			<table class="table table-hover">
						<tr>
							<th>Art</th>
							<th>Mitarbeiter der Antrag gestellt hat</th>
							<th>Start</th>
							<th>Ende</th>
							<th>Vertretungen</th>
							<th></th>
						</tr>
						<tbody id="department_request_list">

						</tbody>
					</table>
			<!-- /Panel-body -->
		</div>
		<!-- /panel -->
		<a href="#" onclick="showDepartmentTable()" id="substitute_link">Ich übernehme zurzeit die Vertretung für meinen Abteilungsleiter</a>
	</div>
	<!-- /container -->


	<div class="container" id="managementTable">
		<!-- Tabelle für Geschäftsleitung -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title" style="color: #aaaaaa">Urlaubsanfragen der
					Abteilungsleiter</h3>
			</div>
			<div class="panel-body">					
				Bitte bearbeiten Sie die Urlaubsanfragen der Abteilungsleiter.
				<!-- /Tabelle -->
			</div>
			<table class="table table-hover">
						<tr>
							<th>Art</th>
							<th>Mitarbeiter der Antrag gestellt hat</th>
							<th>Start</th>
							<th>Ende</th>
							<th>Vertretungen</th>
							<th></th>
						</tr>
						<tbody id="management_request_list">

						</tbody>
					</table>
			<!-- /Panel-body -->
		</div>
		<!-- /panel -->
	</div>
	<!-- /container -->


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
				</div>
				<!-- /modal-header -->
				<div class="modal-body">

					<form role="form">
						<div class="radio">
							<label> <input type="radio" name="optradio" id="sub_accept"
								checked=""> Vertretung zustimmen
							</label>
						</div>


						<div class="radio">
							<label> <input type="radio" name="optradio" id="sub_decline">
								Vertretung ablehnen
							</label>
						</div>

						<form class="form-inline">
							<div class="radio">
								<label> <input type="radio" name="optradio" id="sub_change"> Ich
									möchte meine Vertretung abgeben an:
								</label>
							</div>
							<div class="dropdown">
								<label for="substitutes_Menu">Vertretung</label> <select
									id="substitutes_Menu" class="form-control">
									<option>---</option>
								</select>
							</div>
						</form>


					</form>

				</div>
				<!-- /modal-body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-lg btn-block"
						id="btn_accept_substitute">Abschicken</button>
				</div>
				<!-- /modal-footer -->

			</div>
			<!-- /modal-content -->
		</div>
		<!-- /modal-dialog -->
	</div>
	<!-- /sub_popup -->


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
				</div>
				<!-- /modal-header -->
				<div class="modal-body">

					<form role="form">
						<div class="radio">
							<label> <input type="radio" name="optradio" id="holiday_accept"
								checked=""> Einverstanden wie beantragt
							</label>
						</div>

						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"
								name="optradio" id="holiday_decline" aria-label="...">
							</span> <input type="text" placeholder="Antrag abgelehnt wegen"
								class="form-control" aria-label="..." id="holiday_decline_text">
						</div>

						<div class="input-group">
							<span class="input-group-addon"> <input type="radio"
								name="optradio" id="holiday_change" aria-label="...">
							</span> <input type="text"
								placeholder="Antrag ablehnen und Ausweichtermin vorschlagen"
								class="form-control" aria-label="..." id="holiday_change_text">
						</div>

					</form>

				</div>
				<!-- /modal-body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default btn-lg btn-block"
						id="btn_accept_holiday">Abschicken</button>
				</div>
				<!-- /modal-footer -->

			</div>
			<!-- /modal-content -->
		</div>
		<!-- /modal-dialog -->
	</div>
	<!-- /department_popup -->

</body>
</html>
