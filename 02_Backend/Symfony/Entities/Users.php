<?php


namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * This is  custom user entity used to
 * handle both Companies, Contractors and Admin users.
 *
 * We do not setup any assert regulations here
 * because it depends specifically to the user role
 * and is now controlled in for each user type
 * in forms domain (bossiness logic).
 *
 *
 * @ORM\Entity(repositoryClass="App\Repository\Users\UsersRepository")
 * @ORM\Table(name="users")
 *
 */
class Users implements AdvancedUserInterface, \Serializable {
	/**
	 * @var int
	 *
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $fullName;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $companyName;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 *
	 *
	 */
	private $username;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", unique=true)
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @var array
	 *
	 * @ORM\Column(type="json")
	 */
	private $roles = [];


	/**
	 * @var boolean
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $isActive = false;

	/**
	 * Random string sent to the user email address in order to verify it.
	 *
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $confirmationToken;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\DateTime
	 */
	private $passwordRequestedAt;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\DateTime
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @Assert\Length(max=300)
	 */
	private $skills;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 * @Assert\Length(max=2000)
	 *
	 */
	private $about;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $photo;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(type="datetime", nullable=true)
	 * @Assert\DateTime
	 */
	private $dateOfBirth;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $signature;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $address;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $city;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $zip;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $district;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $country;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $cellPhone;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $phone;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $fax;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 *
	 */
	private $vat;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $taxID;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $bank;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $accountOwner;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $account;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $bic;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $iban;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 *
	 */
	private $branch;


	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Jobs", mappedBy="user")
	 */
	private $jobs;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\UsersSectors", mappedBy="user" , cascade={"persist"})
	 */
	private $usersSectors;


	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Bids", mappedBy="user")
	 */
	private $bids;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Ads", mappedBy="user")
	 */
	private $ads;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Contracts", mappedBy="company")
	 */
	private $companyContracts;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Contracts", mappedBy="contractor")
	 */
	private $contractorsContracts;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Notifications", mappedBy="contractor")
	 */
	private $contractorNotifications;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Notifications", mappedBy="company")
	 */
	private $companyNotifications;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\UserNotificationsSettings", mappedBy="user")
	 */
	private $notificationsSettings;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $emailAddressInBid = true;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $phoneNumberInBid = true;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $blocked = false;


	public function __construct() {
		$this->jobs                    = new ArrayCollection();
		$this->usersSectors            = new ArrayCollection();
		$this->bids                    = new ArrayCollection();
		$this->ads                     = new ArrayCollection();
		$this->companyContracts        = new ArrayCollection();
		$this->contractorsContracts    = new ArrayCollection();
		$this->contractorNotifications = new ArrayCollection();
		$this->companyNotifications    = new ArrayCollection();
		$this->notificationsSettings   = new ArrayCollection();
	}


	/**
	 * @return mixed
	 */
	public function getPhoto() {
		return $this->photo;
	}

	/**
	 * @param mixed $photo
	 */
	public function setPhoto( $photo ) {
		$this->photo = $photo;
	}

	/**
	 * @return mixed
	 */
	public function getAbout() {
		return $this->about;
	}

	/**
	 * @param mixed $about
	 */
	public function setAbout( $about ) {
		$this->about = $about;
	}

	/**
	 * @return string
	 */
	public function getCompanyName(): ?string {
		return $this->companyName;
	}

