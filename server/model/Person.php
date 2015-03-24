<?php
require_once dirname(__FILE__).'/../../lib/json/jsonCompatibility.php';
class Person {
	private $id;
	private $forename;
	private $lastname;
	private $department;
	private $field_service;
	private $remaining_holiday;
	private $role;

	/**
	 * Repräsentiert eine Person
	 * @param id ID
	 * @param forename Vorname
	 * @param lastname Nachname
	 * @param department Abteilungs-ID
	 * @param field_service Außendienst (als boolean)
	 * @param remaining_holiday verbleibende Urlaubstage;
	 * @param role Mitarbeiter-Typ 1: Mitarbeiter, 2: Abteilungsleiter, 3: Geschäftsleitung
	 */
	public function __construct($id, $forename, $lastname, $department_id, $field_service, $remaining_holiday, $role) {
		$this->id = $id;
		$this->forename = $forename;
		$this->lastname = $lastname;
		$this->department = $department_id;
		$this->field_service = $field_service;
		$this->remaining_holiday = $remaining_holiday;
		$this->role = $role;
	}
	
	public function getID(){
		return $this->id;
	}
	
	public function getForename(){
		return $this->forename;
	}
	
	public function getLastname(){
		return $this->lastname;
	}
	
	public function getDepartment(){
		return $this->department;
	}
	
	public function getFieldservice(){
		return $this->field_service;
	}
	
	public function getRemainingHoliday(){
		return $this->remaining_holiday;
	}
	
	public function getRole(){
		return $this->role;
	}

	public function toArray() {
		$data = array();
		$data["id"]=$this->id;
		$data["forename"]=$this->forename;
		$data["lastname"]=$this->lastname;
		$data["department"]=$this->department;
		$data["field_service"]=$this->field_service;
		$data["remaining_holiday"]=$this->remaining_holiday;
		$data["role"]=$this->role;
		return $data;
	}
}
?>