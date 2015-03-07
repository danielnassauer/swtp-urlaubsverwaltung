<?php
include 'server/db/conf.php';

function create_holidayrequests_table() {
	// Create connection
	$conn = new mysqli ( $db_servername, $db_username, $db_password, $db_name );
	// Check connection
	if ($conn->connect_error) {
		die ( "Connection failed: " . $conn->connect_error );
	}
	
	// sql to create table
	$sql = "CREATE TABLE HolidayRequests (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
start VARCHAR(30) NOT NULL,
end VARCHAR(30) NOT NULL
)";
	
	if ($conn->query ( $sql ) === TRUE) {
		echo "Table MyGuests created successfully";
	} else {
		echo "Error creating table: " . $conn->error;
	}
	
	$conn->close ();
}

function getDbConf() {
	$db_conf_file = file_get_contents ( "server/db/conf.php" );
	$db_conf_file = str_replace ( "\r\n", "", $db_conf_file );
	$db_conf_file = str_replace ( "\n", "", $db_conf_file );
	$db_conf_file = str_replace ( "<?php", "", $db_conf_file );
	$db_conf_file = str_replace ( "?>", "", $db_conf_file );
	$db_conf_file = str_replace ( " ", "", $db_conf_file );
	$db_conf_file = str_replace ( "$", "", $db_conf_file );
	$db_conf_file = str_replace ( "=", "", $db_conf_file );
	$conf = array ();
	foreach ( explode ( ';', $db_conf_file ) as $value ) {
		if (strlen ( $value ) > 0) {
			$data = explode ( '"', $value );
			$conf [$data [0]] = $data [1];
		}
	}
	return $conf;
}

if (isset ( $_GET ['create_holidayrequests_table'] )) {
	create_holidayrequests_table();
}

$conf = getDbConf ();
?>
<!DOCTYPE html>
<html>
<meta charset="utf-8">

<head>
<title>Urlaubsverwaltung</title>

<link rel="stylesheet" href="lib/ionicons/css/ionicons.min.css" />
<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css" />

<script type="text/javascript" src="lib/jquery/jquery-1.11.1.min.js"></script>
<script src="lib/bootstrap/js/bootstrap.min.js"></script>

</head>

<body>
	<div class="container">

		<!-- DB ZUGANGSDATEN -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">DB Zugangsdaten</h3>
			</div>
			<div class="panel-body">

				<form class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label">servername</label>
						<div class="col-sm-10">
							<input class="form-control" id="dbconf_servername"
								value="<?php echo $conf["db_servername"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">username</label>
						<div class="col-sm-10">
							<input class="form-control" id="dbconf_username"
								value="<?php echo $conf["db_username"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">password</label>
						<div class="col-sm-10">
							<input class="form-control" id="dbconf_password"
								value="<?php echo $conf["db_password"];?>">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">db</label>
						<div class="col-sm-10">
							<input class="form-control" id="dbconf_db"
								value="<?php echo $conf["db_name"];?>">
						</div>
					</div>
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">Speichern</button>
					</div>


				</form>
			</div>

		</div>


		<!-- HOLIDAYREQUESTS DB -->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">HolidayRequests DB</h3>
			</div>
			<div class="panel-body">
				<button type="submit" class="btn btn-default"
					name="create_holidayrequests_table">Tabelle erstellen</button>
			</div>
		</div>

	</div>

</body>

</html>