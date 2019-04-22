<?php

namespace App\Repository\Contracts;

use App\Entity\Contracts;
use App\Services\BrowserServices\BrowserService;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

/**
 * @method Contracts|null find( $id, $lockMode = null, $lockVersion = null )
 * @method Contracts|null findOneBy( array $criteria, array $orderBy = null )
 * @method Contracts[]    findAll()
 * @method Contracts[]    findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null )
 */
class ContractorsContractsRepository extends ContractsRepository {
	private $em;
	private $browser;
	private $browserSettings = [
		'filters_settings' => [
			[
				'title'             => 'Status',
				'slug'              => 'status',
				'query_entity_slug' => 'c',
				'type'              => 'alternative_search',
				'db_field'          => 'status'
			],
			[
				'title'             => 'In review by',
				'slug'              => 'in_review_by',
				'company'           =>  false,
				'query_entity_slug' => 'c',
				'type'              => 'alternative_search',
				'db_field'          => 'reviewStatus'
			]
		],
		'sort_settings'    => [
			[
				'slug'              => 'mo',  /*FIRST ELEMENT IN ARRAY IS A DEFAULT ONE*/
				'type'              => 'same_class',
				'query_entity_slug' => 'c',
				'title'             => 'Modified on',
				'db_key'            => 'modificationDate',
				'sortOrderNames'    => [
					'asc'  => 'Nearest',
					'desc' => 'Farthest',
				]
			],
			[
				'slug'              => 'co',
				'type'              => 'same_class',
				'query_entity_slug' => 'c',
				'title'             => 'Created on',
				'db_key'            => 'publishDate',
				'sortOrderNames'    => [
					'asc'  => 'Newest',
					'desc' => 'Oldest',
				]
			],
			[
				'slug'              => 'status',
				'type'              => 'alternative',
				'query_entity_slug' => 'c',
				'title'             => 'Status',
				'db_key'            => 'status',
				'sortOrderNames'    => [
					'desc' => 'Desc',
					'asc'  => 'Asc',
				]
			],
			[
				'slug'              => 'irb',
				'type'              => 'idle',
				'query_entity_slug' => 'c',
				'title'             => 'In review by',
			],
		]
	];


	public function __construct( ManagerRegistry $registry, EntityManagerInterface $em, BrowserService $browser ) {
		parent::__construct( $registry, $em, $browser );
		$this->em      = $em;
		$this->browser = $browser;
		$this->setBrowserSettings( $this->browserSettings );
	}


	public function getContracts( int $page = 1, $request, $user ): Pagerfanta {
		$order         = $this->browser->getSortOrder(
			$request,
			$this->getBrowserSettings()['sort_settings'],
			$this->getSortKey(),
			$this->getSortOrderKey()
		);

		$activeFilters = $this->browser->getActiveFilters(
			$request,
			$this->getBrowserSettings()['filters_settings']
		);


		$this->setActiveFilters( $activeFilters );
		if ( $activeFilters ) {


			$contracts = $this->getEntityManager()
              ->createQuery( '
                SELECT c
                FROM App\\Entity\\Contracts c 
                ' . $activeFilters['where'] . ' 
                AND c.contractor = :userID
                ORDER BY ' . $order . '
            ' );
			foreach ( $activeFilters['params'] as $key => $parameter ) {
				$contracts->setParameter( $parameter['key'], $parameter['value'] );
			}
			$contracts->setParameter( 'userID', $user->getID() );
		} else {
			$contracts = $this->getEntityManager()
			                  ->createQuery( '
                SELECT c
                FROM App\\Entity\\Contracts c 
                WHERE c.contractor = :userID
                ORDER BY ' . $order . '
            ' );
			$contracts->setParameter( 'userID', $user->getID() );
		}
		$pagerfanta = $this->browser->getPaginatedResults( $contracts, $page, parent::PAGINATION_NUMBER_OF_ITEMS );

		return $pagerfanta;
	}


}
