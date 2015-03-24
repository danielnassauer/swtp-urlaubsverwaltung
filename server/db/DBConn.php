<?php
require_once 'model/Person.php';
require_once 'model/Department.php';
require_once 'model/HolidayRequest.php';
require_once dirname(__FILE__).'/../../lib/json/jsonCompatibility.php';
class DBConn {

	public function __construct() {
		// create example data
		return 		
		$requests = "[";
		for($i = 0; $i < 3; $i ++) {
			$r = new HolidayRequest ( $i, "$i", "$i", $i, array (
					$i + 2,
					$i + 1 
			), 1, 1, "kommentar" );
			$requests .= $r->toJSON () . ",";
		}
		$r = new HolidayRequest ( 3, "3", "3", 3, array (
				5,
				4 
		), 1, 1, "kommentar" );
		$requests .= $r->toJSON () . "]";
		file_put_contents ( "exampleRequests.json", $requests );
	}

	public function getPersons() {
		return file_get_contents ( "db/examplePersons.json" );
	}

	public function getPerson($id) {
		$persons = json_decode ( file_get_contents ( "examplePersons.json" ), $assoc = true );
		foreach ( $persons as $person ) {
			if ($person ["id"] == $id) {
				echo json_encode ( $person );
				return;
			}
		}
	}

	public function getHolidayRequests() {
		return file_get_contents ( "exampleRequests.json" );
	}

	public function getHolidayRequest($id) {
		$requests = json_decode ( file_get_contents ( "db/exampleRequests.json" ), $assoc = true );
		foreach ( $requests as $holReq ) {
			if ($holReq ["id"] == $id) {
				return new HolidayRequest ( $holReq ["id"], $holReq ["start"], $holReq ["end"], $holReq ["person"], $holReq ["substitutes"], $holReq ["type"], $holReq ["status"], $holReq ["comment"] );
			}
		}
	}

	public function editHolidayRequest($holidayRequest) {
		$r = $this->getHolidayRequest ( $holidayRequest->getID () );
		$r->edit ( $holidayRequest );
		
		$requests = json_decode ( file_get_contents ( "exampleRequests.json" ), $assoc = true );
		for($i = 0; $i < count ( $requests ); $i ++) {
			if ($requests [$i] ["id"] == $r->getID ()) {
				$requests [$i] ["start"] = $r->getStart ();
				$requests [$i] ["end"] = $r->getEnd ();
				$requests [$i] ["person"] = $r->getPerson ();
				$requests [$i] ["substitutes"] = $r->getSubstitutes ();
				$requests [$i] ["type"] = $r->getType ();
				$requests [$i] ["status"] = $r->getStatus ();
				$requests [$i] ["comment"] = $r->getComment ();
				break;
			}
		}
		file_put_contents ( "exampleRequests.json", json_encode ( $requests ) );
	}

	public function createHolidayRequest($start, $end, $person, $substitutes, $type, $status, $comment) {
		$requests = json_decode ( file_get_contents ( "exampleRequests.json" ), $assoc = true );
		$id = count ( $requests );
		$holReq = new HolidayRequest ( $id, $start, $end, $person, $substitutes, $type, $status, $comment );
		$requests [$id] = json_decode ( $holReq->toJSON (), $assoc = true );
		file_put_contents ( "exampleRequests.json", json_encode ( $requests ) );
		return $holReq->toJSON ();
	}
}
?>