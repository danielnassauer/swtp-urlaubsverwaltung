<?php
class PDFCreator {
	
	/**
	 * Erzeugt ein PDF-Dokument zu einem Urlaubsantrag.
	 * @param holiday_request HolidayRequest-Objekt der Urlaubsanfrage
	 * @param $path Pfad, an den das PDF-Dokument geschrieben wird
	 */
	public static function writePDF($holiday_request, $path) {
		echo $holiday_request->getPerson()->getForename();
	}
	
	
}
?>