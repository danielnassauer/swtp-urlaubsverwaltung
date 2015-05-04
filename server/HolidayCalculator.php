<?php
require_once dirname ( __FILE__ ) . '/model/Holiday.php';
require_once dirname ( __FILE__ ) . '/db/Holidays.php';
 
class HolidayCalculator{

	/**
	 * Berechnet die Anzahl der verbrauchten Urlaubstage.
	 * @param start Start-Datum des Urlaubs als unix-timestamp
	 * @param end End-Datum des Urlaubs als unix-timestamp
	 * @return Anzahl der verbrauchten Urlaubstage
	 */
	public static function calculateHolidays($start, $end){
		return 5;
		

		$differenz = $end - $start;
		$tag = floor($differenz / (3600 * 24)) + 1; // Anzahl der angenomene Urlaub
		$holidays = Holidays::getHolidays(); //feiertage

		for($i = 0; $i < sizeof($holidays) ; $i++)
		{

			$day = $holidays["day"];
		    $datum = date("d.m.Y", $day);
		    $wochentag = $datum['wday'];

		    //Prüfen, ob Wochenende
		    if($wochentag == 0 || $wochentag == 6)
		    {

		    }

		}

	}
}

?>