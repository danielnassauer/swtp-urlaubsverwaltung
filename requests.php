
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
	
	
	
	function restUrlaub(){
	var rows=0;
	rows = user.remaining_holiday;
		$("#resttage").html(rows);
	}
	
	
	function onHolidayRequestEdit(id) {
		$("#btn_accept_substitute").attr("onclick",
				"onSubstituteFinished(" + id + ")")
		$("#sub_popup").modal("show");
		

	}
	


	function onSubstituteFinished(id) {
		$("#sub_popup").modal("hide");
		var accepted = $("#sub_accept").prop("checked");
		var request = getHolidayRequest(id);
		var subs = request.substitutes;
		subs[user.id] = accepted;
		editHolidayRequest(id, request.start, request.end, subs,
				request.status, request.comment);
		$("#sub_accept").attr('checked' , false);
		$("#sub_decline").attr('checked' , false);
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
		if($("#holiday_accept").prop("checked")){
			var accepted = 1;
			editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, request.comment);
		} else if($("#holiday_decline").prop("checked")){
			var accepted = 3;
			editHolidayRequest(id, request.start, request.end, request.substitutes, accepted, request.cmment);
			}else {}

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
			
			rows += "<tr onclick='onDepartmentHolidayRequestEdit(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + request.person
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + request.substitute
						+ "</td><td>" + request.status + "</td></tr>";
		
		}	
		
		$("#department_request_list").html(rows);

		
		}

	function updateMySubstituteTable(){
		
		var requests = getHolidayRequests();
		var Persons = getPersons();
		
		var filter_my_subs = {"filter": isSubstituteFilter, "attachment":user.id}
		
		requests = filterHolidayRequests(requests,[filter_my_subs]);
		
		var rows = "";
		for (var i = 0; i < requests.length; i++){
			
			var request;
			request = requests[i];
			var start = new Date(request.start * 1000);
			var end = new Date(request.end * 1000);
			
			rows += "<tr onclick='onHolidayRequestEdit(" + request.id
						+ ")'><td>" + request.type + "</td><td>" + request.person
						+ "</td><td>" + start.getDate() + "."
						+ (start.getMonth() + 1) + "." + start.getFullYear()
						+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
						+ "." + end.getFullYear() + "</td><td>" + request.substitute
						+ "</td><td>" + request.status + "</td></tr>";
			
			}
			
			$("#request_list").html(rows);
			
		
		}


	function showOwnHolidayRequests() {
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

	}

	$(document).ready(function() {
		restUrlaub();
		updateMySubstituteTable();
		updateDepartmentTable();
	})
</script>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<span class="navbar-brand">Urlaubsverwaltung</span>
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
	
	<div> <!-- Tabelle für Mitarbeiter -->
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
	</div>
	
	<div> <!-- Tabelle für Abteilungsleiter -->
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
	</div>
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
									id="sub_accept"> Vertretung zustimmen
								</label>
							</div>


						<div class="radio">
							<label> <input type="radio" name="optradio" id="sub_decline">
								Vertretung ablehnen
							</label>
						</div>

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
									id="holiday_accept"> Einverstanden wie beantragt
								</label>
							</div>


						<div class="radio">
							<label> <input type="radio" name="optradio" id="holiday_decline">
								Abgelehnt wegen:
							</label>
						</div>
						
						<div class="radio">
							<label> <input type="radio" name="optradio" id="holiday_change">
								Geändert wie folgt wegen:
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


</body>
</html>
