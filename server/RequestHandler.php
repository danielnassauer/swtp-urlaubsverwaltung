<?php
require_once dirname ( __FILE__ ) . '/db/HolidayRequests.php';
require_once dirname ( __FILE__ ) . '/db/Persons.php';
require_once dirname ( __FILE__ ) . '/db/Holidays.php';
require_once dirname ( __FILE__ ) . '/model/Person.php';
require_once dirname ( __FILE__ ) . '/session/UserRights.php';
require_once dirname ( __FILE__ ) . '/HolidayCalculator.php';
require_once dirname ( __FILE__ ) . '/model/EmailArt.php';
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
		
		// PERSONS
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
					
					//escape html
					$person ["is_admin"] = $person ["is_admin"] == true;
					$person ["field_service"] = $person ["field_service"] == true;
					$person ["remaining_holiday"] = intval($person ["remaining_holiday"]);
					$person ["role"] = intval($person ["role"]);										
					
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
		}		

		// HOLIDAYREQUESTS
		elseif ($ressource == "HolidayRequest") {
			
			// GET HOLIDAYREQUEST
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
			}			

			// POST HOLIDAYREQUEST
			elseif ($request->method == "POST") {
				if (! isset ( $id )) {
					$holReq = $request->content;
					
					//escape html
					$holReq ["start"] = intval($holReq ["start"]);
					$holReq ["end"] = intval($holReq ["end"]);
					$holReq ["person"] = intval($holReq ["person"]);
					$holReq ["type"] = htmlentities($holReq ["type"], ENT_QUOTES);
					
					// Rechte prüfen
					if (UserRights::createHolidayRequest ( $holReq ["person"] )) {
						// verbrauchte urlaubstage abziehen (null ausgeben wenn verbleibende < 0)
						if ($holReq ["type"] == "Urlaub" || $holReq ["type"] == "Freizeit") {
							$used_holidays = HolidayCalculator::calculateHolidays ( $holReq ["start"], $holReq ["end"] );
							$person = Persons::getPerson ( $holReq ["person"] );
							if ($person->getRemainingHoliday () - $used_holidays < 0) {
								echo "null";
								return;
							}
							self::subRemainingHoliday ( $person, $used_holidays );
						}
						
						$request = HolidayRequests::createRequest ( $holReq ["start"], $holReq ["end"], $holReq ["person"], $holReq ["substitutes"], $holReq ["type"] );
						echo $request->toJSON ();
						EmailArt::email1 ( $request );
					}
				}
			}			

			// PUT HOLIDAYREQUEST
			elseif ($request->method == "PUT") {
				if (isset ( $id )) {
					$holReq = $request->content;
					$orig_holReq = HolidayRequests::getRequest ( $id );
					
					//escape html
					$holReq ["start"] = intval($holReq ["start"]);
					$holReq ["end"] = intval($holReq ["end"]);
					$holReq ["status"] = intval($holReq ["status"]);
					$holReq ["comment"] = htmlentities($holReq ["comment"], ENT_QUOTES);
					
					// Rechte prüfen
					$start = $orig_holReq->getStart ();
					$end = $orig_holReq->getEnd ();
					if (UserRights::editStartAndEnd ( $orig_holReq->getPerson () )) {
						if ($orig_holReq->getType () == "Urlaub" || $orig_holReq->getType () == "Freizeit") {
							// zuerst zuvor verbrauchte Urlaubstage wieder gutschreiben
							$person = Persons::getPerson ( $orig_holReq->getPerson () );
							$used_holidays = HolidayCalculator::calculateHolidays ( $start, $end );
							self::addRemainingHoliday ( $person, $used_holidays );
							// dann neu verbrauchte Urlaubstage wieder abziehen
							$person = Persons::getPerson ( $orig_holReq->getPerson () );
							$used_holidays = HolidayCalculator::calculateHolidays ( $holReq ["start"], $holReq ["end"] );
							self::subRemainingHoliday ( $person, $used_holidays );
						}
						
						$start = $holReq ["start"];
						$end = $holReq ["end"];
					}
					$substitutes = $orig_holReq->getSubstitutes ();
					if (UserRights::editSubstitutes ( $orig_holReq->getSubstitutes (), $holReq ["substitutes"] )) {
						$substitutes = $holReq ["substitutes"];
					}
					
					$status = $orig_holReq->getStatus ();
					if ($holReq ["status"] == 4) {
						if (UserRights::cancel ( $orig_holReq )) {
							if ($orig_holReq->getType () == "Urlaub" || $orig_holReq->getType () == "Freizeit") {
								// beim stornieren urlaubstage zurückbuchen
								$person = Persons::getPerson ( $orig_holReq->getPerson () );
								$used_holidays = HolidayCalculator::calculateHolidays ( $orig_holReq->getStart (), $orig_holReq->getEnd () );
								self::addRemainingHoliday ( $person, $used_holidays );
							}
							$status = 4;
						}
					} elseif ($holReq ["status"] == 1 || $holReq ["status"] == 3) {
						if (UserRights::acceptOrDeclineRequest ( $orig_holReq )) {
							$status = $holReq ["status"];
							// beim ablehnen urlaubstage zurückbuchen
							if ($status == 3) {
								if ($orig_holReq->getType () == "Urlaub" || $orig_holReq->getType () == "Freizeit") {
									$person = Persons::getPerson ( $orig_holReq->getPerson () );
									$used_holidays = HolidayCalculator::calculateHolidays ( $orig_holReq->getStart (), $orig_holReq->getEnd () );
									self::addRemainingHoliday ( $person, $used_holidays );
								}
							}
						}
					}
					
					HolidayRequests::editRequest ( $id, $start, $end, $substitutes, $status, $holReq ["comment"] );
				}
			}
			
			// DELETE HOLIDAYREQUEST
			elseif ($request->method == "DELETE") {
				if (isset ( $id )) {
					if(UserRights::deleteHolidayRequests()){
						HolidayRequests::deleteRequest($id);
					}
				}
			}
		}		

		// HOLIDAYS
		elseif ($ressource == "Holiday") {
			
			// GET HOLIDAY
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

	private static function subRemainingHoliday($person, $value) {
		Persons::editPerson ( $person->getID (), $person->getFieldservice (), $person->getRemainingHoliday () - $value, $person->getRole (), $person->isAdmin () );
	}

	private static function addRemainingHoliday($person, $value) {
		Persons::editPerson ( $person->getID (), $person->getFieldservice (), $person->getRemainingHoliday () + $value, $person->getRole (), $person->isAdmin () );
	}

}
?>