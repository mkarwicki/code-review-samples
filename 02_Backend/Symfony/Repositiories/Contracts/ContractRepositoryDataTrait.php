<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-19
 * Time: 10:19
 */

namespace App\Repository\Contracts;


use App\Entity\Contracts;
use App\Entity\Jobs;


/**
 * THIS TRAIT IS USED TO MAP CONTRACT FIELDS WITH VARIOUS PROPERTIES
 *
 *
 * Trait ContractRepositoryDataTrait
 *
 * @package App\Repository\Contracts
 */
trait ContractRepositoryDataTrait {


	public function getOverriddenData( Contracts $contract ): array {
		$data = [];
		$company    = $contract->getCompany();
		$contractor = $contract->getContractor();
		$bid        = $contract->getBid();
		$job        = $contract->getJob();
		/**
		 * LOCAL JOB
		 */
		if($job->getLocationType() == 1){
			/**
			 * HOUR RATE LOCAL JOB
			 */
			if($job->getBudgetType() == 1){
				$data['conclusionDate']        = new \DateTime();
				$data['city']                  = $job->getCity();
				$data['companyName']           = $company->getCompanyName();
				$data['companyRepresentative'] = $company->getFullName();
				$data['contractorName']        = $contractor->getFullName();
				$data['contractorAddress']     = $contractor->getFullAddressString();
				$data['contractorTaxID']       = $contractor->getTaxID();
				$data['jobTitle']              = $job->getTitle();
				$data['jobDescription']        = $job->getDescription();
				$data['jobLocation']           = $job->getCity();
				$data['hourlyRate']            = $bid->getRate();
				$data['extraRateOvertime']     = $job->getExtraRateOvertime();
				$data['extraRateHoliday']      = $job->getExtraRateHoliday();
				$data['contractFrom']          = $job->getDurationFrom();
				$data['contractTo']            = $job->getDurationTo();
				$data['companyNote']           = '';
				$data['companyNoteDate']       = new \DateTime();
			}
			/**
			 * FIXED FEE LOCAL JOB
			 */
			if($job->getBudgetType() == 2){
				$data['conclusionDate']        = new \DateTime();
				$data['city']                  = $job->getCity();
				$data['companyName']           = $company->getCompanyName();
				$data['companyRepresentative'] = $company->getFullName();
				$data['contractorName']        = $contractor->getFullName();
				$data['contractorAddress']     = $contractor->getFullAddressString();
				$data['contractorTaxID']       = $contractor->getTaxID();
				$data['jobTitle']              = $job->getTitle();
				$data['jobDescription']        = $job->getDescription();
				$data['jobLocation']           = $job->getCity();
				$data['contractRate']          = $bid->getRate();
				$data['contractFrom']          = $job->getDurationFrom();
				$data['contractTo']            = $job->getDurationTo();
				$data['companyNote']           = '';
				$data['companyNoteDate']       = new \DateTime();
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
				$data['conclusionDate']        = new \DateTime();
				$data['city']                  = $job->getCity();
				$data['companyName']           = $company->getCompanyName();
				$data['companyRepresentative'] = $company->getFullName();
				$data['contractorName']        = $contractor->getFullName();
				$data['contractorAddress']     = $contractor->getFullAddressString();
				$data['contractorTaxID']       = $contractor->getTaxID();
				$data['jobTitle']              = $job->getTitle();
				$data['jobDescription']        = $job->getDescription();
				$data['hourlyRate']            = $bid->getRate();
				$data['extraRateOvertime']     = $job->getExtraRateOvertime();
				$data['extraRateHoliday']      = $job->getExtraRateHoliday();
				$data['contractFrom']          = $job->getDurationFrom();
				$data['contractTo']            = $job->getDurationTo();
				$data['companyNote']           = '';
				$data['companyNoteDate']       = new \DateTime();
			}
			/**
			 * FIXED FEE ONLINE JOB
			 */
			if($job->getBudgetType() == 2){
				$data['conclusionDate']        = new \DateTime();
				$data['city']                  = $job->getCity();
				$data['companyName']           = $company->getCompanyName();
				$data['companyRepresentative'] = $company->getFullName();
				$data['contractorName']        = $contractor->getFullName();
				$data['contractorAddress']     = $contractor->getFullAddressString();
				$data['contractorTaxID']       = $contractor->getTaxID();
				$data['jobTitle']              = $job->getTitle();
				$data['jobDescription']        = $job->getDescription();
				$data['contractRate']          = $bid->getRate();
				$data['contractFrom']          = $job->getDurationFrom();
				$data['contractTo']            = $job->getDurationTo();
				$data['companyNote']           = '';
				$data['companyNoteDate']       = new \DateTime();
			}
		}
		return $data;
	}


