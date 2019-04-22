<?php

namespace App\Repository\Contracts;

use App\Entity\Contracts;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Services\BrowserServices\BrowserService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;



/**
 * @method Contracts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contracts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contracts[]    findAll()
 * @method Contracts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContractsRepository extends ServiceEntityRepository
{
	private $em;
	private $browser;
	private $browserSettings;
	private $sortKey = 'sort';
	private $sortOrderKey = 'sort_order';
	private $activeFilters;
	public const PAGINATION_NUMBER_OF_ITEMS = 10;


	use ContractRepositoryDataTrait;



	public function __construct( ManagerRegistry $registry, EntityManagerInterface $em, BrowserService $browser ) {
		parent::__construct( $registry, Contracts::class );
		$this->em      = $em;
		$this->browser = $browser;
	}


	/**
	 * @return string
	 */
	public function getSortKey(): string {
		return $this->sortKey;
	}

	/**
	 * @param string $sortKey
	 */
	public function setSortKey( string $sortKey ) {
		$this->sortKey = $sortKey;
	}

	/**
	 * @return string
	 */
	public function getSortOrderKey(): string {
		return $this->sortOrderKey;
	}

	/**
	 * @param string $sortOrderKey
	 */
	public function setSortOrderKey( string $sortOrderKey ) {
		$this->sortOrderKey = $sortOrderKey;
	}


	public function getAllContracts( ) {
		return $this->findAll();
	}


	public function getContractsRequiringCompanyAttention($user) {
		return $this->createQueryBuilder('c')
            ->andWhere('c.company = :user')
            ->andWhere('c.reviewStatus = :status')
            ->andWhere('c.status != :concluded')
            ->setParameter('user', $user)
            ->setParameter('status', 'company')
            ->setParameter('concluded', 'concluded')
            ->orderBy('c.publishDate', 'ASC')
            ->getQuery()
            ->getResult()
		;
	}


	public function getContractsRequiringContractorAttention($user) {
		return $this->findBy(
			[
				'contractor'   => $user,
				'reviewStatus' => 'contractor'
			],
			[
				'publishDate' => 'ASC'
			]
		);
	}






	/**
	 * @return array
	 */
	public function getBrowserSettings(): ?array {
		return $this->browserSettings;
	}


	public function getBrowserConfigurationData( $request ) {
		return $this->browser->getBrowserConfigurationData( $this->browserSettings, $request, $this->getSortKey(),
			$this->getSortOrderKey() );
	}


	/**
	 * @param mixed $browserSettings
	 */
	public function setBrowserSettings( $browserSettings ) {
		$this->browserSettings = $browserSettings;
	}


	public function getContracts( int $page = 1, $request, $user ) {
		return $this->findBy(
			[],
			[ 'deadline' => 'DESC' ]
		);
	}


	/**
	 * @return mixed
	 */
	public function getActiveFilters() {
		return $this->activeFilters;
	}


	/**
	 * @param mixed $activeFilters
	 */
	public function setActiveFilters( $activeFilters ) {
		$this->activeFilters = $activeFilters;
	}


	public function getRouteParams( $request ) {
		$tab     = [];
		$filters = $this->getActiveFilters();
		$data    = [];
		if ( $filters ) {
			$data = array_merge( $filters['linkParams'], $data );
		}
		if ( $request->get( $this->getSortKey() ) ) {
			$tab[ $this->getSortKey() ] = $request->get( $this->getSortKey() );
		}
		if ( $request->get( $this->getSortOrderKey() ) ) {
			$tab[ $this->getSortOrderKey() ] = $request->get( $this->getSortOrderKey() );
		}

		return array_merge( $data, $tab );
	}

	public function getFiltersRouteParams( $request ) {
		$filters = $this->getActiveFilters();
		$data    = [];
		if ( $filters ) {
			$data = array_merge( $filters['linkParams'], $data );
		}

		return $data;
	}









}
