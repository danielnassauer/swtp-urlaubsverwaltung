<?php
require_once dirname(__FILE__).'/Request.php';
require_once dirname(__FILE__).'/RequestHandler.php';

$request = new Request ();
$handler = new RequestHandler($request);
?>
