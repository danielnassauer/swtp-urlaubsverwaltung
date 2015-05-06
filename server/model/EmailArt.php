<?php

require_once dirname ( __FILE__ ) . '/../model/HolidayRequest.php';
require_once dirname ( __FILE__ ) . '/../model/Person.php';
require_once dirname ( __FILE__ ) . '/../db/Persons.php';
require_once dirname ( __FILE__ ) . '/../HolidayCalculator.php';



/**
* 
*/
class EmailArt
{
	
	

	//E-mail zum Vetreter
public static function email1($holidayRequest){	
  $sent = false;
  $requester = $holidayRequest->getPerson();
  $subject = "Vertretung";
  $header   = array();
  $header[] = "MIME-Version: 1.0";
  $header[] = "Content-type: text/plain; charset=UTF-8";
  $header[] =  "From: ".$requester->getForename()." ".$requester->getLastname()." <".Persons::getEmail($holidayRequest->getID()).">\n"; 


	foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted == 1)
    {
    	$id = $i;

      $to = Persons::getEmail($id);

      $message = "Sehr Geehrte(r) Frau/Herr ".Persons::getPerson($id)->getLastname().",\n\n";
      $message .= "ich möchte Urlaub von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd())." nehmen und wollte wissen,"
               ."ob Sie mich in dieser Zeitpunkt vertreten könnten.\n\n"
               ."Mit freundlichen Grüßen\n"
               .$requester->getForename()." ".$requester->getLastname();

      $sent = mail($to,$subject,$message,implode("\r\n",$header));

    }
  }
      return $sent;
}

	//E-mail zum Abteilungsleiter
public static function email2($holidayRequest,$idVonLeiter){
    $sent = false;
    $requester = $holidayRequest->getPerson();
    $subject = $holidayRequest->getType(); 
    $header   = array();
    $header[] = "MIME-Version: 1.0";
    $header[] = "Content-type: text/plain; charset=UTF-8";
    $header[] =  "From: ".$requester->getForename()." ".$requester->getLastname()." <".Persons::getEmail($holidayRequest->getID()).">\n"; 


	foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted == 2)
    {
    	$id = $i;

    }
  }

      $to = Persons::getEmail($idVonLeiter);

      $message  = "Sehr Geehrte(r) Frau/Herr ".Persons::getPerson($idVonLeiter)->getLastname().",\n\n";
      $message .= "hiermit beantrage ich ".HolidayCalculator::calculateHolidays($holidayRequest->getStart(),$holidayRequest->getEnd())." Urlaubstage im Zeitraum von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd())." nehmen.Die Vertretung Übernimt  Frau/Herr "
               .Persons::getPerson($id)->getLastname().".\nBitte bestätigen Sie mir die Urlaubstage.\n\n"
               ."Mit freundlichen Grüßen\n"
               .$holidayRequest->getPerson()->getForename()." ".$holidayRequest->getPerson()->getLastname();

      $sent = mail($to,$subject,$message,implode("\r\n",$header));

    
  
	   return $sent;
}

	//E-mail zum Administrator
  // $id  ID der Administrator
public static function email3($holidayRequest,$id)
{

  
  $requester = $holidayRequest->getPerson();
  $subject = "Erinnerung";
  $header   = array();
  $header[] = "MIME-Version: 1.0";
  $header[] = "Content-type: text/plain; charset=UTF-8";
  $header[] =  "From: ".$requester->getForename()." ".$requester->getLastname()." <".Persons::getEmail($holidayRequest->getID()).">\n"; 
 

	$to = Persons::getEmail($id);
	$message = "Sehr Geehrte(r) Frau/Herr ".Persons::getPerson($id)->getLastname().",\n\n";
	$message .= "Sie haben meinen Urlaubsantrag noch nicht bearbeitet.Vielen Dank im Voraus.\n\n"
                ."Mit freundlichen Grüßen\n"
                .$holidayRequest->getPerson()->getForename()." ".$holidayRequest->getPerson()->getLastname();

   return mail($to,$subject,$message,implode("\r\n",$header));

}

}

?>