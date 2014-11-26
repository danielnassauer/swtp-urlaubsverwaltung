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
					echo $dbconn->getHolidayRequest ( $id );
				} else {
					echo $dbconn->getHolidayRequests ();
				}
			} elseif ($request->method == "POST") {
				if (! isset ( $id )) {
					echo $dbconn->createHolidayRequest ( $holidayRequest );
				}
			} elseif ($request->method == "PUT") {
				if (isset ( $id )) {
					$r = $request->content;
					$dbconn->createHolidayRequest ( $r->getStart (), $r->getEnd (), $r->getPerson (), $r->getSubstitutes (), $r->getType (), $r->getStatus (), $r->getComment () );
				}
			}
		}
	}
}
?>