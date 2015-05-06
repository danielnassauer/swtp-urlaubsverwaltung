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
		}
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
					<li><a href="index.php"><span class="ion-grid">
								Übersicht</a></li>
					<li><a href="my.php"><span class="ion-home"></span> Mein Kalender</a></li>
					<li><a href="requests.php"><span class="ion-clipboard"> Anfragen</a></li>
					<li class="active"><a href="help.php"><span class="ion-help-circled"> Hilfe</a></li>
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
		<!-- Tabelle für Mitarbeiter -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title" style="color: #aaaaaa">Hilfe:</h3>
			</div>
			<div class="panel-body">					
				test22222222222222222222
				<!-- /Tabelle -->
			</div>
			<!-- /Panel-body -->
		</div>
		<!-- /panel -->
	</div>
	<!-- /container -->
	</div>

</body>
</html>
