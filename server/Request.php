<?php
include dirname(__FILE__).'/../lib/json/jsonCompatibility.php';
class Request {
	public $url;
	public $method;
	public $content;

	public function __construct() {
		$this->method = $_SERVER ['REQUEST_METHOD'];
		$this->url = explode ( '/', $_SERVER ['REQUEST_URI'] );
		array_splice ( $this->url, 0, 1 );
		$this->content = json_decode ( file_get_contents ( 'php://input' ), $assoc = true );
	}
}
?>