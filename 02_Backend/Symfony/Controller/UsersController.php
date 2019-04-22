<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-07-23
 * Time: 12:22
 */

namespace App\Controller\Users;

use App\Entity\Jobs;
use App\Forms\Users\Common\ChangePasswordForm\ChangePasswordForm;
use App\Forms\Users\Companies\Contracts\EditContractForms\EditFixedFeeLocalContractForm;
use App\Forms\Users\Companies\Contracts\EditContractForms\EditFixedFeeOnlineContractForm;
use App\Forms\Users\Companies\Contracts\EditContractForms\EditHourlyRateLocalContractForm;
use App\Forms\Users\Companies\Contracts\EditContractForms\EditHourlyRateOnlineContractForm;
use App\Forms\Users\Companies\Contracts\ReplyWithoutSigningContractForm\ReplyWithoutSigningContractForm;
use App\Forms\Users\Companies\Contracts\SendContractForm\SendContractForm;
use App\Forms\Users\Companies\EditProfileDetailsForm\EditCompanyProfileDetailsForm;
use App\Forms\Users\Contractors\Contracts\RequestChangeForm\RequestChangeForm;
use App\Forms\Users\Contractors\Contracts\SignContractForm\SignContractForm as SignContractFormContractor;
use App\Forms\Users\Companies\Contracts\SignContractForm\SignContractForm as SignContractFormCompany;
use App\Forms\Users\Contractors\EditProfileDetailsForm\EditContractorProfileDetailsForm;
use App\Forms\Users\Contractors\EditProfileForm\EditContractorProfileForm;
use App\Forms\Users\Companies\EditProfileForm\EditCompanyProfileForm;
use App\Forms\Users\Contractors\EditProfileNotificationsForm\EditProfileNotificationsForm;
use App\Forms\Users\Contractors\PostABidForm\PostABidForm;
use App\Repository\Contracts\CompaniesContractsRepository;
use App\Repository\Contracts\ContractorsContractsRepository;
use App\Repository\Contracts\ContractsRepository;
use App\Repository\Jobs\CompaniesJobsRepository;
use App\Repository\Jobs\ContractorsJobsRepository;
use App\Repository\Jobs\JobsRepository;
use App\Repository\Notifications\NotificationsRepository;
use App\Repository\Sectors\Qualifications\QualificationsRepository;
use App\Repository\Sectors\SectorsRepository;
use App\Repository\Sectors\UsersSectorsRepository;
use App\Repository\Users\UsersRepository;
use App\Services\BreadcrumbServices\CompaniesBreadcrumbService;
use App\Services\BreadcrumbServices\ContractorsBreadcrumbService;
use App\Services\NavigationServices\MainNavigationService;
use App\Services\NavigationServices\UsersNavigationService;
use App\Services\PagesServices\PagesServices;
use App\Utils\Text\TextUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Logged in users controller
 * for common user actions
 *
 * Table of contents:
 * Routes
 * 1 - Dashboard
 * 2 - Edit profile
 * 3 - Edit profile details
 * 4 - Edit profile notifications
 * 5 - Change profile password
 * 6 - JOBS aka JOBS LIST
 * 7 - MY CONTRACTS aka CONTRACTS
 * 8 - Contract page
 *
 * Class UsersController
 * @package App\Controller\Users
 */
class UsersController extends Controller {

