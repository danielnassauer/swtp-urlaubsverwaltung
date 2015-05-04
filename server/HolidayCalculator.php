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

		$differenz = $end - $start;
		$tag = floor($differenz / (3600 * 24)) + 1; //ingesamt Tage
        $count = 0; //Zählt Samstage und Sonntage
        $feiertage = feiertageInSE($start,$end);

		  $iter = 24*60*60; // Ein Tag in Sekunden

            for($i = $start; $i <= $end; $i=$i+$iter)
            {
             if(Date('D',$i) == 'Sat' || Date('D',$i) == 'Sun')
              {
                  $count++;
              }
            }

        $urlaubstage = (25 - ($tag - ($feiertage + $count)));
        
		return $urlaubstage;
	}

	private function feiertageInSE($start,$end){

	$holidays = Holidays::getHolidays(); //feiertage
    
	$count = 0; //Anzahl der Feiertage von Montag bis Freitag

      foreach ($holidays as $value)
      { 
      	if($value["day"] == $start)
      	{
      		while ($value["day"] < $end)
      	    {
		        $datum = date("d.m.Y", $value["day"]);
		        $wochentag = $datum['wday'];
		        //Prüfen, ob Werkstag
      			if($wochentag != 0 && $wochentag != 6)
      			{
                   $count++;
      			}

      			$value = $value["day"] + 86400;
      		}
      	}

      }
              return $count;
	}
}

?>