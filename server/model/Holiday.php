<?php
require_once dirname ( __FILE__ ) . '/../../lib/json/jsonCompatibility.php';
class Holiday {
	public $name;
	public $day;

	/**
	 * Repräsentiert einen Feiertag
	 *
	 * @param
	 *        	name Name des Feiertags
	 * @param
	 *        	day Datum des Feiertags (Unix-Timestamp)
	 */
	public function __construct($name, $day) {
		$this->name = $name;
		$this->day = $day;
	}

	public function toJSON() {
		$data = array ();
		$data ["name"] = $this->name;
		$data ["day"] = $this->day;
		return json_encode ( $data );
	}

	public function toArray() {
		$data = array ();
		$data ["name"] = $this->name;
		$data ["day"] = $this->day;
		return $data;
	}
}
?>