	/**
	 * 1 - Dashboard
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY') or has_role('ROLE_ADMIN')")
	 * @Route("dashboard", name="users_dashboard")
	 */
	public function dashboard(
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		JobsRepository $jobsRepository,
		ContractsRepository $contractsRepository,
		PostABidForm $postABidForm,
		Request $request,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$user = $this->getUser();
		$notificationsData=$nr->getNotificationsData($this->getUser());
		if($user->hasRole( 'ROLE_ADMIN' ) ){
			return $this->redirectToRoute('sonata_admin_login');
		}
		if ( $user->hasRole( 'ROLE_CONTRACTOR' ) ) {
			$upcomingAndOngoingJobs = $jobsRepository->getUpcomingAndOngoingContractorJobs($user);
			$openJobsWithUserBids = $jobsRepository->getOpenJobsWithContractorBids($user);
			$contractsRequiringAttention = $contractsRepository->getContractsRequiringContractorAttention($user);
			/**
			 * CONTRACTOR USER POST BID FORMS
			 */
			$postABidsForms = [];
			if(count($openJobsWithUserBids)>0){
				foreach ( $openJobsWithUserBids as $key => $job ) {
					if ( $job->getStatus() == 'open' ) {
						$form = $postABidForm->getPostABidContractorForm(
							$request, $this,
							$user,
							$job,
							'post_a_bid_modal-' . $job->getID()
						);
						$postABidsForms[ $job->getID() ] = $form->createView();
						if ( $form->isSubmitted() && $form->isValid() ) {
							return $this->redirectToRoute( 'users_dashboard' );
						}
					}
				}
			}
			return $this->render( 'users/contractors/pages/dashboard/contractors_dashboard.html.twig', [
				'mainNav'                     => $mainNav->getNavigation( null ),
				'usersNav'                    => $usersNav->getNavigation( [ 'dashboard' ] ),
				'showTopUsersNav'             => true,
				'upcomingAndOngoingJobs'      => $upcomingAndOngoingJobs,
				'openJobsWithUserBids'        => $openJobsWithUserBids,
				'postABidsForms'              => $postABidsForms,
				'contractsRequiringAttention' => $contractsRequiringAttention,
				'notificationsData'           => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('freelancer_dashboard')
			] );
		} elseif ( $user->hasRole( 'ROLE_COMPANY' ) ) {
			$upcomingAndOngoingJobs      = $jobsRepository->getUpcomingAndOngoingCompanyJobs( $user );
			$openUserJobs                = $jobsRepository->getOpenUserJobs( $user );
			$contractsRequiringAttention = $contractsRepository->getContractsRequiringCompanyAttention( $user );
			return $this->render( 'users/companies/pages/dashboard/companies_dashboard.html.twig', [
				'mainNav'                     => $mainNav->getNavigation( null ),
				'usersNav'                    => $usersNav->getNavigation( [ 'dashboard' ] ),
				'showTopUsersNav'             => true,
				'upcomingAndOngoingJobs'      => $upcomingAndOngoingJobs,
				'openUserJobs'                => $openUserJobs,
				'contractsRequiringAttention' => $contractsRequiringAttention,
				'notificationsData'           => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('company_dashboard')
			] );
		}
	}


	/**
	 *
	 * 2 - Edit profile
	 *
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/edit_profile", name="users_dashboard_edit_profile")
	 */
	public function profile(
		Request $request,
		EditContractorProfileForm $editContractorProfileForm,
		UsersSectorsRepository $userSectorsRepository,
		SectorsRepository $sectorsRepository,
		QualificationsRepository $qualificationsRepository,
		EditCompanyProfileForm $editCompanyProfileForm,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		ContractorsBreadcrumbService $contractorsBreadcrumbService,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$user = $this->getUser();
		$notificationsData=$nr->getNotificationsData($this->getUser());

		if ( $user->hasRole( 'ROLE_CONTRACTOR' ) ) {
			$form = $editContractorProfileForm->getEditContractorForm( $request, $this, $user, $userSectorsRepository );

			return $this->render( 'users/contractors/pages/edit_profile/contractors_edit_profile.html.twig', [
				'editContractorProfileForm' => $form->createView(),
				'formSubmittedAndValid'     => $form->isSubmitted() && $form->isValid(),
				'userSectorsData'           => $userSectorsRepository->findBy( [ 'user' => $user ] ),
				'sectorsData'               => $sectorsRepository->findAll(),
				'qualificationsData'        => $qualificationsRepository->findAll(),
				'breadcrumbTrail'           => $contractorsBreadcrumbService->getBreadcrumbTrailNavigationForRoute( 'users_dashboard_edit_profile' ),
				'mainNav'                   => $mainNav->getNavigation( null ),
				'usersNav'                  => $usersNav->getNavigation( null ),
				'showTopUsersNav'           => true,
				'notificationsData'         => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('private_profile')
			] );
		} elseif ( $user->hasRole( 'ROLE_COMPANY' ) ) {
			$form = $editCompanyProfileForm->getEditCompanyProfileForm( $request, $this, $user );

			return $this->render( 'users/companies/pages/edit_profile/companies_edit_profile.html.twig', [
				'editCompanyProfileForm' => $form->createView(),
				'formSubmittedAndValid'  => $form->isSubmitted() && $form->isValid(),
				'breadcrumbTrail'        => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_dashboard_edit_profile' ),
				'mainNav'                => $mainNav->getNavigation( null ),
				'usersNav'               => $usersNav->getNavigation( [ 'profile' ] ),
				'showTopUsersNav'        => true,
				'notificationsData'         => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('private_profile')
			] );
		}
	}


