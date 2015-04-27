
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
	var user = getPerson(80);
</script>

<script type="text/javascript">
	

	function onHolidayRequestEdit(id) {
		$("#btn_accept_substitute").attr("onclick",
				"onSubstituteFinished(" + id + ")")
		$("#popup").modal("show");
		

	}

	function onSubstituteFinished(id) {
		$("#popup").modal("hide");
		var accepted = $("#radio_yes").prop("checked");
		var request = getHolidayRequest(id);
		var subs = request.substitutes;
		subs[user.id] = accepted;
		editHolidayRequest(id, request.start, request.end, subs,
				request.status, request.comment);
		$("#radio_yes").attr('checked' , false);
		$("#radio_no").attr('checked' , false);
	}
	
	function in_array(arr, val){
		for(var y = 0; y < arr.length; y++) {
			if(arr[y] == val)
			return true;
		}
    
		return false;
	}
	
	

	function showDepartmentRequests(){
		var requests = getHolidayRequests();
		var persons = getPersons();
		
		/*	Filter um Personen aus der eigenen Abteilung zu bekommen	*/
		
		var dep_persons = [];
		for (var j = 0; j < persons.length; j++){
			var filtered_persons = persons[j];
			if (filtered_persons.department == user.department){
				dep_persons.push(filtered_persons);
				}
			}
		
		
		/*  Filter um Requests aus der eigenen Abteilung zu bekommen*/
		var ids = [];
		for(x=0; x< dep_persons.length; x++){
			var abt_person = dep_persons[x];
			ids[x] = abt_person.id;
			}	
		console.log(ids);
		console.log(user.id);
		if(in_array(ids, user.id)){"KLAPPT"}
	
/*		var dep_requests = [];
		for (var i = 0; i < requests.length; i++){
			var request = requests[i];
			for (z = 0; ids.length; z++){}
			}
			
		*/
		
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
		showOwnHolidayRequests();
		showDepartmentRequests();
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
		</div>
	</nav>
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
	</div>

	<div id="popup" class="modal fade">
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
									id="radio_yes"> Vertretung zustimmen
								</label>
							</div>


						<div class="radio">
							<label> <input type="radio" name="optradio" id="radio_no">
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
	</div><!-- /popup -->


</body>
</html>
