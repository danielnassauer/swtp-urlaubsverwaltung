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

	public function __construct($id, $start, $end, $person, $substitutes, $type, $comment) {
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
}
?>