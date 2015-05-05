<?php
require_once dirname ( __FILE__ ) . '/Session.php';
require_once dirname ( __FILE__ ) . '/../db/Persons.php';
require_once dirname ( __FILE__ ) . '/../model/HolidayRequest.php';
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

	/**
	 * Prüft, ob der aktuelle user Urlaubsanträge erstellen darf.
	 * Admins und der eigene user dürfen Urlaubsanträge erstellen.
	 *
	 * @param
	 *        	person_id ID der Person, der der Urlaubsantrag gehört.
	 */
	public static function createHolidayRequest($person_id) {
		return self::$user->isAdmin () || $person_id == self::$user->getID ();
	}

	/**
	 * Prüft, ob der aktuelle user das Start- und Enddatum von Urlaubsanträgen ändern darf.
	 * Admins und der eigene user dürfen Start- und Enddatum ändern.
	 *
	 * @param
	 *        	person_id ID der Person, der der Urlaubsantrag gehört.
	 */
	public static function editStartAndEnd($person_id) {
		return self::$user->isAdmin () || $person_id == self::$user->getID ();
	}

	/**
	 * Prüft, ob der aktuelle user die Substitutes ändern darf.
	 * Admins dürfen Substitutes ändern.
	 * Ein Substitute darf nur seinen eigenen Status ändern.
	 * Ein Substitute darf sich selbst mit einem anderen Substitute ersetzen.
	 *
	 * @param
	 *        	old_subs Unveränderte Substitutes
	 * @param
	 *        	new_subs neue Substitutes
	 */
	public static function editSubstitutes($old_subs, $new_subs) {
		// Admin
		if (self::$user->isAdmin ()) {
			return true;
		}
		
		// Ein Substitute darf nur seinen eigenen Status ändern.
		if (count ( $old_subs ) == count ( $new_subs )) {
			$ok = true;
			foreach ( $old_subs as $subs => $status ) {
				if (! array_key_exists ( $subs, $new_subs )) {
					$ok = false;
					break;
				}
				if ($new_subs [$subs] != $old_subs [$subs] && $subs != self::$user->getID ()) {
					$ok = false;
					break;
				}
			}
			if ($ok) {
				return true;
			}
		}
		
		// Ein Substitute darf sich selbst mit einem anderen Substitute ersetzen.
		if (count ( $old_subs ) == count ( $new_subs ) && array_key_exists ( self::$user->getID (), $old_subs ) && ! array_key_exists ( self::$user->getID (), $new_subs )) {
			$ok = true;
			$count = 0;
			foreach ( $old_subs as $subs => $status ) {
				if (! array_key_exists ( $subs, $new_subs )) {
					$count ++;
					if ($count > 1) {
						$ok = false;
						break;
					}
				}
			}
			if ($ok) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Prüft, ob der aktuelle user die Urlaubsanfragen stornieren darf.
	 * Admins dürfen Urlaubsanfragen stornieren.
	 * Antragsteller darf eigene Urlaubsanfragen stornieren.
	 */
	public static function cancel($holidayRequest) {
		// Admin
		if (self::$user->isAdmin ()) {
			return true;
		}
		
		// Antragsteller
		if (self::$user->getID () == $holidayRequest->getPerson ()) {
			return true;
		}
		
		return false;
	}

	/**
	 * Prüft, ob der aktuelle user die Urlaubsanfragen akzeptieren oder ablehnen darf.
	 * Admins dürfen Urlaubsanfragen akzeptieren oder ablehnen.
	 * Geschäftsleiter dürfen Urlaubsanfragen akzeptieren oder ablehnen.
	 * Der zugehörige Abteilungsleiter darf Urlaubsanfragen akzeptieren oder ablehnen.
	 */
	public static function acceptOrDeclineRequest($holidayRequest) {
		// Admin
		if (self::$user->isAdmin ()) {
			return true;
		}
		
		// Geschäftsleiter
		if (self::$user->getRole () == 3) {
			return true;
		}
		
		// Abteilungsleiter
		$requester = Persons::getPerson ( $holidayRequest->getPerson () );
		if (self::$user->getRole () == 2 && self::$user->getDepartment () == $requester->getDepartment ()) {
			return true;
		}
		
		return false;
	}
}

UserRights::init ();
?>