	/**
	 * 3 - Edit profile details
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/edit_profile/profile_details", name="users_dashboard_edit_profile_details")
	 */
	public function profileDetails
	(
		Request $request,
		EditContractorProfileDetailsForm $editContractorProfileDetailsForm,
		EditCompanyProfileDetailsForm $editCompanyProfileDetailsForm,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		ContractorsBreadcrumbService $contractorsBreadcrumbService,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$user = $this->getUser();
		$notificationsData=$nr->getNotificationsData($this->getUser());
		if ( $user->hasRole( 'ROLE_CONTRACTOR' ) ) {
			$form = $editContractorProfileDetailsForm->getEditContractorProfileDetailsForm( $request, $this, $user );

			return $this->render( 'users/contractors/pages/edit_profile_details/contractor_edit_profile_details.html.twig',
				[
					'editContractorProfileDetailsForm' => $form->createView(),
					'formSubmittedAndValid'            => $form->isSubmitted() && $form->isValid(),
					'breadcrumbTrail'                  => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_dashboard_edit_profile_details' ),
					'mainNav'                          => $mainNav->getNavigation( null ),
					'usersNav'                         => $usersNav->getNavigation( null ),
					'showTopUsersNav'                  => true,
					'notificationsData'         => $notificationsData,
					'footerPages'       => $pagesService->getFooterPages(),
					'page'              => $pagesService->getCurrentPage('profile_details')
				] );
		} elseif ( $user->hasRole( 'ROLE_COMPANY' ) ) {
			$form = $editCompanyProfileDetailsForm->getEditCompanyProfileDetailsForm( $request, $this, $user );

			return $this->render( 'users/companies/pages/edit_profile_details/company_edit_profile_details.html.twig',
				[
					'editCompanyProfileDetailsForm' => $form->createView(),
					'formSubmittedAndValid'         => $form->isSubmitted() && $form->isValid(),
					'breadcrumbTrail'               => $contractorsBreadcrumbService->getBreadcrumbTrailNavigationForRoute( 'users_dashboard_edit_profile_details' ),
					'mainNav'                       => $mainNav->getNavigation( null ),
					'usersNav'                      => $usersNav->getNavigation( [ 'profile-details' ] ),
					'showTopUsersNav'               => true,
					'notificationsData'         => $notificationsData,
					'footerPages'       => $pagesService->getFooterPages(),
					'page'              => $pagesService->getCurrentPage('profile_details')
				] );
		}
	}


