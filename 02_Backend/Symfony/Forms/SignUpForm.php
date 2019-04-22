/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-07-06
 * Time: 06:56
 */

namespace App\Forms\Visitors\SignUp;


use App\Entity\UsersSectors;
use App\Entity\Users;
use App\Repository\Sectors\Qualifications\QualificationsRepository;
use App\Repository\Sectors\SectorsRepository;
use App\Repository\Users\UsersRepository;
use App\Services\EmailServices\SignUpEmail;
use App\Utils\Text\TextUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;

/**
 * Class to setup and validate sign up forms
 *
 * At the time writing this comment We can have
 * 2 types of users (contractors and companies)
 * So far both form setup and validation is taken
 * care of here. If we would like to have more then 2
 * types of user it would be wise to move this into
 * individual class'es
 *
 *
 * Class SignUpForm
 * @package App\Forms\Visitors\SignUp
 */
class SignUpForm extends Controller {
	private $em;
	private $encoder;
	private $signUpEmail;
	private $textUtils;
	private $sectorsRepository;
	private $qualificationsRepository;
	private $usersRepository;

	/*INCJECT ENTITY MANAGER AND USER PASSWORD ENCODER SERVICES*/
	public function __construct(
		EntityManagerInterface $em,
		UserPasswordEncoderInterface $encoder,
		SignUpEmail $signUpEmail,
		TextUtils $textUtils,
		SectorsRepository $sectorsRepository,
		QualificationsRepository $qualificationsRepository,
		UsersRepository $usersRepository
	) {
		$this->em                       = $em;
		$this->encoder                  = $encoder;
		$this->signUpEmail              = $signUpEmail;
		$this->textUtils = $textUtils;
		$this->sectorsRepository = $sectorsRepository;
		$this->qualificationsRepository = $qualificationsRepository;
		$this->usersRepository = $usersRepository;
	}

	/**
	 * Setups contractors sign up form fields and validations
	 *
	 * @param Request $request
	 * @param Controller $controller
	 *
	 * @return \Symfony\Component\Form\FormInterface
	 */
	public function getContractorForm( Request $request, Controller $controller, $notificationSettings ) {

		$sectors        = $this->sectorsRepository->getChoiceFieldData();
		$qualifications = $this->qualificationsRepository->getChoiceFieldData();


		/*
			WE CAN PASS ARRAY WITH DEFAULT DATA TO
			FORM BUILDER IF WE WANT TO SET IN EXAMPLE SOME PLACEHOLDERS
		*/
		//$form = $controller->createFormBuilder()
		$form = $controller->container->get( 'form.factory' )->createNamedBuilder( 'Contractor' )
			/* EMAIL FIELD */
            ->add( 'email', EmailType::class, [
				'constraints' => [
					new Assert\NotBlank(),
					//*CUSTOM VALIDATION CHECK IF GIVEN FILED IS UNIQUE IN USERS DATABASE TABLE*/
					new CustomAssert\IsUniqueUserField( [
						'field' => 'email'
					] ),
					new Assert\Email( [
						'message' => 'The email "{{ value }}" is not a valid email.',
					] ),
					new Assert\Length( [
						'max'        => 50,
					] ),
				]
			] )
			/* USER NAME FIELD */
			->add( 'userName', TextType::class, [
				'constraints' => [
					new Assert\NotBlank(),
					//*CUSTOM VALIDATION CHECK IF GIVEN FILED IS UNIQUE IN USERS DATABASE TABLE*/
					new CustomAssert\IsUniqueUserField( [
						'field' => 'username'
					] ),
					new Assert\Length( [
						'min'        => 5,
						'max'        => 25,
						'minMessage' => 'User name must be at lest {{ limit }} characters long',
						'maxMessage' => 'User name cannot be longer then {{ limit }} characters',
					] ),
					new Assert\Regex( [
						'pattern'    => "/^[a-zA-Z0-9]*$/",
						'match'      => true,
						'message' => 'User name must contain only letters and numbers',
					] ),
					new CustomAssert\UsernameValidatedString( [
						'field' => 'username'
					] ),
				]
			] )
			/* PASSWORD AND REPEAT PASSWORD FIELD */
			->add( 'password', RepeatedType::class, [
				'type'            => PasswordType::class,
				'invalid_message' => 'The password fields must match',
				'first_options'   => [
					'label'       => 'Password',
					'constraints' => [
						new Assert\NotBlank(),
						new Assert\Length( [
							'min'        => 8,
							'max'        => 30,
							'minMessage' => 'Password must be at lest {{ limit }} characters long',
							'maxMessage' => 'Password must cannot be longer then {{ limit }} characters',
						] ),
					]
				],
				'second_options'  => [
					'label'       => 'Repeat Password',
					'constraints' => [
						new Assert\NotBlank(),
						new Assert\Length( [
							'min'        => 8,
							'max'        => 30,
							'minMessage' => 'Repeat Password must be at lest {{ limit }} characters long',
							'maxMessage' => 'Repeat Password must cannot be longer then {{ limit }} characters',
						] ),
					]
				],
			] )
			/* FIRST NAME, LAST NAME FIELD */
			->add( 'firstNameLastName', TextType::class, [
				'label'       => 'First name, last name',
				'constraints' => [
					new Assert\NotBlank(),
					new Assert\Length( [
						'min'        => 5,
						'max'        => 50,
						'minMessage' => 'First name, last name field must be at lest {{ limit }} characters long',
						'maxMessage' => 'First name, last name cannot be longer then {{ limit }} characters',
					] ),
				]
			] )
			/* SECTOR FIELD */
			->add( 'sector', ChoiceType::class, [
				'label' => 'global.sector',
				'help' => 'You can add more later',
				'choices'     =>
					array_merge(
						[ 'Choose' => null ], $sectors
					)
				,
				'constraints' => [
					new Assert\NotBlank(),
				]
			] )
			/* QUALIFICATIONS FIELD */
			->add( 'qualifications', ChoiceType::class, [
				'label' => 'global.sector_qualifications',
				'choices'     =>
					array_merge(
						[ 'Choose' => null ], $qualifications
					)
				,
				'constraints' => [
					new Assert\NotBlank(),
				]
			] )
			/* CONDITION 1 FIELD */
			->add( 'condition1', CheckboxType::class, array(
				'label'       => 'I confirm that the above is true and lorem ipsum dolor.',
				'constraints' => [
					new Assert\IsTrue(),
				]
			) )
			/* CONDITION 2 FIELD */
			 ->add( 'condition2', CheckboxType::class, array(
				'label'       => 'I accept service Terms and conditions and Privacy policy',
				'constraints' => [
					new Assert\IsTrue(),
				]
			) )
			/* SIGN UP BUTTON */
			->add( 'signUp', SubmitType::class,
				[
					'attr' => [
						'class' => 'btn btn-primary btn-lg submit-sign-up-button'
					]
				] )
		     ->getForm();


		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			//FORM VALIDATION SUCCESS - WE CAN ADD USER*/
			$this->addContractorUser( $form, $notificationSettings );
		}

