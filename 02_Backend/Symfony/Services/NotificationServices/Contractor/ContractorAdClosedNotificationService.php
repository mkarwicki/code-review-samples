<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-26
 * Time: 08:04
 */

namespace App\Services\NotificationServices\Contractor;

use App\Entity\Ads;
use App\Entity\Notifications;
use App\Services\NotificationServices\NotificationServiceTrait;


class ContractorAdClosedNotificationService {
	private $notificationType = 'contractor-ad-closed';


	use NotificationServiceTrait;


	/**
	 * @param ADS $ad
	 */
	public function addNotification(Ads $ad) {
		/* IF THIS TYPE OF NOTIFICATION IS NOT ACTIVE IN CURRENT USER PROFILE SETTINGS DO NOT CONTINUE */
		if(!$this->userNotificationActivated($ad->getUser(), $this->notificationType)) return false;
		/* ADD NOTIFICATION */
		$notification = new Notifications();
		$notification->setType($this->notificationType);
		$notification->setContractor($ad->getUser());
		$notification->setAds($ad);
		$notification->setPublishDate(new \DateTime());
		$this->em->persist($notification);
		$this->em->flush();
		$this->sendNotificationEmail($notification);
	}



	public function sendNotificationEmail(Notifications $notification){
		$this->notificationsEmail->setName('Ad '.$notification->getAds()->getTitle().' has expired');
		$this->notificationsEmail->setBody(
			$this->twig->render( 'email_templates/notifications_email/contractor/contractor_close_ad.html.twig',
				[
					'notification' => $notification
				]
			)
		);
		$this->notificationsEmail->setTo($notification->getContractor()->getEmail());
		$this->notificationsEmail->sendEmail();
	}




}