	public function getOverriddenDataFromFrom(array $formData, Jobs $job): array {
		$data = [];
		/**
		 * LOCAL JOB
		 */
		if($job->getLocationType() == 1){
			/**
			 * HOUR RATE LOCAL JOB
			 */
			if($job->getBudgetType() == 1) {
				$data['conclusionDate']        = $formData['conclusionDate'];
				$data['city']                  = $formData['city'];
				$data['companyName']           = $formData['companyName'];
				$data['companyRepresentative'] = $formData['companyRepresentative'];
				$data['contractorName']        = $formData['contractorName'];
				$data['contractorAddress']     = $formData['contractorAddress'];
				$data['contractorTaxID']       = $formData['contractorTaxID'];
				$data['jobTitle']              = $formData['jobTitle'];
				$data['jobDescription']        = $formData['jobDescription'];
				$data['jobLocation']           = $formData['jobLocation'];
				$data['hourlyRate']            = $formData['hourlyRate'];
				$data['extraRateOvertime']     = $formData['extraRateOvertime'];
				$data['extraRateHoliday']      = $formData['extraRateHoliday'];
				$data['contractFrom']          = $formData['contractFrom'];
				$data['contractTo']            = $formData['contractTo'];
				$data['companyNote']           = $formData['companyNote'];
				$data['companyNoteDate']       = new \DateTime();
			}
			/**
			 * FIXED FEE LOCAL JOB
			 */
			if($job->getBudgetType() == 2){
				$data['conclusionDate']        = $formData['conclusionDate'];
				$data['city']                  = $formData['city'];
				$data['companyName']           = $formData['companyName'];
				$data['companyRepresentative'] = $formData['companyRepresentative'];
				$data['contractorName']        = $formData['contractorName'];
				$data['contractorAddress']     = $formData['contractorAddress'];
				$data['contractorTaxID']       = $formData['contractorTaxID'];
				$data['jobTitle']              = $formData['jobTitle'];
				$data['jobDescription']        = $formData['jobDescription'];
				$data['jobLocation']           = $formData['jobLocation'];
				$data['contractRate']          = $formData['contractRate'];
				$data['contractFrom']          = $formData['contractFrom'];
				$data['contractTo']            = $formData['contractTo'];
				$data['companyNote']           = $formData['companyNote'];
				$data['companyNoteDate']       = new \DateTime();
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
				$data['conclusionDate']        = $formData['conclusionDate'];
				$data['city']                  = $formData['city'];
				$data['companyName']           = $formData['companyName'];
				$data['companyRepresentative'] = $formData['companyRepresentative'];
				$data['contractorName']        = $formData['contractorName'];
				$data['contractorAddress']     = $formData['contractorAddress'];
				$data['contractorTaxID']       = $formData['contractorTaxID'];
				$data['jobTitle']              = $formData['jobTitle'];
				$data['jobDescription']        = $formData['jobDescription'];
				$data['hourlyRate']            = $formData['hourlyRate'];
				$data['extraRateOvertime']     = $formData['extraRateOvertime'];
				$data['extraRateHoliday']      = $formData['extraRateHoliday'];
				$data['contractFrom']          = $formData['contractFrom'];
				$data['contractTo']            = $formData['contractTo'];
				$data['companyNote']           = $formData['companyNote'];
				$data['companyNoteDate']       = new \DateTime();
			}
			/**
			 * FIXED FEE ONLINE JOB
			 */
			if($job->getBudgetType() == 2){
				$data['conclusionDate']        = $formData['conclusionDate'];
				$data['city']                  = $formData['city'];
				$data['companyName']           = $formData['companyName'];
				$data['companyRepresentative'] = $formData['companyRepresentative'];
				$data['contractorName']        = $formData['contractorName'];
				$data['contractorAddress']     = $formData['contractorAddress'];
				$data['contractorTaxID']       = $formData['contractorTaxID'];
				$data['jobTitle']              = $formData['jobTitle'];
				$data['jobDescription']        = $formData['jobDescription'];
				$data['contractRate']          = $formData['contractRate'];
				$data['contractFrom']          = $formData['contractFrom'];
				$data['contractTo']            = $formData['contractTo'];
				$data['companyNote']           = $formData['companyNote'];
				$data['companyNoteDate']       = new \DateTime();

			}
		}
		return $data;
	}




}