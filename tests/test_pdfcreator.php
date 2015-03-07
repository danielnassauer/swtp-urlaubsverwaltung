<?php 
require '../server/PDFCreator.php';
require '../server/model/HolidayRequest.php';
require '../server/model/Person.php';

$person = new Person(42, "Max", "Mustermann", "Verkauf", False, 50, 1);
// 20.01.2015 - 01.02.2015
$holiday_request = new HolidayRequest(1, 1421712000, 1422748800, $person, array(), 1, 2, null);
PDFCreator::writePDF($holiday_request, "pdf/test1.pdf");

//abgelehnter urlaubsantrag
$holiday_request = new HolidayRequest(1, 1421712000, 1422748800, $person, array(), 1, 3, null);
PDFCreator::writePDF($holiday_request, "pdf/test2.pdf");
?>