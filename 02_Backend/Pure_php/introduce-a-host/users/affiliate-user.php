<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-10-29
 * Time: 07:30
 */

class AffiliateUser  {

	private $email;
	private $nameAndSurname;
	private $hostUsers;
	private $hostUsersString;
	private $msg;

	/**
	 * AffiliateUser constructor.
	 *
	 * @param $email
	 * @param $nameAndSurname
	 * @param $hostUsers
	 * @param $hostUsersString
	 * @param $msg
	 */
	public function __construct( $email, $nameAndSurname, $hostUsers, $hostUsersString, $msg ) {
		$this->email           = htmlentities($email, ENT_QUOTES);
		$this->nameAndSurname  = htmlentities($nameAndSurname, ENT_QUOTES);
		$this->hostUsers       = $hostUsers;
		$this->hostUsersString = htmlentities($hostUsersString, ENT_QUOTES);
		$this->msg             = htmlentities($msg, ENT_QUOTES);
	}


	/**
	 * @return mixed
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail( $email ) {
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getNameAndSurname() {
		return $this->nameAndSurname;
	}

	/**
	 * @param mixed $nameAndSurname
	 */
	public function setNameAndSurname( $nameAndSurname ) {
		$this->nameAndSurname = $nameAndSurname;
	}

	/**
	 * @return mixed
	 */
	public function getHostUsers() {
		return $this->hostUsers;
	}

	/**
	 * @param mixed $hostUsers
	 */
	public function setHostUsers( $hostUsers ) {
		$this->hostUsers = $hostUsers;
	}

	/**
	 * @return mixed
	 */
	public function getHostUsersString() {
		return $this->hostUsersString;
	}

	/**
	 * @param mixed $hostUsersString
	 */
	public function setHostUsersString( $hostUsersString ) {
		$this->hostUsersString = $hostUsersString;
	}

	/**
	 * @return mixed
	 */
	public function getMsg() {
		return $this->msg;
	}

	/**
	 * @param mixed $msg
	 */
	public function setMsg( $msg ) {
		$this->msg = $msg;
	}






}