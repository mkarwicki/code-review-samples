<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-26
 * Time: 09:55
 */

namespace App\Services\NotificationServices;


use App\Entity\Users;
use App\Services\EmailServices\NotificationsEmail;
use Doctrine\ORM\EntityManagerInterface;

trait NotificationServiceTrait {

	protected $em;
	protected $notificationsEmail;
	protected $twig;

	public function __construct(
		EntityManagerInterface $em,
		NotificationsEmail $notificationsEmail,
		\Twig_Environment $twig
	) {
		$this->em = $em;
		$this->notificationsEmail = $notificationsEmail;
		$this->twig = $twig;
	}



	public function userNotificationActivated(Users $user, $type ){
		return $user->getUserNotificationSettingStatus($type);
	}





}