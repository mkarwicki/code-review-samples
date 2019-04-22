<?php


namespace App\Entity;

use App\Entity\Bids;
use App\Entity\JobsSectors;
use App\Entity\Users;
use App\Utils\Text\Slugger;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as CustomAssert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\Jobs\JobsRepository")
 * @ORM\Table(name="jobs")
 *
 */
class Jobs {

	/* Use constants to define configuration options that rarely change.  */
	const PAGINATION_NUMBER_OF_ITEMS = 10;


	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 *
	 */
	private $id;


	/**
	 * @return int
	 */
	public function getId(): int {
		return $this->id;
	}


	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="jobs")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;


	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 *
	 */
	private $status = 'open';


	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 *
	 * @Assert\NotBlank()
	 * @Assert\Length(max=100)
	 *
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 *
	 */
	private $titleSlug;


	/**
	 * @var string
	 *
	 * @ORM\Column(type="text", nullable=true)
	 * @Assert\NotBlank()
	 *
	 * @Assert\Length(max=4000)
	 *
	 */
	private $description;


	/**
	 * @var ?string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 * @Assert\NotBlank()
	 * @Assert\Length(max=150)
	 */
	private $skills;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\DateTime
	 * @Assert\NotBlank()
	 *
	 */
	private $durationFrom;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\DateTime
	 * @Assert\NotBlank()
	 * @Assert\GreaterThanOrEqual(propertyPath="durationFrom")
	 *
	 */
	private $durationTo;


	/**
	 * @var integer
	 *
	 * 1 - Local
	 * 2 - Online
	 *
	 * @ORM\Column(type="integer", nullable=false)
	 * @Assert\NotNull()
	 *
	 */
	private $locationType = false;


	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="locationType",
	 *     fieldValue="1"
	 * )
	 * @Assert\Length(max=100)
	 *
	 */
	private $address;


	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="locationType",
	 *     fieldValue="1"
	 * )
	 *
	 * @Assert\Length(max=50)
	 *
	 */
	private $city;


	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="locationType",
	 *     fieldValue="1"
	 * )
	 * @Assert\Length(max=50)
	 */
	private $district;


	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="locationType",
	 *     fieldValue="1"
	 * )
	 * @Assert\Length(max=50)
	 */
	private $country;


	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="locationType",
	 *     fieldValue="1"
	 * )
	 * @Assert\Length(max=12)
	 */
	private $zip;


	/**
	 * @var integer
	 *
	 * IF value = 2 compact to 2 digits
	 * 2 - Compact to 2 digits ... etc...
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="locationType",
	 *     fieldValue="1"
	 * )
	 */
	private $compactZIP = 0;


	/**
	 * @var integer
	 *
	 * 1 - Hourly job
	 * 2 - Fixed fee job
	 *
	 * @ORM\Column(type="integer", nullable=false)
	 * @Assert\NotBlank()
	 *
	 *
	 */
	private $budgetType = false;

	/**
	 * This is additional placeholder made specificly
	 * for budget/rate search option
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 *
	 */
	private $minBudgetOrRateVal;

	/**
	 * This is additional placeholder made specificly
	 * for budget/rate search option
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 *
	 */
	private $maxBudgetOrRateVal;


	/**
	 * Storing prices as integers
	 * (e.g. 100 = $1 USD) can avoid rounding issues.
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="budgetType",
	 *     fieldValue="1"
	 * )
	 * @Assert\Length(max=5)
	 *
	 */
	private $minHourlyRate;


	/**
	 * Storing prices as integers
	 * (e.g. 100 = $1 USD) can avoid rounding issues.
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 * When budget type selected to hourly job and min hour rate is selected and max hour rate
	 * selected -> then max hourly rate must be greater or equal to min hourly rate
	 * @Assert\Expression(
	 *    "!(this.getBudgetType() == 1 and this.getMinHourlyRate() > 0 and this.getMaxHourlyRate() != false ) or (this.getMinHourlyRate() < this.getMaxHourlyRate())",
	 *    message="This value should be greater then min hourly rate."
	 * )
	 * @Assert\Length(max=5)
	 */
	private $maxHourlyRate;


	/**
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * IF Hourly job selected and current field is selected its value must be grater then 100%
	 * @Assert\Expression(
	 *    "!(this.getBudgetType() == 1 and value !=false and value <= 100) ",
	 *    message="This value should be greater than 100%"
	 *)
	 * @Assert\Length(max=3)
	 */
	private $extraRateOvertime;


	/**
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 *
	 * IF Hourly job selected and field is selected its value must be grater then 100%
	 * @Assert\Expression(
	 *    "!(this.getBudgetType() == 1 and value !=false and value <= 100) ",
	 *    message="This value should be greater than 100%"
	 *)
	 * @Assert\Length(max=3)
	 */
	private $extraRateHoliday;


	/**
	 * Storing prices as integers
	 * (e.g. 100 = $1 USD) can avoid rounding issues.
	 *
	 * @var integer
	 * @ORM\Column(type="integer", nullable=true)
	 * @CustomAssert\NotBlankWhenOtherFieldSelected(
	 *     field="budgetType",
	 *     fieldValue="2"
	 * )
	 * If budget type is set to fixed fee job and max fee is set check that min fee is grater or equal to it.
	 * @Assert\Expression(
	 *    "!(this.getBudgetType() == 2 and this.getMaxFee() > 0 and this.getMinFee() != false ) or (this.getMinFee() < this.getMaxFee())",
	 *    message="This value should be less then max fee."
	 * )
	 * @Assert\Length(max=5)
	 */
	private $minFee;


	/**
	 * Storing prices as integers
	 * (e.g. 100 = $1 USD) can avoid rounding issues.
	 *
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 * @Assert\Length(max=5)
	 */
	private $maxFee;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\DateTime
	 * @Assert\NotBlank()
	 * @Assert\GreaterThan("today")
	 *
	 */
	private $deadline;


	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime")
	 * @Assert\DateTime
	 */
	private $publishDate;


	/**
	 * @var string
	 *
	 * Note: it is being used as unique id for masking the real autoincrement id
	 *
	 * @ORM\Column(type="string")
	 *
	 *
	 */
	private $jobID;


	/**
	 * @ORM\OneToMany(
	 *     targetEntity="App\Entity\JobsSectors",
	 *     mappedBy="job",
	 *     orphanRemoval=true,
	 *     cascade={"persist"})
	 */
	private $jobsSectors;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Bids", mappedBy="job")
	 */
	private $bids;


	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $bidsCount = 0;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $minBid = 0;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $maxBid = 0;


	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $lastBidValue = 0;

	/**
	 * @return int
	 */
	public function getLastBidValue(): ?int {
		return $this->lastBidValue;
	}

	/**
	 * @param int $lastBidValue
	 */
	public function setLastBidValue( ?int $lastBidValue ) {
		$this->lastBidValue = $lastBidValue;
	}


	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $bidPlaced = 0;


	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $jobAccepted = 0;


	/**
	 * @var integer
	 *
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $jobContractConcluded = 0;


	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $acceptedBidID = 0;

	/**
	 * @var integer
	 *
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $acceptedUserID = 0;

	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\Contracts", mappedBy="job", cascade={"persist", "remove"})
	 */
	private $contract;

	/**
	 * @return int
	 */
	public function getJobAccepted(): ?int {
		return $this->jobAccepted;
	}

	/**
	 * @param int $jobAccepted
	 */
	public function setJobAccepted( ?int $jobAccepted ) {
		$this->jobAccepted = $jobAccepted;
	}

	/**
	 * @return int
	 */
	public function getAcceptedBidID(): ?int {
		return $this->acceptedBidID;
	}

	/**
	 * @param int $acceptedBidID
	 */
	public function setAcceptedBidID( ?int $acceptedBidID ) {
		$this->acceptedBidID = $acceptedBidID;
	}

	/**
	 * @return int
	 */
	public function getAcceptedUserID(): ?int {
		return $this->acceptedUserID;
	}

	/**
	 * @param int $acceptedUserID
	 */
	public function setAcceptedUserID( ?int $acceptedUserID ) {
		$this->acceptedUserID = $acceptedUserID;
	}


	/**
	 * @return int
	 */
	public function getMinBid(): ?int {
		return $this->minBid;
	}

	/**
	 * @param int $minBid
	 */
	public function setMinBid( ?int $minBid ) {
		$this->minBid = $minBid;
	}

	/**
	 * @return int
	 */
	public function getMaxBid(): ?int {
		return $this->maxBid;
	}

	/**
	 * @param int $maxBid
	 */
	public function setMaxBid( ?int $maxBid ) {
		$this->maxBid = $maxBid;
	}

	/**
	 * @return int
	 */
	public function getBidPlaced(): ?int {
		return $this->bidPlaced;
	}

	/**
	 * @param int $bidPlaced
	 */
	public function setBidPlaced( ?int $bidPlaced ) {
		$this->bidPlaced = $bidPlaced;
	}


	/**
	 * @return int
	 */
	public function getBidsCount(): ?int {
		return $this->bidsCount ? $this->bidsCount : 0;
	}

	/**
	 * @param int $bidsCount
	 */
	public function setBidsCount( ?int $bidsCount ) {
		$this->bidsCount = $bidsCount;
	}


	public function __construct() {
		$this->bids        = new ArrayCollection();
		$this->jobsSectors = new ArrayCollection();
	}


	/**
	 * @return string
	 */
	public function getJobID(): ?string {
		return $this->jobID;
	}

	/**
	 * @param string $jobID
	 */
	public function setJobID( string $jobID ) {
		$this->jobID = $jobID;
	}

	public function generateJobID() {
		return 'J-' . $this->id . substr( md5( strtotime( 'now' ) ), 0, 5 );
	}

	/**
	 * @return \DateTime
	 */
	public function getPublishDate(): \DateTime {
		return $this->publishDate;
	}

	/**
	 * @param \DateTime $publishDate
	 */
	public function setPublishDate( \DateTime $publishDate ) {
		$this->publishDate = $publishDate;
	}


	/**
	 * @return string
	 */
	public function getStatus(): string {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus( string $status ) {
		$this->status = $status;
	}


	/**
	 * @return \DateTime
	 */
	public function getDeadline(): ?\DateTime {
		return $this->deadline;
	}


	/**
	 * @param \DateTime $deadline
	 */
	public function setDeadline( ?\DateTime $deadline ) {
		$this->deadline = $deadline;
	}


	/**
	 * @return string
	 */
	public function getDescription(): ?string {
		return $this->description;
	}


	/**
	 * @param string $description
	 */
	public function setDescription( ?string $description ) {
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getSkills(): ?string {
		return $this->skills;
	}


	/**
	 * @param string $skills
	 */
	public function setSkills( string $skills ) {
		$this->skills = $skills;
	}


	/**
	 * @return \DateTime
	 */
	public function getDurationFrom(): ?\DateTime {
		return $this->durationFrom;
	}


	/**
	 * @param \DateTime $durationFrom
	 */
	public function setDurationFrom( ?\DateTime $durationFrom ) {
		$this->durationFrom = $durationFrom;
	}


	/**
	 * @return \DateTime
	 */
	public function getDurationTo(): ?\DateTime {
		return $this->durationTo;
	}


	/**
	 * @param \DateTime $durationTo
	 */
	public function setDurationTo( ?\DateTime $durationTo ) {
		$this->durationTo = $durationTo;
	}


	/**
	 * @return int
	 */
	public function getLocationType(): ?int {
		return $this->locationType;
	}


	/**
	 * @param int $locationType
	 */
	public function setLocationType( ?int $locationType ) {
		$this->locationType = $locationType;
	}


	/**
	 * @return string
	 */
	public function getAddress(): ?string {
		return $this->address;
	}


	/**
	 * @param string $address
	 */
	public function setAddress( string $address ) {
		$this->address = $address;
	}

	/**
	 * @return string
	 */
	public function getCity(): ?string {
		return $this->city;
	}

	/**
	 * @param string $city
	 */
	public function setCity( string $city ) {
		$this->city = $city;
	}

	/**
	 * @return string
	 */
	public function getDistrict(): ?string {
		return $this->district;
	}

	/**
	 * @param string $district
	 */
	public function setDistrict( string $district ) {
		$this->district = $district;
	}

	/**
	 * @return string
	 */
	public function getCountry(): ?string {
		return $this->country;
	}

	/**
	 * @param string $country
	 */
	public function setCountry( string $country ) {
		$this->country = $country;
	}

	/**
	 * @return string
	 */
	public function getZip(): ?string {
		return $this->zip;
	}

	/**
	 * @param string $zip
	 */
	public function setZip( string $zip ) {
		$this->zip = $zip;
	}

	/**
	 * @return int
	 */
	public function getCompactZIP(): ?int {
		return $this->compactZIP;
	}

	/**
	 * @param int $compactZIP
	 */
	public function setCompactZIP( int $compactZIP ) {
		$this->compactZIP = $compactZIP;
	}

	/**
	 * @return int
	 */
	public function getBudgetType(): ?int {
		return $this->budgetType;
	}

	/**
	 * @param int $budgetType
	 */
	public function setBudgetType( ?int $budgetType ) {
		$this->budgetType = $budgetType;
	}

	/**
	 * @return int
	 */
	public function getMinHourlyRate(): ?int {
		return $this->minHourlyRate;
	}

	/**
	 * @param int $minHourlyRate
	 */
	public function setMinHourlyRate( ?int $minHourlyRate ) {
		$this->minHourlyRate = $minHourlyRate;
	}

	/**
	 * @return int
	 */
	public function getMinBudgetOrRateVal(): ?int {
		return $this->minBudgetOrRateVal;
	}

	/**
	 * @param int $minBudgetOrRateVal
	 */
	public function setMinBudgetOrRateVal( ?int $minBudgetOrRateVal ) {
		$this->minBudgetOrRateVal = $minBudgetOrRateVal;
	}

	/**
	 * @return int
	 */
	public function getMaxBudgetOrRateVal(): ?int {
		return $this->maxBudgetOrRateVal;
	}

	/**
	 * @param int $maxBudgetOrRateVal
	 */
	public function setMaxBudgetOrRateVal( ?int $maxBudgetOrRateVal ) {
		$this->maxBudgetOrRateVal = $maxBudgetOrRateVal;
	}






	/**
	 * @return int
	 */
	public function getMaxHourlyRate(): ?int {
		return $this->maxHourlyRate;
	}

	/**
	 * @param int $maxHourlyRate
	 */
	public function setMaxHourlyRate( int $maxHourlyRate ) {
		$this->maxHourlyRate = $maxHourlyRate;
	}

	/**
	 * @return int
	 */
	public function getExtraRateOvertime(): ?int {
		return $this->extraRateOvertime;
	}

	/**
	 * @param int $extraRateOvertime
	 */
	public function setExtraRateOvertime( int $extraRateOvertime ) {
		$this->extraRateOvertime = $extraRateOvertime;
	}

	/**
	 * @return int
	 */
	public function getExtraRateHoliday(): ?int {
		return $this->extraRateHoliday;
	}

	/**
	 * @param int $extraRateHoliday
	 */
	public function setExtraRateHoliday( int $extraRateHoliday ) {
		$this->extraRateHoliday = $extraRateHoliday;
	}

	/**
	 * @return int
	 */
	public function getMinFee(): ?int {
		return $this->minFee;
	}

	/**
	 * @param int $minFee
	 */
	public function setMinFee( int $minFee ) {
		$this->minFee = $minFee;
	}

	/**
	 * @return int
	 */
	public function getMaxFee(): ?int {
		return $this->maxFee;
	}

	/**
	 * @param int $maxFee
	 */
	public function setMaxFee( int $maxFee ) {
		$this->maxFee = $maxFee;
	}

	/**
	 * @return string
	 */
	public function getTitle(): ?string {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle( string $title ) {
		$this->title = $title;
	}


	/**
	 * @return string
	 */
	public function getTitleSlug(): ?string {
		return $this->titleSlug;
	}

	/**
	 * @param string $titleSlug
	 */
	public function setTitleSlug() {
		$this->titleSlug = Slugger::slugify( $this->title );
	}

	public function getUriSlug() {
		return $this->titleSlug . '-' . $this->jobID;
	}


	public function getBudgetOrRate(): string {
		$out = '';
		if ( $this->budgetType == 1 ) { // Hourly job
			if ( $this->maxHourlyRate ) {
				$out .= $this->minHourlyRate . '-' . $this->maxHourlyRate;
			} else {
				$out .= $this->minHourlyRate;
			}
		} elseif ( $this->budgetType == 2 ) { // Fixed fee job
			if ( $this->maxFee ) {
				$out .= $this->minFee . '-' . $this->maxFee;
			} else {
				$out .= $this->minFee;
			}
		}
		return $out.$this->getBudgetOrRateAbbrev();
	}

	public function getBudgetOrRateAbbrev(): string {
		$out = '';
		if ( $this->budgetType == 1 ) { // Hourly job
			$out .= ' €/h';
		} elseif ( $this->budgetType == 2 ) { // Fixed fee job
			$out .= ' €';
		}
		return $out;
	}






	public function getUser(): ?Users {
		return $this->user;
	}


	public function setUser( ?Users $user ): self {
		$this->user = $user;

		return $this;
	}


	/**
	 * @return Collection|JobsSectors[]
	 */
	public function getJobsSectors(): Collection {
		return $this->jobsSectors;
	}


	public function addJobsSectors( JobsSectors $jobsSector ): self {
		if ( ! $this->jobsSectors->contains( $jobsSector ) ) {
			$this->jobsSectors[] = $jobsSector;
			$jobsSector->setJob( $this );
		}

		return $this;
	}


	public function removeJobsSectors( JobsSectors $jobsSector ): self {
		if ( $this->jobsSectors->contains( $jobsSector ) ) {
			$this->jobsSectors->removeElement( $jobsSector );
			// set the owning side to null (unless already changed)
			if ( $jobsSector->getJob() === $this ) {
				$jobsSector->setJob( null );
			}
		}

		return $this;
	}

	/**
	 * @return Collection|Bids[]
	 */
	public function getBids(): Collection {
		return $this->bids;
	}


	public function getJobBidingContractors() {
		$tab=[];
		if(count($this->bids)>0){
			foreach($this->bids as $bid){
				/* @var $bid \App\Entity\Bids */
				$tab[$bid->getUser()->getId()]=$bid->getUser();
			}
		}
		return $tab;
	}




	public function addBid( Bids $bid ): self {
		if ( ! $this->bids->contains( $bid ) ) {
			$this->bids[] = $bid;
			$bid->setJob( $this );
		}

		return $this;
	}


	public function removeBid( Bids $bid ): self {
		if ( $this->bids->contains( $bid ) ) {
			$this->bids->removeElement( $bid );
			// set the owning side to null (unless already changed)
			if ( $bid->getJob() === $this ) {
				$bid->setJob( null );
			}
		}

		return $this;
	}


	public function updateMinMaxBids() {
		$min = 9999999999;
		$max = 0;
		foreach ( $this->getBids() as $bid ) {
			$min = Min( $min, $bid->getRate() );
			$max = Max( $max, $bid->getRate() );
		}
		if ( $min < 9999999999 ) {
			$this->setMinBid( $min );
		} else {
			$this->setMinBid( 0 );
		}
		$this->setMaxBid( $max );
	}

	public function updateLastBidValue() {
		$k = 0;
		foreach ( $this->getBids() as $bid ) {
			$k = $bid->getRate();
		}
		$this->setLastBidValue( $k );
	}


	public function getUserMaxJobBid( $user ) {
		$max = 0;
		foreach ( $this->getBids() as $bid ) {
			if ( $bid->getUser() == $user ) {
				$max = max( $max, $bid->getRate() );
			}
		}
		return $max;
	}


	public function getUserLastJobBid( $user ) {
		$bids=[];
		foreach ( $this->getBids() as $bid ) {
			if ( $bid->getUser() == $user ) {
				$bids[$bid->getPublishDate()->getTimestamp()]=$bid;
			}
		}
		return $bids[(max(array_keys($bids)))];
	}



	public function getNewBidsNumber() {
		$i = 0;
		foreach ( $this->getBids() as $bid ) {
			if ( $bid->isNew() ) {
				$i ++;
			}
		}

		return $i;
	}


	public function isJobRelatedToUser( ?Users $user ) {
		if ( ! $user ) {
			return false;
		}
		if ( $this->getUser() == $user ) {
			return true;
		}
		foreach ( $this->getBids() as $bid ) {
			if ( $bid->getUser() == $user ) {
				return true;
			}
		}

		return false;
	}


	public function getAcceptedJobBidRate() {

		foreach ( $this->getBids() as $bid ) {

			if ( $bid->getId() == $this->acceptedBidID ) {
				return $bid->getRate();

			}

		}


	}

	/**
	 * @return int
	 */
	public function getJobContractConcluded(): ?int {
		return $this->jobContractConcluded;
	}

	/**
	 * @param int $jobContractConcluded
	 */
	public function setJobContractConcluded( ?int $jobContractConcluded ) {
		$this->jobContractConcluded = $jobContractConcluded;
	}


	public function setMinMaxBudgetOrRateValue(){
		/**
		 * HOUR RATE
		 */
		if($this->getBudgetType() == 1){
			$this->setMinBudgetOrRateVal($this->minHourlyRate);
			$this->setMaxBudgetOrRateVal($this->maxHourlyRate);
		}
		/**
		 * FIXED FEE
		 */
		if($this->getBudgetType() == 2){
			$this->setMinBudgetOrRateVal($this->minFee);
			$this->setMaxBudgetOrRateVal($this->maxFee);
		}
	}



	public function getContract(): ?Contracts {
		return $this->contract;
	}

	public function setContract( Contracts $contract ): self {
		$this->contract = $contract;

		// set the owning side of the relation if necessary
		if ( $this !== $contract->getJob() ) {
			$contract->setJob( $this );
		}

		return $this;
	}


	public function __toString() {

		return $this->title;

	}


}
