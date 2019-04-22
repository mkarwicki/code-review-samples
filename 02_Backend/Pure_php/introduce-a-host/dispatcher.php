<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-10-29
 * Time: 06:45
 */

include('settings/settings.php');
include('users/affiliate-user.php');
include('users/host-user.php');
include('users/host-users.php');
include('eventListeners/hots-event-listener.php');
include('introduce-a-host.php');




/*WYSYLANIE MAILI*/
if($_REQUEST['introduce-a-host-form']==1) {
	if(!validateCaptcha()){
		echo json_encode(array('error'=>'captcha'));
		die;
	}
	$hostsUsers= new HostUsers($_REQUEST['hostEmails']);
	$affiliateUser = new AffiliateUser(
		$_REQUEST['affiliateUserEmailAddress'],
		$_REQUEST['affiliateUserNameAndSurname'],
		$hostsUsers->getHostUsers(),
		$_REQUEST['hostEmails'],
		$_REQUEST['msg']
	);
	$introduceAHost = new IntroduceAHost($affiliateUser, new Email());
	$status = $introduceAHost->introduceAHost();
	if($status == 'success') {
		echo json_encode(array('success'=>1));
		die;
	}else{
		echo json_encode(array('error'=>1));
		die;
	}
}



/*HOST ODPALA LINK W MALU I PRZECHODZI NA NASZA STRONE*/
if($_REQUEST['host']) {
	$hash = $_REQUEST['host'];
	$hostEventListener = new HostEventListener($hash);
	$hostEventListener->onEmailLinkClickedEventSubscriber();
}








