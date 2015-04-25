<?php

class Email{

	private $to;
	private $subject;
	private $message;
	private $additional_headers;


	/**
	 * Repräsentiert ein Email
	 * @param to Adresse des Empfängers
	 * @param subject Betreff 
	 * @param message inhalt
	 * @param additional_headers header
	 */

	public function __construct($to,$subject,$message,$additional_headers){
          $this->to = $to;
          $this->subject = $subject;
          $this->message = $message;
          $this->additional_headers = $additional_headers;
	}

	public function getTo(){
		return $this->to;
	}

	public function getSubject(){
		return $this->subject;
	}

	public function getMessage(){
		return $this->message;
	}

	public function getHeader(){
		return $this->additional_headers;
	}

	public function senden(){
		@mail($this->to,$this->subject,$this->message,$this->additional_headers);
	}
}

?>