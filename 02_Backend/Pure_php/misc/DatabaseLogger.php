<?php

class DatabaseLogger extends PDO {
	private $engine;
	private $host;
	private $database;
	private $user;
	private $pass;

	public function __construct(){
		$this->engine = 'mysql';
		$this->host = 'localhost';
		$this->database = '';
		$this->user = '';
		$this->pass = '';
		$dns = $this->engine.':dbname='.$this->database.";host=".$this->host;
		parent::__construct( $dns, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'") );
	}

	public function logData($address,$type,$email){
		global $apartmentTypes;
		if(strlen($apartmentTypes[$type]['title'])>0):
			$type=$apartmentTypes[$type]['title'];
			$stmt = $this->prepare("INSERT INTO Dane (typ_apartamentu, lokalizacja_apartamentu, adres_email ) VALUES (:typ_apartamentu, :lokalizacja_apartamentu,:adres_email)");
			$stmt->bindParam(':typ_apartamentu', $type);
			$stmt->bindParam(':lokalizacja_apartamentu', $address);
			$stmt->bindParam(':adres_email', $email);
			$stmt->execute();
		endif;
	}
}
