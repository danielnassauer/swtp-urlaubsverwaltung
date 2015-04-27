<?php 
require '../server/PDFCreator.php';
require '../server/model/HolidayRequest.php';
require '../server/model/Person.php';

$person = new Person(42, "Max", "Mustermann", "Verkauf", False, 50, 1,false);

//abgelehnter urlaubsantrag
$holiday_request = new HolidayRequest(1, 1428105600, 1428278400, $person, array(), 1, 3,"kein Vertreter!");
PDFCreator::writePDF($holiday_request, "../pdf/".$person->getForename().".pdf");

