<?php
require_once dirname ( __FILE__ ) . '/db/HolidayRequests.php';
require_once dirname ( __FILE__ ) . '/db/Persons.php';
require_once dirname ( __FILE__ ) . '/db/Holidays.php';
require_once dirname ( __FILE__ ) . '/session/UserRights.php';
class RequestHandler {

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
		
		// Request auswerten
		if ($ressource == "Person") {
			
			// GET PERSON
			if ($request->method == "GET") {
				if (isset ( $id )) {
					$person = Persons::getPerson ( $id );
					echo json_encode ( $person->toArray () );
				} else {
					$persons = array ();
					foreach ( Persons::getPersons () as $person ) {
						array_push ( $persons, $person->toArray () );
					}
					echo json_encode ( $persons );
				}
			}			

			// PUT PERSON
			elseif ($request->method == "PUT") {
				if (isset ( $id )) {
					$person = $request->content;
					$orig_person = Persons::getPerson ( $id );
					
					// Rechte prüfen
					$is_admin = $orig_person->isAdmin ();
					if (UserRights::editIsAdmin ()) {
						$is_admin = $person ["is_admin"];
					}
					$role = $orig_person->getRole ();
					if (UserRights::editRole ()) {
						$role = $person ["role"];
					}
					$remaining_hol = $orig_person->getRemainingHoliday ();
					if (UserRights::editRemainingHolidays ()) {
						$remaining_hol = $person ["remaining_holiday"];
					}
					$field_service = $orig_person->getFieldservice ();
					if (UserRights::editFieldService ( $id )) {
						$field_service = $person ["field_service"];
					}
					
					Persons::editPerson ( $id, $field_service, $remaining_hol, $role, $is_admin );
				}
			}
		} elseif ($ressource == "HolidayRequest") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					$request = HolidayRequests::getRequest ( $id );
					echo $request->toJSON ();
				} else {
					$requests = array ();
					foreach ( HolidayRequests::getRequests () as $request ) {
						array_push ( $requests, $request->toArray () );
					}
					echo json_encode ( $requests );
				}
			} elseif ($request->method == "POST") {
				if (! isset ( $id )) {
					$holReq = $request->content;
					$request = HolidayRequests::createRequest ( $holReq ["start"], $holReq ["end"], $holReq ["person"], $holReq ["substitutes"], $holReq ["type"] );
					echo $request->toJSON ();
				}
			} elseif ($request->method == "PUT") {
				if (isset ( $id )) {
					$holReq = $request->content;
					HolidayRequests::editRequest ( $holReq ["id"], $holReq ["start"], $holReq ["end"], $holReq ["substitutes"], $holReq ["status"], $holReq ["comment"] );
				}
			}
		} elseif ($ressource == "Holiday") {
			if ($request->method == "GET") {
				if (! isset ( $id )) {
					$holidays = array ();
					foreach ( Holidays::getHolidays () as $holiday ) {
						array_push ( $holidays, $holiday->toArray () );
					}
					echo json_encode ( $holidays );
				}
			}
		}
	}
}
?>