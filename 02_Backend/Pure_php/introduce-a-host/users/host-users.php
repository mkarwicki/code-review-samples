<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-10-29
 * Time: 07:30
 */

class HostUsers  {

	private $hostUsers;

	/**
	 * HostUsers constructor.
	 *
	 * @param $hostUsers
	 */
	public function __construct( $hostUsers ) {
		$this->hostUsers = $hostUsers;
	}

	/**
	 * @return mixed
	 */
	public function getHostUsers() {
		$array = explode(',', $this->hostUsers);
		$usersCollection = [];
		foreach($array as $key=>$email){
			$email=htmlentities($email, ENT_QUOTES);
			$usersCollection[]=new HostUser($email);
		}
		return $usersCollection;
	}





}