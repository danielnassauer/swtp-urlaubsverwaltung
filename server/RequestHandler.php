<?php
include 'DBConn.php';
class RequestHandler {
	private $dbconn;

	public function __construct($request) {
		// URL auswerten
		$url = $request->url;
		$ressource;
		$id;
		if (is_numeric ( $url [count ( $url ) - 1] )) {
			$id = $url [count ( $url ) - 1];
			$ressource = $url [count ( $url ) - 2];
		} else {
			$ressource = $url [count ( $url ) - 1];
		}
		
		// DB-Verbindung herstellen
		$dbconn = new DBConn ();
		
		// Request auswerten
		if ($ressource == "Person") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					echo $dbconn->getPerson ( $id );
				} else {
					echo $dbconn->getPersons ();
				}
			}
		} elseif ($ressource == "Department") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					echo $dbconn->getDepartment ( $id );
				} else {
					echo $dbconn->getDepartments ();
				}
			}
		} elseif ($ressource == "HolidayRequest") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					echo $dbconn->getHolidayRequest ( $id )->toJSON();
				} else {
					echo $dbconn->getHolidayRequests ();
				}
			} elseif ($request->method == "POST") {
				if (! isset ( $id )) {
					$holReq = $request->content;
					echo $dbconn->createHolidayRequest ( $holReq ["start"], $holReq ["end"], $holReq ["person"], $holReq ["substitutes"], $holReq ["type"], $holReq ["status"], $holReq ["comment"] );
				}
			} elseif ($request->method == "PUT") {
				if (isset ( $id )) {
					$holReq = $request->content;
					$dbconn->editHolidayRequest ( new HolidayRequest($holReq ["id"],$holReq ["start"], $holReq ["end"], $holReq ["person"], $holReq ["substitutes"], $holReq ["type"], $holReq ["status"], $holReq ["comment"]));
				}
			}
		}
	}
}
?>