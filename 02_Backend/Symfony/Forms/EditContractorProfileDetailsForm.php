<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-07-18
 * Time: 13:20
 */

namespace App\Forms\Users\Contractors\EditProfileDetailsForm;


use App\Utils\Text\TextUtils;
use Doctrine\Common\Proxy\Exception\UnexpectedValueException;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\FormInterface;
use App\Entity\Users;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;



class EditContractorProfileDetailsForm extends Controller {
	private $em;
	private $encoder;
	private $textUtils;



	public function __construct(EntityManagerInterface $em,UserPasswordEncoderInterface $encoder, TextUtils $textUtils) {
		$this->em = $em;
		$this->encoder = $encoder;
		$this->textUtils = $textUtils;
	}

	/**
	 * Update contractor profile details data form settings
	 *
	 * @param Request $request
	 * @param Controller $controller
	 * @param Users $user
	 *
	 * @return \Symfony\Component\Form\FormInterface
	 */
	public function getEditContractorProfileDetailsForm( Request $request, Controller $controller, Users $user ) {
		if(!$user) {
			throw new UnexpectedValueException('No user to update');
		}
		/*DATE OF BIRTH SETUP*/
		$dateOfBirthSetup['label']='Date of birth';
		$dateOfBirthSetup['widget']='single_text';
		if($user->getDateOfBirth()){
			$dateOfBirthSetup['data']=$user->getDateOfBirth();
		}
		$form = $controller->createFormBuilder()
		/* EMAIL */
		->add('email', TextType::class,[
				'label' => 'Email',
				'data'=>$user->getEmail(),
				'attr'=> [
					'disabled'=>'true',
					'value'=> $user->getEmail()
				]
			]
		)
		/* USERNAME */
		->add('username', TextType::class,[
				'label' => 'Username',
				'data'=>$user->getUsername(),
				'attr'=> [
					'disabled'=>'true',
					'value'=> $user->getUsername()
				]
			]
		)
		/* FIRST NAME LAST NAME */
		->add('fullName', TextType::class,[
				'label' => 'First name, last name',
				'data'=>$user->getFullName(),
				'attr'=> [
					'disabled'=>'true',
					'value'=> $user->getFullName()
				],
				'constraints' => [
					new Assert\Length( [
						'max'        => 50,
					] ),
				],
			]
		)
		/* DATE OF BIRTH */
		->add('dateOfBirth', BirthdayType::class,
			$dateOfBirthSetup
		)
		/* SIGNATURE */
		->add('signature', FileType::class,[
				'label' => 'Signature (JPG file)',
				'constraints' => [
					new Assert\File([
						'maxSize' => '10240k',
						'mimeTypes'=> [
							'image/jpeg'
						],
						'mimeTypesMessage' => 'Please upload a valid JPEG',
					])
				]
			]
		)
		/* STREET, BUILDING, LOCAL */
		->add('address', TextType::class,[
				'label' => 'Street, building, local',
				'data'=>$user->getAddress(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 100,
					] ),
				],
			]
		)
		/* CITY/TOWN */
		->add('city', TextType::class,[
				'label' => 'City/town',
				'data'=>$user->getCity(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 50,
					] ),
				],
			]
		)
		/* ZIP */
		->add('zip', TextType::class,[
				'label' => 'ZIP',
				'data'=>$user->getZip(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 12,
					] ),
				],
			]
		)
		/* DISTRICT/PROVINCE */
		->add('district', TextType::class,[
				'label' => 'District/Province',
				'data'=>$user->getDistrict(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 50,
					] ),
				],
			]
		)
		/* COUNTRY */
		->add('country', TextType::class,[
				'label' => 'Country',
				'data'=>$user->getCountry(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 50,
					] ),
				],
			]
		)
		/* CELL PHONE */
		->add('cellPhone', TelType::class,[
				'label' => 'Cell phone',
				'data'=>$user->getCellPhone(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 20,
					] ),
				],
			]
		)
		/* PHONE */
		->add('phone', TelType::class,[
				'label' => 'Phone',
				'data'=>$user->getPhone(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 20,
					] ),
				],
			]
		)
		/* FAX */
		->add('fax', TextType::class,[
				'label' => 'Fax',
				'data'=>$user->getFax(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 20,
					] ),
				],
			]
		)
		/* VAT */
		->add('vat', ChoiceType::class,[
				'label' => 'VAT payer',
				'data'=>$user->getVat(),
				'choices'=>[
					'Yes' => true,
					'No' => false,
				]
			]
		)
		/* TAX ID */
		->add('taxID', TextType::class,[
				'label' => 'Tax ID',
				'data'=>$user->getTaxID(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 20,
					] ),
				],
			]
		)
		/* BANK */
		->add('bank', TextType::class,[
				'label' => 'Bank',
				'data'=>$user->getBank(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 50,
					] ),
				],
			]
		)
		/* ACCOUNT OWNER */
		->add('accountOwner', TextType::class,[
				'label' => 'Account owner',
				'data'=>$user->getAccountOwner(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 7,
					] ),
				],
			]
		)
		/* ACCOUNT */
		->add('account', TextType::class,[
				'label' => 'Account',
				'data'=>$user->getAccount(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 35,
					] ),
				],
			]
		)
		/* BIC */
		->add('bic', TextType::class,[
				'label' => 'BIC',
				'data'=>$user->getBic(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 15,
					] ),
				],
			]
		)
		/* IBAN */
		->add('iban', TextType::class,[
				'label' => 'IBAN',
				'data'=>$user->getIban(),
				'constraints' => [
					new Assert\Length( [
						'max'        => 35,
					] ),
				],

			]
		)
		/*SAVE BUTTON*/
		->add( 'save_changes', SubmitType::class,
		[
			'attr' => [
				'class' => 'btn btn-primary btn-lg'
			]
		] )
		->getForm();
		$form->handleRequest( $request );
		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->updateContractorProfileDetails($form,$user);
		}
		return $form;
	}



	private function updateContractorProfileDetails(FormInterface $form,Users $user){
		/** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
		$file=$form->getData()['signature'];
		if($file){
			$fileName = $this->textUtils->getRandomString().'.'.$file->guessExtension();
			if(strlen($user->getSignature())>0){
				$oldFilePath='users/signatures/'.$user->getSignature();
				if(file_exists($oldFilePath)){
					unlink($oldFilePath);
				}
			}
			$file->move(
				'users/signatures/',
				$fileName
			);
			$user->setSignature($fileName);
		}
		if($form->getData()['dateOfBirth']){
			$user->setDateOfBirth($form->getData()['dateOfBirth']);
		}
		$user->setAddress($form->getData()['address']);
		$user->setCity($form->getData()['city']);
		$user->setZip($form->getData()['zip']);
		$user->setDistrict($form->getData()['district']);
		$user->setCountry($form->getData()['country']);
		$user->setCellPhone($form->getData()['cellPhone']);
		$user->setPhone($form->getData()['phone']);
		$user->setFax($form->getData()['fax']);
		$user->setVat($form->getData()['vat']);
		$user->setTaxID($form->getData()['taxID']);
		$user->setBank($form->getData()['bank']);
		$user->setAccountOwner($form->getData()['accountOwner']);
		$user->setAccount($form->getData()['account']);
		$user->setBic($form->getData()['bic']);
		$user->setIban($form->getData()['iban']);
		$this->em->persist( $user );
		$this->em->flush();
	}




}