<?php
class IntroduceAHost{

	private $affiliateUser;
	private $emailService;

	/**
	 * IntroduceAHost constructor.
	 *
	 * @param $affiliateUser
	 * @param $emailService
	 */
	public function __construct(AffiliateUser $affiliateUser, Email $emailService ) {
		$this->affiliateUser = $affiliateUser;
		$this->emailService  = $emailService;
	}

	public function introduceAHost(){
		$this->sendEmailToHosts();
		$this->addHostsToDatabase();
		return 'success';
	}

	private function sendEmailToHosts(){
		$this->emailService->sendIntroduceAHostEmail($this->affiliateUser);
	}

	private function addHostsToDatabase(){
		/* @var $host HostUser */
		/* @var $this->affiliateUser AffiliateUser  */
		foreach($this->affiliateUser->getHostUsers() as $key=>$host){
			$hostBasicData = [
				'post_title'    =>	$host->getEmail(),
				'post_type'     => 	'hosts',
				'post_status'   => 	'publish',
				'post_name'      =>  $host->getHashCode()
			];
			$newHostID = wp_insert_post($hostBasicData);
			update_field('hash',$host->getHashCode(),$newHostID);
			update_field('affiliate_imie_i_nazwisko',$this->affiliateUser->getNameAndSurname(),$newHostID);
			update_field('affiliate_adres_email',$this->affiliateUser->getEmail(),$newHostID);
			update_field('affiliate_msg',$this->affiliateUser->getMsg(),$newHostID);
			update_field('status','Wysłany',$newHostID);
		}
	}
}