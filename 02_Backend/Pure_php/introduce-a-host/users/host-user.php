<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-10-29
 * Time: 07:30
 */

class HostUser  {
	private $email;
	private $hashCode;
	const SALT = '918b01969c6d0f02f66488adb7a4dd56';

	/**
	 * HostUser constructor.
	 *
	 * @param $email
	 */
	public function __construct( $email ) {
		$this->email    = $email;
		$this->hashCode = $this->generateHashCode();
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
	public function getHashCode() {
		return $this->hashCode;
	}

	/**
	 * @param mixed $hashCode
	 */
	public function setHashCode( $hashCode ) {
		$this->hashCode = $hashCode;
	}

	private function generateHashCode() {
		return md5(self::SALT . strtotime('now').rand());
	}


}