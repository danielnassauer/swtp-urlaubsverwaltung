<?php
class HolidayRequest {
	private $id;
	private $start;
	private $end;
	private $person;
	private $substitutes;
	private $type;
	private $status;
	private $comment;

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