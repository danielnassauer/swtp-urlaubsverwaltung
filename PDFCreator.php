<?php
require_once dirname ( __FILE__ ) . '/lib/fpdf/fpdf.php';
require_once dirname ( __FILE__ ) . '/server/db/HolidayRequests.php';
require_once dirname ( __FILE__ ) . '/server/db/Persons.php';
require_once dirname ( __FILE__ ) . '/server/model/HolidayRequest.php';
require_once dirname ( __FILE__ ) . '/server/model/Person.php';
//require_once dirname ( __FILE__ ) . '/server/HolidayCalculator.php';



class PDFCreator {

	private $array ;
	
	/**
	 * Erzeugt ein PDF-Dokument zu einem Urlaubsantrag.
	 * @param holiday_request HolidayRequest-Objekt der Urlaubsanfrage
	 * @param $path Pfad, an den das PDF-Dokument geschrieben wird
	 */
	public static function writePDF($holiday_request, $path) {
		echo "PDF erfolgreich erzeugt... ";
		$person = Persons::getPerson($holiday_request->getPerson());
        $array = $person->toArray();

		$timestamp = time();
        $datum = date("d.m.Y",$timestamp);
        $uhrzeit = date("H:i",$timestamp);
       
		
         $pdf = new FPDF();
         $pdf->SetFont("Helvetica","BU",20);
         $pdf->AddPage();

         $pdf->Image("server/logo_orion.png",150,10,50);
         $pdf->Ln(20);
         
             //Titel
            if(strcmp($holiday_request->getType(),"Urlaub") == 0)
            {
             $pdf->MultiCell( 0, 10, utf8_decode("Urlaubsbestätigung") , 0, 'C', 0); 

            }
            else if(strcmp($holiday_request->getType(),"Freizeit") == 0)
            {
             $pdf->MultiCell( 0, 10, utf8_decode("Freizeitbestätigung") , 0, 'C', 0); 

            }
            else if(strcmp($holiday_request->getType(),"Krankheit") == 0)
            {
             $pdf->MultiCell( 0, 10, utf8_decode("Krankheitsbestätigung") , 0, 'C', 0); 

            }
            else
            {
             $pdf->MultiCell( 0, 10, utf8_decode($holiday_request->getType()) , 0, 'C', 0); 
              
            }


         	
      

         $pdf->Ln(15);

         $pdf->SetFont("Helvetica","I",12);

         //Vor- und Nachname
         $pdf->Ln(10);
         $pdf->Write(5,"Vor- und Nachname:               ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,$array["forename"]." ".$array["lastname"]);
         $pdf->Ln();
         $pdf->Ln();

         
         //Abteilung
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"Abteilung:                               ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,$array["department"]);
         $pdf->Ln();
         $pdf->Ln();


         //Personal-Nr
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"Personal-Nr.:                          ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,$array["id"]);
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln();

         $pdf->Ln(10);



          //Urlaub von-bis
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,$holiday_request->getType()." von:                            ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,date("d.m.Y",$holiday_request->getStart())."                    ");
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"bis                   ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,date("d.m.Y",$holiday_request->getEnd()));
         $pdf->Ln();
         $pdf->Ln();
         $pdf->Ln(10);



         //Vertretung
         foreach ($holiday_request->getSubstitutes() as $i => $accepted)
         {
            if($accepted == 2)
             {
               $id = $i;
         
             }
         }
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"Vertretung:                            ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,Persons::getPerson($id)->getLastname()." ".Persons::getPerson($id)->getForename());
         $pdf->Ln();
         $pdf->Ln();



         
         
         $pdf->Ln(10);

 
         //Status
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"Status Ihres Antrages:          ");
         $pdf->SetFont("Helvetica","BI",12);
         switch ($holiday_request->getStatus())
         {
         	case 1:
            $pdf->Write(5,"angenomen");	
         		break;
         	case 2:
            $pdf->Write(5,"wartend");	
                break;
         	
         	default:
            $pdf->Write(5,"abgelehnt");	 
        		break;
         }
         $pdf->Ln();
         $pdf->Ln(15);

          //Grund der Ablehnung
         if($holiday_request->getStatus() == 3)
         {

         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"Grund der Ablehnung:         ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,$holiday_request->getComment());

         }

         $pdf->Ln(80);

         $pdf->SetFont("Helvetica","I",10);
         $pdf->Write(10,$datum." um ".$uhrzeit." Uhr");
         
          
          
         $pdf->Output($path);
         
	}
	
	
}

if(isset($_POST["pdf_holidayrequest"])){
	$id = $_POST["pdf_holidayrequest"];
	$request = HolidayRequests::getRequest($id);
	$path = "pdf/".$id.".pdf";
	PDFCreator::writePDF($request, $path);
	echo "<a href='".$path."'>download</a>";
}
?>
