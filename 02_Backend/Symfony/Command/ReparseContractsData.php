<?php
/**
 * Created by PhpStorm.
 * User: Michał
 * Date: 2018-09-12
 * Time: 11:36
 */

namespace App\Command;


use App\Repository\Contracts\ContractsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReparseContractsData
 * php bin/console app:reparse-contract-overridden-data
 *
 * @package App\Command
 */
class ReparseContractsData extends Command {

	private $contractsRepository;
	private $em;

	public function __construct(ContractsRepository $contractsRepository, EntityManagerInterface $em)
	{
		$this->contractsRepository = $contractsRepository;
		$this->em = $em;
		parent::__construct();
	}


	protected function configure() {
		$this
			->setName('app:reparse-contract-overridden-data')
			->setDescription('Reparse contract overridden data.')
			->setHelp('This command allows you to add new fields to the specific contracts')
		;
	}


	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$contracts=$this->contractsRepository->getAllContracts();
		$counter=0;
		foreach ( $contracts as $contract ) {
			$job  = $contract->getJob();
			$jsonData = json_encode($this->contractsRepository->getOverriddenData( $contract ));
			$contract->setContractTemporaryOverrides($jsonData);
			$contract->setContractOverrides($jsonData);
			$this->em->persist( $contract );
			$this->em->flush();
			$counter++;
		}


		$output->writeln('Recalculated contracts: '.$counter);
		$output->writeln('');
	}









}