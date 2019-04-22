<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-07-26
 * Time: 15:00
 */

namespace App\Controller\Users\Companies;


use App\Entity\Bids;
use App\Entity\Jobs;
use App\Entity\JobsSectors;
use App\Entity\Users;
use App\Forms\Users\Companies\PostAJobForm\PostAJobForm;
use App\Repository\Jobs\JobsRepository;
use App\Repository\Notifications\NotificationsRepository;
use App\Repository\Sectors\JobsSectorsRepository;
use App\Repository\Sectors\Qualifications\QualificationsRepository;
use App\Repository\Sectors\SectorsRepository;
use App\Services\BreadcrumbServices\CompaniesBreadcrumbService;
use App\Services\NavigationServices\MainNavigationService;
use App\Services\NavigationServices\UsersNavigationService;
use App\Services\NotificationServices\Contractor\ContractorJobClosedNotificationService;
use App\Services\PagesServices\PagesServices;
use App\Utils\Text\TextUtils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Translation\TranslatorInterface;


/**
 *
 * Logged in companies users controller
 * for specific company user actions
 *
 * Table of contents:
 * Routes
 * 1 - POST A JOB
 * 2 - EDIT A JOB
 * 3 - DELETE A JOB
 * 4 - CLOSE A JOB
 *
 * Class CompaniesController
 * @package App\Controller\Users\Companies
 */
class CompaniesController extends Controller {

	/**
	 * 1 - POST A JOB
	 *
	 * @Security("has_role('ROLE_COMPANY')")
	 * @Route("dashboard/post-a-job", name="users_company_post_a_job")
	 *
	 */
	public function postAJob(
		Request $request,
		TranslatorInterface $translator,
		SectorsRepository $sectorsRepository,
		QualificationsRepository $qualificationsRepository,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$job = new Jobs();
		$job->setUser( $this->getUser() );
		$form = $this->createForm( PostAJobForm::class, $job );
		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$job->setPublishDate( new \DateTime() );
			$job->setJobID( $job->generateJobID() );
			$job->setTitleSlug();
			$job->setMinMaxBudgetOrRateValue();
			{ /*JOBS SECTOR*/
				//$jobsSectorsRepository->removeAllJobData($job->getId());
				$sectorID        = $form->get( 'sectors' )->getData();
				$qualificationID = $form->get( 'qualifications' )->getData();
				$sector          = $sectorsRepository->findOneBy( [ 'id' => $sectorID ] );
				$qualifications  = $qualificationsRepository->findOneBy( [ 'id' => $qualificationID ] );
				$jobsSector      = new JobsSectors();
				$jobsSector->setSectors( $sector );
				$jobsSector->setQualifications( $qualifications );
				$jobsSector->setJob( $job );
				$job->addJobsSectors( $jobsSector );
			}
			$em->persist( $job );
			$em->flush();
			$this->addFlash( 'success', $translator->trans( 'jobs.job_added', [ '%title%' => $job->getTitle() ] ) );

			return $this->json( [
				'status' => [
					'jobAdded'   => true,
					'redirectTo' => $this->generateUrl( 'visitors_job_page', [ 'jobURI' => $job->getUriSlug() ] ),
					'notificationsData' => $nr->getNotificationsData( $this->getUser() ),
					'footerPages'       => $pagesService->getFooterPages(),
				],
			] );
		}

