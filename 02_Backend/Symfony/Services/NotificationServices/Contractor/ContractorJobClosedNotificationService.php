<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-26
 * Time: 08:04
 */

namespace App\Services\NotificationServices\Contractor;

use App\Entity\Jobs;
use App\Entity\Notifications;
use App\Entity\Users;
use App\Services\NotificationServices\NotificationServiceTrait;


class ContractorJobClosedNotificationService {
	private $notificationType = 'contractor-job-closed';

	use NotificationServiceTrait;


	/**
	 * @param Jobs $jobs
	 * @param Users $contractor
	 */
	public function addNotification(Jobs $jobs, Users $contractor) {
		/* IF THIS TYPE OF NOTIFICATION IS NOT ACTIVE IN CURRENT USER PROFILE SETTINGS DO NOT CONTINUE */
		if(!$this->userNotificationActivated($contractor, $this->notificationType)) return false;
		/* ADD NOTIFICATION */
		$notification = new Notifications();
		$notification->setType($this->notificationType);
		$notification->setJobs($jobs);
		$notification->setCompany($jobs->getUser());
		$notification->setContractor($contractor);
		$notification->setPublishDate(new \DateTime());
		$this->em->persist($notification);
		$this->em->flush();
		$this->sendNotificationEmail($notification);
	}


	public function sendNotificationEmail(Notifications $notification){
		$this->notificationsEmail->setName('Job '.$notification->getJobs()->getTitle().' has been closed.');
		$this->notificationsEmail->setBody(
			$this->twig->render( 'email_templates/notifications_email/contractor/contractor_job_closed.html.twig',
				[
					'notification' => $notification
				]
			)
		);
		$this->notificationsEmail->setTo($notification->getContractor()->getEmail());
		$this->notificationsEmail->sendEmail();
	}










}