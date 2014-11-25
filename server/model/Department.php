<?php
class Department {
	private $id;
	private $name;

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