<?php
require_once dirname ( __FILE__ ) . '/server/model/Holiday.php';
require_once dirname ( __FILE__ ) . '/server/db/Holidays.php';
 
class HolidayCalculator{

	/**
	 * Berechnet die Anzahl der verbrauchten Urlaubstage.
	 * @param start Start-Datum des Urlaubs als unix-timestamp
	 * @param end End-Datum des Urlaubs als unix-timestamp
	 * @return Anzahl der verbrauchten Urlaubstage
	 */
	public static function calculateHolidays($start, $end){
		Holidays::getHolidays(); //feiertage
		return 5;
	}
}

?>