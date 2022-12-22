<?php

declare(strict_types=1);
namespace In2code\Powermail\Domain\Model;

use DateTime;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class User extends AbstractEntity
{
    const TABLE_NAME = 'fe_users';

    protected string $username = '';
    protected string $password = '';
    protected string $name = '';
    protected string $firstName = '';
    protected string $middleName = '';
    protected string $lastName = '';
    protected string $address = '';
    protected string $telephone = '';
    protected string $fax = '';
    protected string $email = '';
    protected string $title = '';
    protected string $zip = '';
    protected string $city = '';
    protected string $country = '';
    protected string $www = '';
    protected string $company = '';
    protected ?DateTime $lastlogin = null;
    protected ?DateTime $crdate = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Powermail\Domain\Model\Usergroup>
     */
    protected ObjectStorage $usergroup;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected ObjectStorage $image;

    public function __construct(string $username = '', string $password = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->usergroup = new ObjectStorage();
        $this->image = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->usergroup = $this->usergroup ?? new ObjectStorage();
        $this->image = $this->image ?? new ObjectStorage();
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setUsergroup(ObjectStorage $usergroup): self
    {
        $this->usergroup = $usergroup;
        return $this;
    }

    public function addUsergroup(Usergroup $usergroup): self
    {
        $this->usergroup->attach($usergroup);
        return $this;
    }

    public function removeUsergroup(Usergroup $usergroup): self
    {
        $this->usergroup->detach($usergroup);
        return $this;
    }

    public function getUsergroup(): ObjectStorage
    {
        return $this->usergroup;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setMiddleName(string $middleName): self
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setFax($fax): self
    {
        $this->fax = $fax;
        return $this;
    }

    public function getFax(): string
    {
        return $this->fax;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setZip(string $zip): self
    {
        $this->zip = $zip;
        return $this;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setWww(string $www): self
    {
        $this->www = $www;
        return $this;
    }

    public function getWww(): string
    {
        return $this->www;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setImage(ObjectStorage $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): ObjectStorage
    {
        return $this->image;
    }

    public function setLastlogin(DateTime $lastlogin): self
    {
        $this->lastlogin = $lastlogin;
        return $this;
    }

    public function getLastlogin(): ?DateTime
    {
        return $this->lastlogin;
    }

    /**
     * Calculated values
     */

    /**
     * Try to get a readable name in format "lastname, firstname" (if possible)
     *
     * @param string $splitCharacter
     * @return string
     */
    public function getReadableName(string $splitCharacter = ', '): string
    {
        $name = $this->getName();
        if ($this->getLastName() !== '' && $this->getFirstName() !== '') {
            $name = $this->getLastName() . $splitCharacter . $this->getFirstName();
        }
        return $name;
    }

    /**
     * @return FileReference|null
     */
    public function getFirstImage(): ?FileReference
    {
        $images = $this->getImage();
        if ($images !== null) {
            foreach ($images as $image) {
                return $image;
            }
        }
        return null;
    }

    /**
     * @return DateTime|null
     */
    public function getCrdate(): ?DateTime
    {
        return $this->crdate;
    }

    /**
     * @param DateTime $crdate
     * @return User
     */
    public function setCrdate(DateTime $crdate): self
    {
        $this->crdate = $crdate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValidEmail(): bool
    {
        return $this->getEmail() !== '' && GeneralUtility::validEmail($this->getEmail());
    }
}
