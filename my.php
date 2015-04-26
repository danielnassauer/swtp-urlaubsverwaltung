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
<link rel="stylesheet/less" type="text/css" href="lib/datepicker/less/datepicker.less" />

<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>
<script src="lib/fullcalendar/lib/moment.min.js"></script>
<script src="lib/fullcalendar/fullcalendar.js"></script>

<script src="lib/datepicker/js/bootstrap-datepicker.js"></script>
<script src="datepicker.css"></script>

<script src="js/client.js"></script>
<script src="js/model.js"></script>

<script type="text/javascript">
	var user = getPerson(80);
</script>

<script type="text/javascript">
	var calendar;

	function restUrlaub(){
		var rows=0;
		rows = user.remaining_holiday;
			$("#resttage").html(rows);
		//	document.getElementById("resttage").innerHTML = rows;
	}
	function onHolidayRequestCreation() {
		$("#popup").modal("hide");
		var start = new Date(calendar.selected_start).getTime() / 1000;$('#sandbox-container .input-daterange').datepicker({
});
		var end = new Date(calendar.selected_end).getTime() / 1000;
		var substitutes = $.parseJSON($("#text_substitutes").val());

		if ($("#radio_ua").prop("checked")) {
			var type = "Urlaub"
		} else if ($("#radio_fa").prop("checked")) {
			var type = "Freizeit"
		} else {
			var type = $("#text_su").val();
		}

		createHolidayRequest(start, end, user.id, substitutes, type);
	}

	function onHolidayRequestSelection(start, end, allDay) {
		$('#popup').modal("show");
		calendar.fullCalendar('unselect');
		calendar.selected_start = start;
		calendar.selected_end = end;
	}

	function onHolidayRequestEdit(id) {
		$('#changeHoliday').modal("show");
		req = getHolidayRequest(id);
		console.log(id);
		
	}
	
	function changeHoliday(id){
		$('#changeHoliday').modal("hide");
		console.log(id);
		var radio = $("#UA_storno").prop("checked");
		if(radio){
			$('#deleteHoliday').modal("show");
			console.log("löschen");
			}
		else{
			$('#editHoliday').modal("show");
			
			console.log("ändern");
			}
		$("#UA_storno").attr('checked' , false);
		$("#UA_change").attr('checked' , false);
		}

	function showOwnHolidayRequests() {
		var requests = getHolidayRequests();
		var own_requests = [];
		for (var i = 0; i < requests.length; i++) {
			var request = requests[i];
			if (request.person == user.id) {
				own_requests.push(request);
			}
		}

		var rows = "";
		for (var i = 0; i < own_requests.length; i++) {
			var request = own_requests[i];
			var start = new Date(request.start * 1000);
			var end = new Date(request.end * 1000);
			rows += "<tr onclick='onHolidayRequestEdit(" + request.id
					+ ")'><td>" + request.type + "</td><td>" + start.getDate()
					+ "." + (start.getMonth() + 1) + "." + start.getFullYear()
					+ "</td><td>" + end.getDate() + "." + (end.getMonth() + 1)
					+ "." + end.getFullYear() + "</td><td>"
					+ JSON.stringify(request.substitutes) + "</td><td>"
					+ request.status + "</td></tr>";
		}

		$("#holidayrequests_list").html(rows);
	}

	$(document).ready(function() {
		showOwnHolidayRequests();
		restUrlaub();

		calendar = $('#calendar').fullCalendar({
			lang : 'de', //geht eh nicht(-: alles in der fullcalendar.min.js geändert!
			selectable : true,
			editable : true,
			header : {
				left : 'prev,next',
				center : 'title',
				right : 'year,month,agendaWeek'
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
	});
</script>
</head>

<body>
	<div id="popup" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaub beantragen</h4>
				</div> <!-- /modal-header -->
				<div class="modal-body">
					
					<form role="form">
		
							<div class="radio">
								<label> <input type="radio" name="optradio"
									id="radio_ua"> Urlaubsantrag
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
							</span> <input type="text" placeholder="Antrag für Sonderurlaub wegen"
								class="form-control" aria-label="..." id="text_su">
						</div>
						<!-- /input-group -->

					
					</form>


						<div class="input-group">
							<span class="input-group-addon" id="sizing-addon2">Die
								Vertretung übernimmt</span> <input type="text" class="form-control"
								placeholder="Vertretung" aria-describedby="sizing-addon2"
								id="text_substitutes">
						</div>

			</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-lg btn-block"
							onclick="onHolidayRequestCreation()">Abschicken</button>
					</div>
				</div> <!-- /modal-content -->
			</div> <!-- /modal-dialog -->
		</div> <!-- /popup -->




	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<span class="navbar-brand">Urlaubsverwaltung</span>
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
				<span style="margin-left: 6em" class="navbar-brand">Restliche Urlaubstage:	
				<span id='resttage' style="margin-left: 1em" class="badge alert-danger"></span></span>
			</div>
		</div>
	</nav>

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
				<table class="table table-hover">
					<tr>
						<th>Art</th>
						<th>Start</th>
						<th>Ende</th>
						<th>Vertretungen</th>
						<th>Status</th>
					</tr>
					<tbody id="holidayrequests_list">

					</tbody>
				</table>
			</div>
		</div> <!-- /navbar -->
		
		
	<div id="changeHoliday"class="modal fade">
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
								<label> <input type="radio" name="optradio" id="UA_change">
								Urlaubsantrag ändern
								</label>
							</div>
							
							<div class="radio">
								<label> <input type="radio" name="optradio"
									id="UA_storno"> Urlaubsantrag stornieren
								</label>
							</div>

					</form>

				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-lg btn-block"
							id="btn_edt_holiday" onclick="changeHoliday(req.id)">Weiter</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /changeHoliday -->
		
		
		<div id="deleteHoliday"class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4>Urlaubsanträge löschen</h4>
				</div> <!-- /modal-header -->
				<div class="modal-body">

					<p>Möchten Sie ihren Urlaubsantrag wirklich stornieren?</p>

				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Nein, doch nicht</button>
						<button type="button" class="btn btn-primary" >Urlaubsantrag stornieren</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /deleteHoliday -->
	
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
						<p>Hier sollen die Mitarbeiter ihren Urlaub verschieben können</p>
						<p>HHier kann evtl ein Datepicker hin oder sowas</p>
						<p>Die Mitarbeiter sollen auf jeden Fall nicht per Hand ihren neuen Urlaub eintragen</p>

				</div> <!-- /modal-body -->
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" >Urlaubsantrag ändern</button>
					</div> <!-- /modal-footer -->
				
			</div><!-- /modal-content -->
		</div><!-- /modal-dialog -->
	</div><!-- /changeHoliday -->
		
</body>

</html>