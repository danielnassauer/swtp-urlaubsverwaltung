<?php
require("../lib/fpdf/fpdf.php");


class PDFCreator {

	private $array;
	
	/**
	 * Erzeugt ein PDF-Dokument zu einem Urlaubsantrag.
	 * @param holiday_request HolidayRequest-Objekt der Urlaubsanfrage
	 * @param $path Pfad, an den das PDF-Dokument geschrieben wird
	 */
	public static function writePDF($holiday_request, $path) {
		echo "PDF erfolgreich erzeugt... ";
       $array = $holiday_request->getPerson()->toArray();

		$timestamp = time();
        $datum = date("d.m.Y",$timestamp);
        $uhrzeit = date("H:i",$timestamp);
       
		
         $pdf = new FPDF();
         $pdf->SetFont("Helvetica","BU",20);
         $pdf->AddPage();

         $pdf->Image("../server/logo_orion.png",150,10,50);
         $pdf->Ln(20);
         switch ($holiday_request->getType())
         {
         	case 1:
            $pdf->MultiCell( 0, 10, "Urlaubsantrag" , 0, 'C', 0);	
         		break;
         	case 2:
            $pdf->MultiCell( 0, 10, "Freizeitantrag" , 0, 'C', 0);	
                break;
         	
         	default:
            $pdf->MultiCell( 0, 10, "Sonderurlaub" , 0, 'C', 0);		 
        		break;
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
         $pdf->Write(5,"Urlaub von:                            ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,date("d.m.Y",$holiday_request->getStart())."                    ");
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"bis                   ");
         $pdf->SetFont("Helvetica","BI",12);
         $pdf->Write(5,date("d.m.Y",$holiday_request->getEnd()));
         $pdf->Ln();
         $pdf->Ln();
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5,"Dies entspricht ");
         $pdf->SetFont("Helvetica","BI",12);

         //Berechnung des Tages
         $differenz = $holiday_request->getEnd() - $holiday_request->getStart();
         $tag = floor($differenz / (3600 * 24)) + 1;

         $pdf->Write(5," ".$tag);
         $pdf->SetFont("Helvetica","I",12);
         $pdf->Write(5," Urlaubstagen.");
         $pdf->Ln();
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
?>
