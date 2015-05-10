<?php

require_once dirname ( __FILE__ ) . '/../model/HolidayRequest.php';
require_once dirname ( __FILE__ ) . '/../model/Person.php';
require_once dirname ( __FILE__ ) . '/../db/Persons.php';
require_once dirname ( __FILE__ ) . '/../db/HolidayRequests.php';
require_once dirname ( __FILE__ ) . '/../HolidayCalculator.php';



/**
* 
*/
class EmailArt
{
  
  

  //E-mail zum Vetreter
public static function email1($holidayRequest){ 
  $sent = false;
  $requester = Persons::getPerson($holidayRequest->getPerson());

  //$requester = $holidayRequest->getPerson();
  $subject = "Vertretung";
  $header   = array();
  $header[] = "MIME-Version: 1.0";
  $header[] = "Content-type: text/plain; charset=UTF-8";
  $header[] =  "From: ".$requester->getForename()." ".$requester->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()).">\n"; 


  foreach ($holidayRequest->getSubstitutes() as $i => $accepted)
  {
    if($accepted == 1)
    {
      $id = $i;

      $to = Persons::getEmail($id);

      $message = "Sehr Geehrte(r) Frau/Herr ".Persons::getPerson($id)->getLastname().",\n\n";
      $message .= "ich möchte ".$holidayRequest->getType()." von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd())." nehmen und wollte wissen,"
               ."ob Sie mich in dieser Zeitpunkt vertreten könnten.\n\n"
               ."Mit freundlichen Grüßen\n"
               .$requester->getForename()." ".$requester->getLastname();

      $sent = mail($to,$subject,$message,implode("\r\n",$header));

    }
  }
      return $sent;
}

  //E-mail zum Abteilungsleiter
public static function email2($holidayRequest){
    $sent = false;
    $subID = $_POST['sub'];
    $subPerson = Persons::getPerson($subID);
    $requester = Persons::getPerson($holidayRequest->getPerson());
    $subject = $holidayRequest->getType(); 
    $abteilungsleiter = self::getDepartmentLeiter($requester->getDepartment());
    
    $header   = array();
    $header[] = "MIME-Version: 1.0";
    $header[] = "Content-type: text/plain; charset=UTF-8";
    $header[] =  "From: ".$requester->getForename()." ".$requester->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()).">\n"; 


      $to = Persons::getEmail($abteilungsleiter->getID());
  

      $message  = "Sehr Geehrte(r) Frau/Herr ".$abteilungsleiter->getLastname().",\n\n";
      $message .= "hiermit beantrage ich ". 
                $holidayRequest->getType()." im Zeitraum von ".date("d.m.Y",$holidayRequest->getStart())." bis ".date("d.m.Y",$holidayRequest->getEnd()).". Die Vertretung Übernimt  Frau/Herr "
               .$subPerson->getForename()." ".$subPerson->getLastname()."\nBitte bestätigen Sie mir die ".$holidayRequest->getType().".\n\n"
               ."Mit freundlichen Grüßen\n"
               .$requester->getForename()." ".$requester->getLastname();

      $sent = mail($to,$subject,$message,implode("\r\n",$header));

    
  
     return $sent;
}

  //E-mail zum Administrator
public static function email3($holidayRequest)
{

  $sent = false;
  $requester = Persons::getPerson($holidayRequest->getPerson());
  $subject = "Erinnerung";
  $administrator = self::getAdmin();
  $header   = array();
  $header[] = "MIME-Version: 1.0";
  $header[] = "Content-type: text/plain; charset=UTF-8";
 // $header[] =  "From: ".$requester->getForename()." ".$requester->getLastname()." <".Persons::getEmail($holidayRequest->getPerson()).">\n"; 
 foreach ($administrator as $key => $admin) {
  $to = Persons::getEmail($admin->getID());
  $message = "Sehr Geehrte(r) Frau/Herr ".$admin->getLastname().",\n\n";
  $message .= "Der Antrag von Herr/Frau ".$requester->getLastname()." ".$requester->getForename()
                ."in der Abteilung ".$requester->getDepartment()." wurde noch nicht bearbeitet.Vielen Dank.\n\n"
                ."Mit freundlichen Grüßen\n";

  $sent = mail($to,$subject,$message,implode("\r\n",$header));
 }


                

   return $sent;

}

public static function getDepartmentLeiter($dp)
{

  foreach (Persons::getPersons() as $key => $value)
   {
    if(strcmp($dp, $value->getDepartment()) == 0 && $value->getRole() == 2)
    {
      return $value;
    }
   }
   return false;
}

public static function getAdmin()
{
  $result = array();

  foreach (Persons::getPersons() as $key => $value)
   {
    if($value->isAdmin() == true)
    {
      $result[] = $value;
    }
   }
   return $result;
}

}

if(isset($_POST["email_holidayrequest"])){
  $id = $_POST["email_holidayrequest"];
  $request = HolidayRequests::getRequest($id);
  $type = $_POST['type'];
  //var_dump($type);
  if($type == 1)
  {
   EmailArt::email1($request);
  }

  if($type == 2)
  {
   EmailArt::email2($request);
  }

}


?>