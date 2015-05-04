<?php

require_once dirname ( __FILE__ ) . '/../model/Email.php';
require_once dirname ( __FILE__ ) . '/../model/HolidayRequest.php';
require_once dirname ( __FILE__ ) . '/../model/Person.php';
require_once dirname ( __FILE__ ) . '/../db/Persons.php';



/**
* 
*/
class EmailArt
{
	
	

	//E-mail zum Vetreter
public static function email1($holidayRequest){


	foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted == 2)
    {
    	$id = $i;
    }
  }
	$to = Persons::getEmail($id);
	$header =  "From: ".$holidayRequest->getForename." ".$holidayRequest->getPerson()->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()->getID()).">\n";
	$message = "Sehr Geehrter ".Persons::getPerson($id)->getLastname()."\n";
	$message .= "ich möchte Urlaub von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd())." nehmen und wollte wissen,"
	            ."ob Sie mich in dieser Zeitpunkt vertreten könnten.\n"
                ."Mit freundlichen Grüßen\n"
                .$holidayRequest->getPerson()->getForename()." ".$holidayRequest->getPerson()->getLastname();

    $subject = "Vertretung";
    $email = new Email($to,$subject,$message,$header);
    $email->senden();

}

	//E-mail zum Abteilungsleiter
public static function email2($holidayRequest){


	foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted == 2)
    {
    	$id = $i;
    }
  }
	$to = Persons::getEmail($id);
	$header   =  "From: ".$holidayRequest->getForename." ".$holidayRequest->getPerson()->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()->getID()).">\n";
	$message  = "Sehr Geehrter ... \n";
	$message .= "ich möchte Urlaub von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd())." nehmen.Die Vertretung Übernimt  Frau/Herr"
               .Persons::getPerson($id)->getLastname()." Ich bitte Sie mir dies zu bestätigen.\n"
                ."Mit freundlichen Grüßen\n"
                .$holidayRequest->getPerson()->getForename()." ".$holidayRequest->getPerson()->getLastname();

    $subject = "Vertretung";
    $email = new Email($to,$subject,$message,$header);
    $email->senden();

}

	//E-mail zum Administrator
public static function email3($holidayRequest){


	foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted == 2)
    {
    	$id = $i;
    }
  }
	$to = Persons::getEmail($id);
	$header =  "From: ".$holidayRequest->getForename." ".$holidayRequest->getPerson()->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()->getID()).">\n";
	$message = "Sehr Geehrter ".Persons::getPerson($id)->getLastname()."\n";
	$message .= "Sie haben meinen Urlaubsantrag noch nicht bearbeitet\n"
                ."Mit freundlichen Grüßen\n"
                .$holidayRequest->getPerson()->getForename()." ".$holidayRequest->getPerson()->getLastname();

    $subject = "Erinnerung";
    $email = new Email($to,$subject,$message,$header);
    $email->senden();

}

}

?>