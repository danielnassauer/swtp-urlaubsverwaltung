<?php
require_once dirname ( __FILE__ ) . '/model/Holiday.php';
require_once dirname ( __FILE__ ) . '/db/Holidays.php';
class HolidayCalculator {

	/**
	 * Berechnet die Anzahl der verbrauchten Urlaubstage.
	 *
	 * @param
	 *        	start Start-Datum des Urlaubs als unix-timestamp
	 * @param
	 *        	end End-Datum des Urlaubs als unix-timestamp
	 * @return Anzahl der verbrauchten Urlaubstage
	 */
	public static function calculateHolidays($start, $end) {
		$holidays = Holidays::getHolidays (); // feiertage
		$count = 0;
		
		for($day = $start; $day <= $end; $day += 24 * 60 * 60) {
			if (! self::isHoliday ( $day, $holidays ) && ! self::isWeekend ( $day )) {
				$count ++;
			}
		}
		
		return $count;
	}

	private static function isHoliday($timestamp, $holidays) {
		foreach ( $holidays as $holiday ) {
			if (self::isSameDay ( $holiday->day, $timestamp )) {
				return true;
			}
		}
		return false;
	}

	private static function isWeekend($timestamp) {
		return Date ( 'D', $timestamp ) == 'Sat' || Date ( 'D', $timestamp ) == 'Sun';
	}

	private static function isSameDay($timestamp1, $timestamp2) {
		return strcmp ( date ( "d.m.Y", $timestamp1 ), date ( "d.m.Y", $timestamp2 ) ) == 0;
	}
}

?>