<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-07-06
 * Time: 09:06
 */

namespace App\Validator\Constraints;

use App\Entity\Users;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;

class UsernameValidatedStringValidator extends ConstraintValidator {
	private $em;
	private $params;

	public function __construct(EntityManagerInterface $em,ParameterBagInterface $params) {
		$this->em = $em;
		$this->params = $params;
	}

	public function validate($value, Constraint $constraint)
	{
		// custom constraints should ignore null and empty values to allow
		// other constraints (NotBlank, NotNull, etc.) take care of that
		if (null === $value || '' === $value) {
			return;
		}

		if (!is_string($value)) {
			throw new UnexpectedTypeException($value, 'string');
		}
		$testArray=$footerPages = $this->params->get('sign_up')['username_constraints'];
		$test=false;
		foreach($testArray as $key=>$valToCompare){
			if(stripos($value, $valToCompare) !== false){
				$test = $valToCompare;
				break;
			}
		}
		if($test){
			$this->context->buildViolation($constraint->message)
	          ->setParameter('{{ string }}', $test)
	          ->addViolation();
		}
	}

}