		return $this->render( 'users/companies/pages/post_a_job/company_post_a_job.html.twig', [
			'job'                               => $job,
			'postAJobFormAction'                => 'add',
			'postAJobFormActionPath'            => 'users_company_post_a_job',
			'postAJobForm'                      => $form->createView(),
			'breadcrumbTrail'                   => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_company_post_a_job' ),
			'hideBreadcrumbJobStatusAndActions' => true,
			'mainNav'                           => $mainNav->getNavigation( null ),
			'usersNav'                          => $usersNav->getNavigation( null ),
			'showTopUsersNav'                   => true,
			'notificationsData'                 => $nr->getNotificationsData( $this->getUser() ),
			'footerPages'                       => $pagesService->getFooterPages(),
			'page'                              => $pagesService->getCurrentPage('post_a_job')
		] );
	}


	/**
	 * 2 - EDIT A JOB
	 *
	 * @Security("has_role('ROLE_COMPANY')")
	 * @Route("dashboard/jobs/edit/{jobURI}", name="users_company_edit_a_job")
	 */
	public function editAJob(
		string $jobURI,
		Request $request,
		TextUtils $textUtils,
		JobsRepository $jobsRepository,
		TranslatorInterface $translator,
		SectorsRepository $sectorsRepository,
		QualificationsRepository $qualificationsRepository,
		JobsSectorsRepository $jobsSectorsRepository,
		CompaniesBreadcrumbService $companiesBreadcrumbTrail,
		MainNavigationService $mainNav,
		UsersNavigationService $usersNav,
		NotificationsRepository $nr,
		PagesServices $pagesService
	) {
		$jobID = TextUtils::getIDFromURI( $jobURI );
		/* @var $job Jobs */
		$job   = $jobsRepository->findOneBy( [
			'user'  => $this->getUser(),
			'jobID' => $jobID,
		] );
		if ( $job->getStatus() == 'closed' || $job->getBidPlaced() ) {
			throw new \Exception( 'Job is closed or bid has been placed' );
		}
		if ( ! $job ) {
			throw new \Exception( 'Job no longer exists, has been deleted or is not editable.' );
		}
		$form = $this->createForm( PostAJobForm::class, $job );

		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$job->setTitleSlug();
			$job->setMinMaxBudgetOrRateValue();
			{ /*JOBS SECTOR*/
				$jobsSectorsRepository->removeAllJobData( $job->getId() );
				$sectorID        = $form->get( 'sectors' )->getData();
				$qualificationID = $form->get( 'qualifications' )->getData();;
				$sector         = $sectorsRepository->findOneBy( [ 'id' => $sectorID ] );
				$qualifications = $qualificationsRepository->findOneBy( [ 'id' => $qualificationID ] );
				$jobsSector     = new JobsSectors();
				$jobsSector->setSectors( $sector );
				$jobsSector->setQualifications( $qualifications );
				$jobsSector->setJob( $job );
				$job->addJobsSectors( $jobsSector );
			}
			$em->persist( $job );
			$em->flush();
			$this->addFlash( 'success', $translator->trans( 'jobs.job_edited', [ '%title%' => $job->getTitle() ] ) );

			return $this->json( [
				'status' => [
					'jobEdited'  => true,
					'redirectTo' => $this->generateUrl( 'visitors_job_page', [ 'jobURI' => $job->getUriSlug() ] )
				],
			] );
		}

		return $this->render( 'users/companies/pages/post_a_job/company_post_a_job.html.twig', [
			'job'                               => $job,
			'postAJobFormAction'                => 'edit',
			'postAJobFormActionPath'            => 'users_company_edit_a_job',
			'postAJobForm'                      => $form->createView(),
			'breadcrumbTrail'                   => $companiesBreadcrumbTrail->getBreadcrumbTrailNavigationForRoute( 'users_company_post_a_job' ),
			'hideBreadcrumbJobStatusAndActions' => true,
			'mainNav'                           => $mainNav->getNavigation( null ),
			'usersNav'                          => $usersNav->getNavigation( null ),
			'showTopUsersNav'                   => true,
			'notificationsData'                 => $nr->getNotificationsData($this->getUser()),
			'footerPages'                       => $pagesService->getFooterPages(),
			'page'                              => $pagesService->getCurrentPage('edit_a_job')
		] );
	}

	/**
	 * 3 - DELETE A JOB
	 *
	 * @Security("has_role('ROLE_COMPANY')")
	 * @Route("dashboard/jobs/delete/{jobURI}", name="users_company_delete_a_job")
	 */
	public function deleteAJob(
		string $jobURI,
		Request $request,
		TextUtils $textUtils,
		JobsRepository $jobsRepository,
		TranslatorInterface $translator,
		NotificationsRepository $nr
	) {
		$notificationsData=$nr->getNotificationsData($this->getUser());
		$jobID = TextUtils::getIDFromURI( $jobURI );
		$job   = $jobsRepository->findOneBy( [
			'user'  => $this->getUser(),
			'jobID' => $jobID,
		] );
		if ( $job->getStatus() == 'closed' || $job->getBidPlaced() ) {
			throw new \Exception( 'Job is closed or bid has been placed' );
		}
		if ( ! $job ) {
			throw new \Exception( 'Job no longer exists, has been deleted or is not editable.' );
		} else {
			$em = $this->getDoctrine()->getManager();
			if ( $job ) { //ADD IF WE CAN REMOVE JOB VALIDATOR
				$jobTitle = $job->getTitle();
				$em->remove( $job );
				$em->flush();
				$this->addFlash( 'success', $translator->trans( 'jobs.job_deleted', [ '%title%' => $jobTitle ] ) );

				return $this->redirectToRoute( 'users_jobs' );
			} else {
				throw new \Exception( 'This job can not be removed.' );
			}
		}
	}


	/**
	 * 4 - CLOSE A JOB
	 *
	 * @Security("has_role('ROLE_COMPANY')")
	 * @Route("dashboard/jobs/close/{jobURI}", name="users_company_close_a_job")
	 */
	public function closeAJob(
		string $jobURI,
		Request $request,
		TextUtils $textUtils,
		JobsRepository $jobsRepository,
		TranslatorInterface $translator,
		ContractorJobClosedNotificationService $contractorJobClosedNotificationService,
		NotificationsRepository $nr
	) {
		$notificationsData=$nr->getNotificationsData($this->getUser());
		$jobID = TextUtils::getIDFromURI( $jobURI );
		$job   = $jobsRepository->findOneBy( [
			'user'  => $this->getUser(),
			'jobID' => $jobID,
		] );
		if ( ! $job ) {
			throw new \Exception( 'Job no longer exists, has been deleted or is not editable.' );
		} else {
			$em = $this->getDoctrine()->getManager();
			$job->setStatus( 'closed' );
			$em->persist( $job );
			$em->flush();
			/*SET BID IS NEW STATUS TO FALSE*/
			/** @var $bid Bids */
			foreach($job->getBids() as $bid):
				$bid->setIsNew(false);
				$em->persist($bid);
				$em->flush();
			endforeach;


			/**
			 * JOB CLOSED NOTIFICATION - CONTRACTORS
			 * FOR ALL DISTINCT CONTRACTORS BIDING FOR THIS JOB SEND NOTIFICATION THAT
			 * THE JOB HAS BEEN CLOSED
			 **/
			$jobContractors=$job->getJobBidingContractors();
			if(count($jobContractors)>0){
				foreach($jobContractors as $key=>$contractor){
					/* @var $contractor \App\Entity\Users */
					$contractorJobClosedNotificationService->addNotification($job,$contractor);
				}
			}
			$this->addFlash( 'success', $translator->trans( 'jobs.job_status_updated', [ '%title%' => $job->getTitle() ] ) );





			return $this->redirectToRoute( 'users_jobs' );
		}
	}


}