		return $form;
	}


	/**
	 * Setups company sign up form fileds and validations
	 *
	 * @param Request $request
	 * @param Controller $controller
	 *
	 * @return \Symfony\Component\Form\FormInterface
	 */
	public function getCompanyForm( Request $request, Controller $controller, $notificationSettings ) {
		/*
			WE CAN PASS ARRAY WITH DEFAULT DATA TO
			FORM BUILDER IF WE WANT TO SET IN EXAMPLE SOME PLACEHOLDERS
		*/
		$form = $controller->container->get( 'form.factory' )->createNamedBuilder( 'Company' )
			/* EMAIL FIELD */
			->add( 'email', EmailType::class, [
				'constraints' => [
					new Assert\NotBlank(),
					//*CUSTOM VALIDATION CHECK IF GIVEN FILED IS UNIQUE IN USERS DATABASE TABLE*/
					new CustomAssert\IsUniqueUserField( [
						'field' => 'email'
					] ),
					new Assert\Email( [
						'message' => 'The email "{{ value }}" is not a valid email.',
					] ),
				]
			] )
			/* COMPANY NAME FIELD */
			->add( 'companyName', TextType::class, [
				'constraints' => [
					new Assert\NotBlank(),
					new Assert\Length( [
						'min'        => 3,
						'max'        => 30,
						'minMessage' => 'Company name, field must be at lest {{ limit }} characters long',
						'maxMessage' => 'First name, last name cannot be longer then {{ limit }} characters',
					] ),
				]
			] )
			/* USER NAME FIELD */
			->add( 'userName', TextType::class, [
				'constraints' => [
					new Assert\NotBlank(),
					//*CUSTOM VALIDATION CHECK IF GIVEN FILED IS UNIQUE IN USERS DATABASE TABLE*/
					new CustomAssert\IsUniqueUserField( [
						'field' => 'username'
					] ),
					new Assert\Length( [
						'min'        => 5,
						'max'        => 25,
						'minMessage' => 'User name must be at lest {{ limit }} characters long',
						'maxMessage' => 'User name cannot be longer then {{ limit }} characters',
					] ),
					new Assert\Regex( [
						'pattern'    => "/^[a-zA-Z0-9]*$/",
						'match'      => true,
						'message' => 'User name must contain only letters and numbers',
					] ),
					new CustomAssert\UsernameValidatedString( [
						'field' => 'username'
					] ),
				]
			] )
			/* PASSWORD AND REPEAT PASSWORD FIELD */
			->add( 'password', RepeatedType::class, [
				'type'            => PasswordType::class,
				'invalid_message' => 'The password fields must match',
				'first_options'   => [
					'label'       => 'Password',
					'constraints' => [
						new Assert\NotBlank(),
						new Assert\Length( [
							'min'        => 8,
							'max'        => 30,
							'minMessage' => 'Password must be at lest {{ limit }} characters long',
							'maxMessage' => 'Password must cannot be longer then {{ limit }} characters',
						] ),
					]
				],
				'second_options'  => [
					'label'       => 'Repeat Password',
					'constraints' => [
						new Assert\NotBlank(),
						new Assert\Length( [
							'min'        => 8,
							'max'        => 30,
							'minMessage' => 'Repeat Password must be at lest {{ limit }} characters long',
							'maxMessage' => 'Repeat Password must cannot be longer then {{ limit }} characters',
						] ),
					]
				],
			] )
			/* CONDITION 2 FIELD */
			->add( 'condition2', CheckboxType::class, array(
				'label'       => 'I accept service Terms and conditions and Privacy policy',
				'constraints' => [
					new Assert\IsTrue(),
				]
			) )
			/* SIGN UP BUTTON */
			->add( 'signUp', SubmitType::class,
				[
					'attr' => [
						'class' => 'btn btn-primary btn-lg submit-sign-up-button'
					]
				] )
			->getForm();

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			//FORM VALIDATION SUCESS - WE CAN ADD USER*/
			$this->addCompanyUser( $form, $notificationSettings );
		}

		return $form;
	}


	/**
	 * ADDS CONTRACTORS USER AND EXECUTES SEND VALIDATION EMAIL
	 *
	 * To consider :: move it to the users repository class or user class?
	 *
	 * @param FormInterface $form
	 */
	private function addContractorUser( FormInterface $form, $notificationSettings ) {
		$formData = $form->getData();
		$secret   = $this->textUtils->getRandomString(); // USER SECRET FOR EMAIL VALIDATION
		$user     = new Users();
		{  // USER DATA SEGMENT
			$user->setEmail( $formData['email'] );
			$user->setUsername( $formData['userName'] );
			/*WE ENCRYPT USER PASSWORD USING BCRYPT WITCH HAS BUILT IN SALT FUNCTION SO WE DO NOT NEED CUSTOM SALT*/
			$user->setPassword( $this->encoder->encodePassword( $user, $formData['password'] ) );
			$user->setFullName( $formData['firstNameLastName'] );
			$user->setRoles( [ 'ROLE_USER', 'ROLE_CONTRACTOR' ] );
			$user->setCreatedAt( new \DateTime() );
			$user->setConfirmationToken( $secret );
			$user->serialize();

		}
		/*SET USER SECTOR*/
		{
			$userSector     = new UsersSectors();
			$sector         = $this->sectorsRepository->findOneBy( [ 'id' => $formData['sector'] ] );
			$qualifications = $this->qualificationsRepository->findOneBy( [ 'id' => $formData['qualifications'] ] );
			$userSector->setSectors( $sector );
			$userSector->setQualifications( $qualifications );
			$userSector->setUser( $user );
			$user->addUsersSector( $userSector );
		}

		/*UPDATE USER DATA*/
		$this->em->persist( $user );
		$this->em->flush();
		/*SETUP NOTIFICATIONS*/
		$this->usersRepository->setAllUserNotificationsToTrue($user, $notificationSettings);




		/*SEND VALIDATION EMAIL WITH SECRET*/
		$this->signUpEmail->setUser( $user );
		$this->signUpEmail->setTo($formData['email']);
		$this->signUpEmail->sendEmail();
	}

	/**
	 * ADDS COMPANY USER AND EXECUTES SEND VALIDATION EMAIL
	 *
	 * To consider :: move it to the users repository class or user class?
	 * or replace it with method like parseUserDataForNewCompanyUser
	 * and then add a method addCompanyUser in user repository class
	 *
	 * @param FormInterface $form
	 */
	private function addCompanyUser( FormInterface $form, $notificationSettings ) {
		$formData = $form->getData();
		$secret   = $this->textUtils->getRandomString(); // USER SECRET FOR EMAIL VALIDATION
		$user     = new Users();
		{  // USER DATA SEGMENT
			$user->setEmail( $formData['email'] );
			$user->setUsername( $formData['userName'] );
			/*WE ENCRYPT USER PASSWORD USING BCRYPT WITCH HAS BUILT IN SALT FUNCTION SO WE DO NOT NEED CUSTOM SALT*/
			$user->setPassword( $this->encoder->encodePassword( $user, $formData['password'] ) );
			$user->setCompanyName( $formData['companyName'] );
			$user->setRoles( [ 'ROLE_USER', 'ROLE_COMPANY' ] );
			$user->setCreatedAt( new \DateTime() );
			$user->setConfirmationToken( $secret );
			$user->serialize();
		}
		/*UPDATE USER DATA*/
		$this->em->persist( $user );
		$this->em->flush();
		/*SETUP NOTIFICATIONS*/
		$this->usersRepository->setAllUserNotificationsToTrue($user, $notificationSettings);
		/*SEND VALIDATION EMAIL WITH SECRET*/
		$this->signUpEmail->setUser( $user );
		$this->signUpEmail->setTo($formData['email']);
		$this->signUpEmail->sendEmail();
	}


}