	/**
	 * @param string $companyName
	 */
	public function setCompanyName( ?string $companyName ) {
		$this->companyName = $companyName;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime {
		return $this->createdAt;
	}

	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt( \DateTime $createdAt ) {
		$this->createdAt = $createdAt;
	}

	/**
	 * @return bool
	 */
	public function isActive(): ?bool {
		return $this->isActive;
	}

	/**
	 * @param bool $isActive
	 */
	public function setIsActive( ?bool $isActive ) {
		$this->isActive = $isActive;
	}

	public function getId(): int {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getConfirmationToken(): string {
		return $this->confirmationToken;
	}

	/**
	 * @param string $confirmationToken
	 */
	public function setConfirmationToken( string $confirmationToken ) {
		$this->confirmationToken = $confirmationToken;
	}

	public function setFullName( ?string $fullName ): void {
		$this->fullName = $fullName;
	}

	public function getFullName(): ?string {
		return $this->fullName;
	}

	public function getUsername(): string {
		return $this->username;
	}

	public function setUsername( string $username ): void {
		$this->username = $username;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function setEmail( string $email ): void {
		$this->email = $email;
	}

	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword( ?string $password ): void {
		if ( strlen( $password ) > 0 ) {
			$this->password = $password;
		}
	}

	/**
	 * @return \DateTime
	 */
    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth(?\DateTime $dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @param mixed $district
     */
    public function setDistrict($district)
    {
        $this->district = $district;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getCellPhone()
    {
        return $this->cellPhone;
    }

    /**
     * @param mixed $cellPhone
     */
    public function setCellPhone($cellPhone)
    {
        $this->cellPhone = $cellPhone;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param mixed $vat
     */
    public function setVat($vat)
    {
        $this->vat = $vat;
    }

    /**
     * @return mixed
     */
    public function getTaxID()
    {
        return $this->taxID;
    }

    /**
     * @param mixed $taxID
     */
    public function setTaxID($taxID)
    {
        $this->taxID = $taxID;
    }

    /**
     * @return mixed
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param mixed $bank
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
    }

    /**
     * @return mixed
     */
    public function getAccountOwner()
    {
        return $this->accountOwner;
    }

    /**
     * @param mixed $accountOwner
     */
    public function setAccountOwner($accountOwner)
    {
        $this->accountOwner = $accountOwner;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param mixed $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return mixed
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @param mixed $bic
     */
    public function setBic($bic)
    {
        $this->bic = $bic;
    }

    /**
     * @return mixed
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param mixed $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }


    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }


    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }


    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }


    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * {@inheritdoc}
	 */
	public function getSalt(): ?string {
         		// See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
         		// we're using bcrypt in security.yml to encode the password, so
         		// the salt value is built-in and you don't have to generate one

         		return null;
         	}


	/**
	 * {@inheritdoc}
	 */
	public function setPasswordRequestedAt( \DateTime $date = null ) {
         		$this->passwordRequestedAt = $date;

         		return $this;
         	}

	/**
	 * Gets the timestamp that the user requested a password reset.
	 *
	 * @return null|\DateTime
	 */
	public function getPasswordRequestedAt() {
         		return $this->passwordRequestedAt;
         	}


	/**
	 * {@inheritdoc}
	 */
	public function isPasswordRequestNonExpired( $ttl ) {
         		return $this->getPasswordRequestedAt() instanceof \DateTime &&
         		       $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
         	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * {@inheritdoc}
	 */
	public function eraseCredentials(): void {
         		// if you had a plainPassword property, you'd nullify it here
         		// $this->plainPassword = null;
         	}

	/**
	 * @return mixed
	 */
	public function getSkills() {
         		return $this->skills;
         	}

	/**
	 * @param mixed $skills
	 */
	public function setSkills( $skills ) {
         		$this->skills = $skills;
         	}

	/**
	 * @return mixed
	 */
	public function getBranch() {
         		return $this->branch;
         	}

	/**
	 * @param mixed $branch
	 */
	public function setBranch( $branch ) {
         		$this->branch = $branch;
         	}


	/**
	 *
	 * @return null|string
	 */
	public function getFullAddressString(): ?string {
         		$addressElements = [
         			$this->getAddress(),
         			$this->getCity(),
         			$this->getZip(),
         			$this->getDistrict(),
         			$this->getCountry()
         		];

         		return \App\Utils\Text\TextUtils::transformStringArrayToCommaSeparatedValues( $addressElements );
         	}


	/**
	 * {@inheritdoc}
	 */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->username, $this->password, $this->isActive]);
    }

	/**
	 * {@inheritdoc}
	 */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password, $this->isActive] = unserialize($serialized,
            ['allowed_classes' => false]);
    }

	/**
	 * @return Collection|Jobs[]
	 */
	public function getJobs(): Collection {
        return $this->jobs;
    }

	public function addJob( Jobs $job ): self {
        if (!$this->jobs->contains($job)) {
            $this->jobs[] = $job;
            $job->setUser($this);
        }

        return $this;
    }

    public function removeJob(Jobs $job): self
    {
        if ($this->jobs->contains($job)) {
            $this->jobs->removeElement($job);
            // set the owning side to null (unless already changed)
            if ($job->getUser() === $this) {
                $job->setUser(null);
            }
        }

        return $this;
    }


	/**
	 * @return Collection|UsersSectors[]
	 */
	public function getUsersSectors(): Collection {
        return $this->usersSectors;
    }


    public function addUsersSector(UsersSectors $usersSector): self
    {
        if (!$this->usersSectors->contains($usersSector)) {
            $this->usersSectors[] = $usersSector;
            $usersSector->setUser($this);
        }

        return $this;
    }


    public function removeUsersSector(UsersSectors $usersSector): self
    {
        if ($this->usersSectors->contains($usersSector)) {
            $this->usersSectors->removeElement($usersSector);
            // set the owning side to null (unless already changed)
            if ($usersSector->getUser() === $this) {
                $usersSector->setUser(null);
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

    public function addBids(Bids $bids): self
    {
        if (!$this->bids->contains($bids)) {
            $this->bids[] = $bids;
            $bids->setUser($this);
        }

        return $this;
    }

    public function removeBids(Bids $bids): self
    {
        if ($this->bids->contains($bids)) {
            $this->bids->removeElement($bids);
            // set the owning side to null (unless already changed)
            if ($bids->getUser() === $this) {
                $bids->setUser(null);
            }
        }

        return $this;
    }

	/**
	 * @return Collection|Ads[]
	 */
	public function getAds(): Collection {
         		return $this->ads;
    }

    public function addAd(Ads $ad): self
    {
        if (!$this->ads->contains($ad)) {
            $this->ads[] = $ad;
            $ad->setUser($this);
        }

        return $this;
    }

    public function removeAd(Ads $ad): self
    {
        if ($this->ads->contains($ad)) {
            $this->ads->removeElement($ad);
            // set the owning side to null (unless already changed)
            if ($ad->getUser() === $this) {
                $ad->setUser(null);
            }
        }

        return $this;
    }

	/**
	 * @return Collection|Contracts[]
	 */
	public function getcompanyContracts(): Collection {
        return $this->companyContracts;
    }

    public function addCompanyContract(Contracts $companyContract): self
    {
        if (!$this->companyContracts->contains($companyContract)) {
            $this->companyContracts[] = $companyContract;
            $companyContract->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyContract(Contracts $companyContract): self
    {
        if ($this->companyContracts->contains($companyContract)) {
            $this->companyContracts->removeElement($companyContract);
            // set the owning side to null (unless already changed)
            if ($companyContract->getCompany() === $this) {
                $companyContract->setCompany(null);
            }
        }

        return $this;
    }

	/**
	 * @return Collection|Contracts[]
	 */
	public function getContractorsContracts(): Collection {
        return $this->contractorsContracts;
    }

    public function addContractorsContract(Contracts $contractorsContract): self
    {
        if (!$this->contractorsContracts->contains($contractorsContract)) {
            $this->contractorsContracts[] = $contractorsContract;
            $contractorsContract->setContractor($this);
        }

        return $this;
    }

    public function removeContractorsContract(Contracts $contractorsContract): self
    {
        if ($this->contractorsContracts->contains($contractorsContract)) {
            $this->contractorsContracts->removeElement($contractorsContract);
            // set the owning side to null (unless already changed)
            if ($contractorsContract->getContractor() === $this) {
                $contractorsContract->setContractor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notifications[]
     */
    public function getContractorNotifications(): Collection
    {
        return $this->contractorNotifications;
    }

    public function addContractorNotification(Notifications $contractorNotification): self
    {
        if (!$this->contractorNotifications->contains($contractorNotification)) {
            $this->contractorNotifications[] = $contractorNotification;
            $contractorNotification->setContractor($this);
        }

        return $this;
    }

    public function removeContractorNotification(Notifications $contractorNotification): self
    {
        if ($this->contractorNotifications->contains($contractorNotification)) {
            $this->contractorNotifications->removeElement($contractorNotification);
            // set the owning side to null (unless already changed)
            if ($contractorNotification->getContractor() === $this) {
                $contractorNotification->setContractor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notifications[]
     */
    public function getCompanyNotifications(): Collection
    {
        return $this->companyNotifications;
    }

    public function addCompanyNotification(Notifications $companyNotification): self
    {
        if (!$this->companyNotifications->contains($companyNotification)) {
            $this->companyNotifications[] = $companyNotification;
            $companyNotification->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyNotification(Notifications $companyNotification): self
    {
        if ($this->companyNotifications->contains($companyNotification)) {
            $this->companyNotifications->removeElement($companyNotification);
            // set the owning side to null (unless already changed)
            if ($companyNotification->getCompany() === $this) {
                $companyNotification->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserNotificationsSettings[]
     */
    public function getNotificationsSettings(): Collection
    {
        return $this->notificationsSettings;
    }

    public function addNotificationsSetting(UserNotificationsSettings $notificationsSetting): self
    {
        if (!$this->notificationsSettings->contains($notificationsSetting)) {
            $this->notificationsSettings[] = $notificationsSetting;
            $notificationsSetting->setUser($this);
        }

        return $this;
    }

    public function removeNotificationsSetting(UserNotificationsSettings $notificationsSetting): self
    {
        if ($this->notificationsSettings->contains($notificationsSetting)) {
            $this->notificationsSettings->removeElement($notificationsSetting);
            // set the owning side to null (unless already changed)
            if ($notificationsSetting->getUser() === $this) {
                $notificationsSetting->setUser(null);
            }
        }

        return $this;
    }


    public function getUserNotificationSettingStatus($notificationKey)
    {
        /* @var $setting \App\Entity\UserNotificationsSettings */
        foreach ($this->notificationsSettings as $setting) {
            if ($setting->getType() == $notificationKey && $setting->getActive()) {
                return true;
            }
        }

        return false;
    }

    public function getEmailAddressInBid(): ?bool
    {
        return $this->emailAddressInBid;
    }

    public function setEmailAddressInBid(bool $emailAddressInBid): self
    {
        $this->emailAddressInBid = $emailAddressInBid;

        return $this;
    }

    public function getPhoneNumberInBid(): ?bool
    {
        return $this->phoneNumberInBid;
    }

    public function setPhoneNumberInBid(?bool $phoneNumberInBid): self
    {
        $this->phoneNumberInBid = $phoneNumberInBid;

        return $this;
    }

    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(?bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }






}
