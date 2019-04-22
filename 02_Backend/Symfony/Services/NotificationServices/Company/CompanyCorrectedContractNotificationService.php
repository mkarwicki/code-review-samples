<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-26
 * Time: 08:04
 */

namespace App\Services\NotificationServices\Company;

use App\Entity\Contracts;
use App\Entity\Notifications;
use App\Services\NotificationServices\NotificationServiceTrait;


class CompanyCorrectedContractNotificationService {
	private $notificationType = 'company-corrected-contract';


	use NotificationServiceTrait;


	/**
	 * @param Contracts $contract
	 *
	 * @return bool
	 */
	public function addNotification(Contracts $contract) {
		/* IF THIS TYPE OF NOTIFICATION IS NOT ACTIVE IN CURRENT USER PROFILE SETTINGS DO NOT CONTINUE */
		if(!$this->userNotificationActivated($contract->getCompany(), $this->notificationType)) return false;
		/* ADD NOTIFICATION */
		$notification = new Notifications();
		$notification->setType($this->notificationType);
		$notification->setJobs($contract->getJob());
		$notification->setCompany($contract->getCompany());
		$notification->setContractor($contract->getContractor());
		$notification->setContract($contract);
		$notification->setPublishDate(new \DateTime());
		$this->em->persist($notification);
		$this->em->flush();
		$this->sendNotificationEmail($notification);
	}


	public function sendNotificationEmail(Notifications $notification){
		$this->notificationsEmail->setName(
			'Change request for contract '.$notification->getContract()->getContractID().'('.$notification->getJobs()->getTitle().')'
		);
		$this->notificationsEmail->setBody(
			$this->twig->render( 'email_templates/notifications_email/company/company_corrected_contract.html.twig',
				[
					'notification' => $notification
				]
			)
		);
		$this->notificationsEmail->setTo($notification->getCompany()->getEmail());
		$this->notificationsEmail->sendEmail();
	}










}