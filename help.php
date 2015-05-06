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
	$(document).ready(function() {
		if(user == null){
			window.location = "login.php";
		}
		if(!user.is_admin){
			$("#admin_ion").addClass('hidden');
		}});
</script>
</head>

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
					<li><a href="requests.php"><span class="ion-clipboard"> Anfragen</a></li>
					<li class="active"><a href="help.php"><span
							class="ion-help-circled"> Hilfe</a></li>
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

	<div class="container">
		<h1>Übersicht</h1>
		<p>Hier sehen Sie alle bestätigten Urlaubsanfragen und
			Krankheitsfälle. Über den Filter können Sie oben eine Abteilung
			auswählen, um sich nur die Anfragen der gewählten Abteilung anzeigen
			zu lassen. Im Kalender können sie außerdem zwischen der Jahres- und
			der Monatsansicht wechseln. Krankheitsfälle und
			Außendienstmitarbeiter werden mit anderen Farben gekennzeichnet.</p>

		<h1>Mein Kalender</h1>
		<h2>Außendienst und restliche Urlaubstage</h2>
		<p>Im oberen Teil können Sie ihre restlichen Urlaubstage einsehen und
			wählen, ob Sie sich zurzeit im Außendiest befinden.</p>
		<h2>Neuen Antrag erstellen</h2>
		<p>Im Kalender können Sie einen neuen Antrag stellen. Ziehen Sie dafür
			mit der Maus vom gewünschten Start-Tag bis zum End-Tag. Im sich
			daraufhin öfnnenden Dialog, können Sie die Art Ihres Antrages und
			eventuelle Vertretungen wählen.</p>
		<h2>Gestellte Anträge einsehen, ändern und stornieren</h2>
		<p>Auf der rechten Seite sehen Sie ihre gestellten Anträge sortiert
			nach noch unbeantworteten, angenommenen und abgelehnten Anträgen.</p>
		<p>Angenommene Anträge können Sie storniern oder sich eine
			PDF-Bestätigung erzeugen lassen. Noch nicht bestätigte Anträge können
			Sie bearbeiten oder stornieren.</p>

		<h1>Anfragen</h1>
		<h2>Vertretungsanfragen bearbeiten</h2>
		<p>In der oberen Liste sehen Sie Vertretungsanfragen von anderen
			Mitarbeitern an Sie. Sie können der Vertretung zustimmen, sie
			ablehnen, oder eine andere Person als Vertretun vorschlagen.</p>
		<h2>Anfragen der Mitarbeiter beantworten</h2>
		<p>Als Abteilungsleiter oder Geschäftsleiter können Sie die Anträge
			Ihrer Mitarbeiter beantworten. Die Anträge stehen in der unteren
			Liste und können entweder angenommen, abgelehnt, oder verlegt werden.</p>
	</div>
	<!-- /container -->
	</div>

</body>
</html>