	/**
	 * 4 - Edit profile notifications
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/edit_profile/notifications", name="edit_profile_notifications")
	 *
	 */
	public function editProfileNotifications (
		Request $request,
		EditProfileNotificationsForm $edit_profile_notifications_form,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$user = $this->getUser();
		$notificationsData=$nr->getNotificationsData($this->getUser());
		if ( $user ) {
			$notificationsTypesData=$this->getParameter('notifications_types');
			if ( $user->hasRole( 'ROLE_CONTRACTOR' ) ) {
				$form = $edit_profile_notifications_form->getEditContractorNotificationsForm( $request, $this, $user, $notificationsTypesData['contractor'] );
				return $this->render( 'users/contractors/pages/edit_profile_notifications/contractor_edit_profile_notifications.twig',
					[
						'editContractorProfileNotificationForm' => $form->createView(),
						'formSubmittedAndValid'                 => $form->isSubmitted() && $form->isValid(),
						'breadcrumbTrail'                       => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'edit_profile_notifications' ),
						'mainNav'                               => $mainNav->getNavigation( null ),
						'usersNav'                              => $usersNav->getNavigation( [ 'notification-settings' ] ),
						'showTopUsersNav'                       => true,
						'notificationsData'                     => $notificationsData,
						'footerPages'       => $pagesService->getFooterPages(),
						'page'              => $pagesService->getCurrentPage('profile_notifications')
					]
				);
			}elseif ( $user->hasRole( 'ROLE_COMPANY' ) ) {
				$form = $edit_profile_notifications_form->getEditContractorNotificationsForm( $request, $this, $user, $notificationsTypesData['company'] );
				return $this->render( 'users/contractors/pages/edit_profile_notifications/contractor_edit_profile_notifications.twig',
					[
						'editContractorProfileNotificationForm' => $form->createView(),
						'formSubmittedAndValid'                 => $form->isSubmitted() && $form->isValid(),
						'breadcrumbTrail'                       => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'edit_profile_notifications' ),
						'mainNav'                               => $mainNav->getNavigation( null ),
						'usersNav'                              => $usersNav->getNavigation( [ 'notification-settings' ] ),
						'showTopUsersNav'                       => true,
						'notificationsData'                     => $notificationsData,
						'footerPages'       => $pagesService->getFooterPages(),
						'page'              => $pagesService->getCurrentPage('profile_notifications')
					]
				);
			}
		}
	}


	/**
	 * 5 - Change profile password
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/edit_profile/change_password", name="change_profile_password")
	 */
	public function changeProfilePassword(
		Request $request,
		ChangePasswordForm $changePasswordForm,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$user = $this->getUser();
		$form = $changePasswordForm->getChangePasswordForm( $request, $this, $user );
		$notificationsData=$nr->getNotificationsData($this->getUser());
		if ( $user ) {
			return $this->render( 'users/common/pages/change_password/change_password_page.html.twig',
				[
					'changePasswordForm'    => $form->createView(),
					'formSubmittedAndValid' => $form->isSubmitted() && $form->isValid(),
					'breadcrumbTrail'       => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'change_profile_password' ),
					'mainNav'               => $mainNav->getNavigation( null ),
					'usersNav'              => $usersNav->getNavigation( [ 'password' ] ),
					'showTopUsersNav'       => true,
					'notificationsData'         => $notificationsData,
					'footerPages'       => $pagesService->getFooterPages(),
					'page'              => $pagesService->getCurrentPage('profile_change_password')
				] );
		}
	}


	/**
	 * 6 - JOBS aka JOBS LIST
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/jobs", name="users_jobs" , defaults={"page" = "1"})
	 * @Route("dashboard/jobs/{page}", requirements={"page": "[1-9]\d*"}, name="users_jobs_list_page_paginated" , defaults={"page" = "1"})
	 */
	public function jobs_list(
		int $page,
		Request $request,
		CompaniesJobsRepository $companiesJobsRepository,
		ContractorsJobsRepository $contractorsJobsRepository,
		PostABidForm $postABidForm,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$user = $this->getUser();
		$notificationsData=$nr->getNotificationsData($this->getUser());
		if ( $user->hasRole( 'ROLE_COMPANY' ) ) {
			return $this->render( 'users/companies/pages/job_list/company_job_list.html.twig', [
				'jobs'                     => $companiesJobsRepository->getJobs( $page, $request, $user ),
				'browserConfigurationData' => $companiesJobsRepository->getBrowserConfigurationData( $request ),
				'routeParams'              => $companiesJobsRepository->getRouteParams( $request ),
				'filterRouteParams'        => $companiesJobsRepository->getFiltersRouteParams( $request ),
				'paginationPage'           => $page,
				'breadcrumbTrail'          => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_jobs' ),
				'mainNav'                  => $mainNav->getNavigation( null ),
				'usersNav'                 => $usersNav->getNavigation( [ 'my-jobs' ] ),
				'showTopUsersNav'          => true,
				'notificationsData'        => $notificationsData,
				'footerPages'              => $pagesService->getFooterPages(),
				'page'                     => $pagesService->getCurrentPage('company_jobs_list_page')
			] );
		} elseif ( $user->hasRole( 'ROLE_CONTRACTOR' ) ) {
			$jobs           = $contractorsJobsRepository->getJobs( $page, $request, $user );
			$postABidsForms = [];
			foreach ( $jobs as $key => $job ) {
				/** @var Jobs $job */
				if ( $job->getStatus() == 'open' ) {
					$form = $postABidForm->getPostABidContractorForm( $request, $this, $user, $job, 'post_a_bid_modal-' . $job->getID() );
					$postABidsForms[ $job->getID() ] = $form->createView();
					if ( $form->isSubmitted() && $form->isValid() ) {
						return $this->redirectToRoute( 'users_jobs' );
					}
				}
			}
			return $this->render( 'users/contractors/pages/job_list/contractor_job_list.html.twig', [
				'jobs'                     => $contractorsJobsRepository->getJobs( $page, $request, $user ),
				'browserConfigurationData' => $contractorsJobsRepository->getBrowserConfigurationData( $request ),
				'routeParams'              => $contractorsJobsRepository->getRouteParams( $request ),
				'filterRouteParams'        => $contractorsJobsRepository->getFiltersRouteParams( $request ),
				'postABidsForms'           => $postABidsForms,
				'paginationPage'           => $page,
				'breadcrumbTrail'          => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_jobs' ),
				'mainNav'                  => $mainNav->getNavigation( null ),
				'usersNav'                 => $usersNav->getNavigation( [ 'my-jobs' ] ),
				'showTopUsersNav'          => true,
				'notificationsData'         => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('freelancer_jobs_list_page')
			] );
		}
	}


	/**
	 * 7 - MY CONTRACTS aka CONTRACTS LIST
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/my-contracts", name="users_contracts" , defaults={"page" = "1"})
	 * @Route("dashboard/my-contracts/{page}", requirements={"page": "[1-9]\d*"}, name="users_contracts_list_page_paginated" , defaults={"page" = "1"})
	 */
	public function myContracts(
		int $page,
		Request $request,
		ContractorsContractsRepository $contractorsContractsRepository,
		CompaniesContractsRepository $companiesContractsRepository,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		ContractorsBreadcrumbService $contractorsBreadcrumbService,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$contractStatuses = $this->getParameter( 'contract_statuses' );
		$user = $this->getUser();
		$notificationsData=$nr->getNotificationsData($this->getUser());
		if ( $user->hasRole( 'ROLE_COMPANY' ) ) {
			return $this->render( 'users/companies/pages/contracts_list/company_contracts_list.html.twig', [
				'contracts'                => $companiesContractsRepository->getContracts( $page, $request, $user ),
				'browserConfigurationData' => $companiesContractsRepository->getBrowserConfigurationData( $request ),
				'routeParams'              => $companiesContractsRepository->getRouteParams( $request ),
				'filterRouteParams'        => $companiesContractsRepository->getFiltersRouteParams( $request ),
				'paginationPage'           => $page,
				'breadcrumbTrail'          => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_contracts' ),
				'mainNav'                  => $mainNav->getNavigation( null ),
				'usersNav'                 => $usersNav->getNavigation( [ 'my-contracts' ] ),
				'contractStatuses'         => $contractStatuses,
				'showTopUsersNav'          => true,
				'notificationsData'        => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('company_contracts_list')

			] );
		} elseif ( $user->hasRole( 'ROLE_CONTRACTOR' ) ) {
			return $this->render( 'users/contractors/pages/contracts_list/contractor_contracts_list.html.twig', [
				'contracts'                => $contractorsContractsRepository->getContracts( $page, $request, $user ),
				'browserConfigurationData' => $contractorsContractsRepository->getBrowserConfigurationData( $request ),
				'routeParams'              => $contractorsContractsRepository->getRouteParams( $request ),
				'filterRouteParams'        => $contractorsContractsRepository->getFiltersRouteParams( $request ),
				'paginationPage'           => $page,
				'breadcrumbTrail'          => $contractorsBreadcrumbService->getBreadcrumbTrailNavigationForRoute( 'users_contracts' ),
				'mainNav'                  => $mainNav->getNavigation( null ),
				'usersNav'                 => $usersNav->getNavigation( [ 'my-contracts' ] ),
				'contractStatuses'         => $contractStatuses,
				'showTopUsersNav'          => true,
				'notificationsData'        => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('freelancer_contracts_list')
			] );
		}
	}


	/**
	 * 8 - Contract page
	 *
	 * @Security("has_role('ROLE_CONTRACTOR') or has_role('ROLE_COMPANY')")
	 * @Route("dashboard/my-contracts/contract/{contractURI}", name="user_contract" )
	 *
	 */
	public function contract(
		String $contractURI,
		Request $request,
		ContractsRepository $contractsRepository,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		ContractorsBreadcrumbService $contractorsBreadcrumbTrail,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		RequestChangeForm $contractorRequestChangeFormObj,
		EditHourlyRateLocalContractForm $editHourlyRateLocalContractForm,
		EditFixedFeeLocalContractForm $editFixedFeeLocalContractForm,
		EditFixedFeeOnlineContractForm $editFixedFeeOnlineContractForm,
		EditHourlyRateOnlineContractForm $editHourlyRateOnlineContractForm,
		SendContractForm $sendContractForm,
		SignContractFormContractor $signContractFormContractor,
		SignContractFormCompany $signContractFormCompany,
		ReplyWithoutSigningContractForm $replyWithoutSigningContractForm,
		NotificationsRepository $notificationsRepository,
		PagesServices $pagesService
	) {
		/*GET CONTRACT AND USER DATA*/
		$contractID = TextUtils::getIDFromURI( $contractURI );
		$contract   = $contractsRepository->findOneBy( [
			'contractID' => $contractID,
		] );

		$job = $contract->getJob();
		$user = $this->getUser();
		$notificationsData=$notificationsRepository->getNotificationsData($this->getUser());
		/**
		 * IF NOTIFICATION STATUS CHANGE REQUEST
		 */
		if($request->get('n')) {
			$notificationsRepository->changeNotificationIsNewStatus($user,$request->get('n'));
			return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
		}
		/**
		 * COMPANY SETTINGS
		 */
		if ( $user->hasRole( 'ROLE_COMPANY' ) && $contract->getCompany() == $user ) {
			/**
			 * EDIT CONTRACT FORM SEGMENT
			 */
			{
				/**
				 * LOCAL JOB
				 */
				if($job->getLocationType() == 1){
					/**
					 * HOUR RATE LOCAL JOB
					 */
					if($job->getBudgetType() == 1){
						$editContractForm=$editHourlyRateLocalContractForm->getForm($request, $this, $user, $contract);
					}
					/**
					 * FIXED FEE LOCAL JOB
					 */
					if($job->getBudgetType() == 2){
						$editContractForm=$editFixedFeeLocalContractForm->getForm($request, $this, $user, $contract);
					}
				}
				/**
				 * ONLINE JOB
				 */
				if($job->getLocationType() == 2){
					/**
					 * HOUR RATE ONLINE JOB
					 */
					if($job->getBudgetType() == 1){
						$editContractForm=$editHourlyRateOnlineContractForm->getForm($request, $this, $user, $contract);
					}
					/**
					 * FIXED FEE ONLINE JOB
					 */
					if($job->getBudgetType() == 2){
						$editContractForm=$editFixedFeeOnlineContractForm->getForm($request, $this, $user, $contract);
					}
				}
				$editContractFormView=$editContractForm->createView();
				if($editContractForm->isSubmitted() && $editContractForm->isValid()){
					return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
				}
			}
			/**
			 * SEND CONTRACT FORM
			 */
			$sendContractForm=$sendContractForm->getForm($request, $this, $user, $contract, $editContractForm);
			$sendContractFormView=$sendContractForm->createView();
			if($sendContractForm->isSubmitted() && $sendContractForm->isValid()){
				return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
			}
			/**
			 * SIGN CONTRACT FORM
			 */
			$signContractForm = $signContractFormCompany->getForm($request, $this, $user, $contract);
			$signContractFormView = $signContractForm->createView();
			if($signContractForm->isSubmitted() && $signContractForm->isValid()){
				return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
			}
			/**
			 * SIGN CONTRACT FORM
			 */
			$replyWithoutSigningContractForm = $replyWithoutSigningContractForm->getForm($request, $this, $user, $contract);
			$replyWithoutSigningContractFormView = $replyWithoutSigningContractForm->createView();
			if($replyWithoutSigningContractForm->isSubmitted() && $replyWithoutSigningContractForm->isValid()){
				return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
			}
			/**
			 * COMPANY VIEW SETUP
			 */
			return $this->render( 'users/companies/pages/contract_page/companies_contract_page.html.twig', [
				'contract'            => $contract,
				'contractData'        => json_decode( $contract->getContractTemporaryOverrides(), true ),
				'breadcrumbTrail'     => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'user_contract', null, $contract ),
				'mainNav'                   => $mainNav->getNavigation( null ),
				'usersNav'                  => $usersNav->getNavigation( [ 'my-contracts' ] ),
				'showTopUsersNav'           => true,
				'editContractForm'          => $editContractFormView,
				'editContractFormSubmitted' => $editContractForm->isSubmitted(),
				'sendContractForm'          => $sendContractFormView,
				'sendContractFormSubmitted' => $sendContractForm->isSubmitted(),
				'signContractForm'          => $signContractFormView,
				'replyWithoutSingingForm'   => $replyWithoutSigningContractFormView,
				'notificationsData'         => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('company_contract')
			] );
		/**
		 * CONTRACTOR SETTINGS
		 */
		} elseif ( $user->hasRole( 'ROLE_CONTRACTOR' ) && $contract->getContractor() == $user  ) {
			/**
			 * REQUEST CHANGE FORM
			 */
			$contractorRequestChangeForm=$contractorRequestChangeFormObj->getRequestContractorRequestChangeForm($request, $this, $user, $contract);
			if($contractorRequestChangeForm->isSubmitted() && $contractorRequestChangeForm->isValid()){
				return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
			}
			/**
			 * SIGN CONTRACT FORM
			 */
			$signContractForm=$signContractFormContractor->getForm($request, $this, $user, $contract);
			$signContractFormView = $signContractForm->createView();
			if($signContractForm->isSubmitted() && $signContractForm->isValid()){
				return $this->redirectToRoute( 'user_contract', ['contractURI'=>$contractURI] );
			}
			/**
			 * CONTRACTOR VIEW SETUP
			 */
			return $this->render( 'users/contractors/pages/contract_page/contractor_contract_page.html.twig', [
				'contract'                      => $contract,
				'contractData'                  => json_decode($contract->getContractOverrides(),true),
				'breadcrumbTrail'               => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'user_contract',null, $contract ),
				'mainNav'                       => $mainNav->getNavigation( null ),
				'usersNav'                      => $usersNav->getNavigation( [ 'my-contracts' ] ),
				'showTopUsersNav'               => true,
				'requestChangeFormIsSubmitted'  => $contractorRequestChangeForm->isSubmitted(),
				'requestChangeForm'             => $contractorRequestChangeForm->createView(),
				'signContractForm'              => $signContractFormView,
				'notificationsData'         => $notificationsData,
				'footerPages'       => $pagesService->getFooterPages(),
				'page'              => $pagesService->getCurrentPage('freelancer_contract')
			] );
		}else{
			throw new \Exception( 'Current user is not allowed to edit this contract.' );
		}
	}
}