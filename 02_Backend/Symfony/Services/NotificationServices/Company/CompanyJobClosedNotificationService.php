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


class CompanyJobClosedNotificationService {
	private $notificationType = 'company-job-closed';


	use NotificationServiceTrait;


	public function addNotification(Jobs $jobs) {
		/* IF THIS TYPE OF NOTIFICATION IS NOT ACTIVE IN CURRENT USER PROFILE SETTINGS DO NOT CONTINUE */
		if(!$this->userNotificationActivated($jobs->getUser(), $this->notificationType)) return false;
		/* ADD NOTIFICATION */
		$notification = new Notifications();
		$notification->setType($this->notificationType);
		$notification->setJobs($jobs);
		$notification->setCompany($jobs->getUser());
		$notification->setPublishDate(new \DateTime());
		$this->em->persist($notification);
		$this->em->flush();
		$this->sendNotificationEmail($notification);
	}


	public function sendNotificationEmail(Notifications $notification){
		$this->notificationsEmail->setName('Job '.$notification->getJobs()->getTitle().' has been closed.');
		$this->notificationsEmail->setBody(
			$this->twig->render( 'email_templates/notifications_email/company/company_job_closed.html.twig',
				[
					'notification' => $notification
				]
			)
		);
		$this->notificationsEmail->setTo($notification->getCompany()->getEmail());
		$this->notificationsEmail->sendEmail();
	}










}