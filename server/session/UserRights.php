<?php
require_once dirname ( __FILE__ ) . '/Session.php';
require_once dirname ( __FILE__ ) . '/../db/Persons.php';
class UserRights {
	private static $user = null;

	public static function init() {
		self::$user = Persons::getPerson ( Session::getUserID () );
	}

	/**
	 * Prüft, ob der aktuelle user Tabellen löschen darf.
	 * Admins dürfen tabellen löschen.
	 */
	public static function deleteTables() {
		return self::$user->isAdmin ();
	}

	/**
	 * Prüft, ob der aktuelle user andere user zum Admin ernennen darf.
	 * Admins dürfen andere zu Admins ernennen.
	 */
	public static function editIsAdmin() {
		return self::$user->isAdmin ();
	}

	/**
	 * Prüft, ob der aktuelle user die Rolle anderer user ändern darf.
	 * Admins dürfen Rollen ändern.
	 */
	public static function editRole() {
		return self::$user->isAdmin ();
	}

	/**
	 * Prüft, ob der aktuelle user die Verbleibenden Urlaubstage anderer user ändern darf.
	 * Admins dürfen Verbleibende Urlaubstage ändern.
	 */
	public static function editRemainingHolidays() {
		return self::$user->isAdmin ();
	}

	/**
	 * Prüft, ob der aktuelle user den Außendienst anderer user ändern darf.
	 * Admins und der eigene user dürfen Außendienst ändern.
	 * 
	 * @param
	 *        	person_id ID der Person, bei der der Außendienst geändert werden soll.
	 */
	public static function editFieldService($person_id) {
		return self::$user->isAdmin () || $person_id == self::$user->getID ();
	}
}

UserRights::init ();
?>