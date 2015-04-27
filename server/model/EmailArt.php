<?php

require 'Email.php';
require '../server/model/HolidayRequest.php';
require '../server/db/Persons.php';


/**
* 
*/
class EmailArt
{
	
	

	//E-mail zum Vetreter
public static function email1($holidayRequest){


	foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted)
    {
    	$id = $i;
    }
  }
	$to = Persons::getEmail($id);
	$header =  "From: ".$holidayRequest->getForename." ".$holidayRequest->getPerson()->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()->getID()).">\n";
	$message = "Sehr Geehrter ".Persons::getPerson($id)->getLastname()."\n";
	$message .= "ich möchte Urlaub von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd())." nehmen und wollte wissen,"
	            ."ob Sie mich in dieser Zeitpunkt vertreten könnten.\n";
                ."Mit freundlichen Grüßen\n"
                .$holidayRequest->getPerson()->getForename()." ".$holidayRequest->getPerson()->getLastname();

    $subject = "Vertretung";
    $email = new Email($to,$subject,$message,$header);
    $email->senden();

}

}

?>