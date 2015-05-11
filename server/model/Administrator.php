<?php
require_once dirname ( __FILE__ ) . '/../db/HolidayRequests.php';
require_once dirname ( __FILE__ ) . '/EmailArt.php';



foreach (HolidayRequests::getRequests() as $key => $holidayRequest) //Alle Anträge durchlaufen
		 { 

	       if($holidayRequest->getStatus() == 2) //falls ein Antrag  wartend is
	        {
               EmailArt::email3($holidayRequest); //sende E-mail zu Administrator
     
	        }
	     }

  	

?>

