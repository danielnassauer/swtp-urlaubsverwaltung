<?php
class Department {
	private $id;
	private $name;

	/**
	 * Repräsentiert eine Abteilung
	 * @param id ID
	 * @param name Name der Abteilung
	 */
	public function __construct($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}

	public function toJSON() {
		$data = array ();
		$data ["id"] = $this->id;
		$data ["name"] = $this->name;		
		return json_encode ( $data );
	}
}
?>