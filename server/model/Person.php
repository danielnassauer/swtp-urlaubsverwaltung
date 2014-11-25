<?php
class Person {
	private $id;
	private $forename;
	private $surname;
	private $department;
	private $field_service;
	private $remaining_holiday;
	private $role;

	public function __construct($id, $forename, $surname, $department_id, $field_service, $remaining_holiday, $role) {
		$this->id = $id;
		$this->forename = $forename;
		$this->surname = $surname;
		$this->department = $department_id;
		$this->field_service = $field_service;
		$this->remaining_holiday = $remaining_holiday;
		$this->role = $role;
	}

	public function toJSON() {
		$data = array();
		$data["id"]=$this->id;
		$data["forename"]=$this->forename;
		$data["surname"]=$this->surname;
		$data["department"]=$this->department;
		$data["field_service"]=$this->field_service;
		$data["remaining_holiday"]=$this->remaining_holiday;
		$data["role"]=$this->role;
		return json_encode($data);
	}
}
?>