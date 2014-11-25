<?php
include 'model/Person.php';

class RequestHandler {

	public function __construct($request) {
		//$this->createExampleData ();
		$url = $request->url;
		$ressource;
		$id;
		if (is_numeric ( $url [count ( $url ) - 1] )) {
			$id = $url [count ( $url ) - 1];
			$ressource = $url [count ( $url ) - 2];
		} else {
			$ressource = $url [count ( $url ) - 1];
		}
		
		if ($ressource == "Person") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					$this->sendPerson ( $id );
				} else {
					$this->sendPersons ();
				}
			}
		} elseif ($ressource == "Department") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					$this->sendDepartment ( $id );
				} else {
					$this->sendDepartment ();
				}
			}
		} elseif ($ressource == "HolidayRequest") {
			if ($request->method == "GET") {
				if (isset ( $id )) {
					$this->sendHolidayRequest ( $id );
				} else {
					$this->sendHolidayRequests ();
				}
			}
		}
	}

	private function sendPersons() {
	}

	private function sendPerson($id) {
		$person = new Person ( 42, "a", "b", 2, true, 25, 1 );
		echo $person->toJSON ();
	}

	private function sendDepartments() {
	}

	private function sendDepartment($id) {
	}

	private function sendHolidayRequests() {
	}

	private function sendHolidayRequest($id) {
	}

	private function createExampleData() {
	}
}
?>