<?php
require_once dirname ( __FILE__ ) . '/Session.php';
require_once dirname ( __FILE__ ) . '/../db/Persons.php';
require_once dirname ( __FILE__ ) . '/../db/HolidayRequests.php';
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
	 * Prüft, ob der aktuelle user Urlaubsanträge löschen darf.
	 * Admins dürfen Urlaubsanträge löschen.
	 */
	public static function deleteHolidayRequests() {
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
	 * Der zugehörige Abteilungsleiter darf Urlaubsanfragen akzeptieren oder ablehnen (nicht eigene Anfragen).
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
		if (self::isDepartmentManager () && self::$user->getDepartment () == $requester->getDepartment ()) {
			// nicht eigene Anfragen
			if (self::$user->getID () != $holidayRequest->getPerson ()) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Ermittelt, ob die angemeldete Person ein Abteilungsleiter ist, oder die
	 * Rechte eines Abteilungsleiters besitzt, das die Person zurzeit die
	 * Vertretung für einen Abteilungsleiter übernimmt.
	 *
	 * @return true, wenn die angemeldete Person Abteilungsleiterrechte hat.
	 */
	private static function isDepartmentManager() {
		if (self::$user->getRole () == 2) {
			return true;
		}
		foreach ( HolidayRequests::getRequests () as $request ) {
			$substitutes = $request->getSubstitutes ();
			if (array_key_exists ( self::$user->getID (), $substitutes )) { // angemeldeter user ist vertretung
				if ($substitutes [self::$user->getID ()] == 2) { // und hat zugestimmt
					if (self::timeInRange ( gmmktime (), $request->getStart (), $request->getEnd () )) { // Vertretung findet heute statt
						$requester = Persons::getPerson ( $request->getPerson () );
						if ($requester->getRole () == 2) { // Abteilungsleiter wird vertreten
							return true;
						}
					}
				}
			}
		}
		
		return false;
	}

	private static function timeInRange($timestamp, $start, $end) {
		for($day = $start; $day <= $end; $day += 24 * 60 * 60) {
			if (self::isSameDay ( $timestamp, $day )) {
				return true;
			}
		}
		return false;
	}

	private static function isSameDay($timestamp1, $timestamp2) {
		return strcmp ( date ( "d.m.Y", $timestamp1 ), date ( "d.m.Y", $timestamp2 ) ) == 0;
	}
}

UserRights::init ();
?>