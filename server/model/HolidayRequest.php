<?php
require_once dirname ( __FILE__ ) . '/../../lib/json/jsonCompatibility.php';
class HolidayRequest {
	public $id;
	public $start;
	public $end;
	public $person;
	public $substitutes;
	public $type;
	public $status;
	public $comment;

	/**
	 * Repräsentiert einen Urlaubsantrag
	 *
	 * @param
	 *        	id ID
	 * @param
	 *        	start Start-Datum (Unix-Timestamp)
	 * @param
	 *        	end End-Datum (Unix-Timestamp)
	 * @param
	 *        	person Antragsteller
	 * @param
	 *        	substitutes Dictionary mit IDs der Vertretungen als Key und einem Integer als Wert (1: wartend, 2: angenommen, 3: abgelehnt).
	 * @param
	 *        	type Art des Urlaubsantrags: "Urlaub", "Freizeit", oder eine Beschreibung für einen Sonderurlaub
	 * @param
	 *        	status Status des Urlaubsantrags 1: angenommen, 2: wartend, 3: abgelehnt
	 * @param
	 *        	comment Kommentar bei Ablehnung
	 */
	public function __construct($id, $start, $end, $person, $substitutes, $type, $status, $comment) {
		$this->id = $id;
		$this->start = $start;
		$this->end = $end;
		$this->person = $person;
		$this->substitutes = $substitutes;
		$this->type = $type;
		$this->status = $status;
		$this->comment = $comment;
	}

	public function toJSON() {
		$data = array ();
		$data ["id"] = $this->id;
		$data ["start"] = $this->start;
		$data ["end"] = $this->end;
		$data ["person"] = $this->person;
		$data ["substitutes"] = $this->substitutes;
		$data ["type"] = $this->type;
		$data ["status"] = $this->status;
		$data ["comment"] = $this->comment;
		return json_encode ( $data );
	}

	public function edit($holidayRequest) {
		$this->start = $holidayRequest->start;
		$this->end = $holidayRequest->end;
		$this->person = $holidayRequest->person;
		$this->substitutes = $holidayRequest->substitutes;
		$this->type = $holidayRequest->type;
		$this->status = $holidayRequest->status;
		$this->comment = $holidayRequest->comment;
	}

	public function toArray() {
		$data = array ();
		$data ["id"] = $this->id;
		$data ["start"] = $this->start;
		$data ["end"] = $this->end;
		$data ["person"] = $this->person;
		$data ["substitutes"] = $this->substitutes;
		$data ["type"] = $this->type;
		$data ["status"] = $this->status;
		$data ["comment"] = $this->comment;
		return $data;
	}

	public function getID() {
		return $this->id;
	}

	public function getStart() {
		return $this->start;
	}

	public function getEnd() {
		return $this->end;
	}

	public function getPerson() {
		return $this->person;
	}

	public function getSubstitutes() {
		return $this->substitutes;
	}

	public function getType() {
		return $this->type;
	}

	public function getStatus() {
		return $this->status;
	}

	public function getComment() {
		return $this->comment;
	}
}
?>