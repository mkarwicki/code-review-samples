<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-26
 * Time: 08:04
 */

namespace App\Services\NotificationServices\Company;

use App\Entity\Bids;
use App\Entity\Jobs;
use App\Entity\Notifications;
use App\Entity\Users;
use App\Services\NotificationServices\NotificationServiceTrait;


class CompanyAddBidsNotificationService {
	private $notificationType = 'company-new-bid';


	use NotificationServiceTrait;


	public function addNotification(Bids $bid, Jobs $jobs, Users $contractor, Users $company) {
		/* IF THIS TYPE OF NOTIFICATION IS NOT ACTIVE IN CURRENT USER PROFILE SETTINGS DO NOT CONTINUE */
		if(!$this->userNotificationActivated($company, $this->notificationType)) return false;
		/* ADD NOTIFICATION */
		$notification = new Notifications();
		$notification->setType($this->notificationType);
		$notification->setBids($bid);
		$notification->setJobs($jobs);
		$notification->setContractor($contractor);
		$notification->setCompany($company);
		$notification->setPublishDate(new \DateTime());
		$this->em->persist($notification);
		$this->em->flush();
		$this->sendNotificationEmail($notification);
	}


	public function sendNotificationEmail(Notifications $notification){
		$this->notificationsEmail->setName('New bid for job ' . $notification->getJobs()->getTitle());
		$this->notificationsEmail->setBody(
			$this->twig->render( 'email_templates/notifications_email/company/company_new_bid.html.twig',
				[
					'notification' => $notification
				]
			)
		);
		$this->notificationsEmail->setTo($notification->getCompany()->getEmail());
		$this->notificationsEmail->sendEmail();
	}










}