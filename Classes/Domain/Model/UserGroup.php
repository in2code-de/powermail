<?php

declare(strict_types=1);

namespace In2code\Powermail\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Usergroup extends AbstractEntity
{
    const TABLE_NAME = 'fe_groups';

    protected string $title = '';
    protected string $description = '';

    /**
     * @var ObjectStorage<Usergroup>
     */
    protected ObjectStorage $subgroup;

    public function __construct(string $title = '')
    {
        $this->setTitle($title);
        $this->subgroup = new ObjectStorage();
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

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setSubgroup(ObjectStorage $subgroup): self
    {
        $this->subgroup = $subgroup;
        return $this;
    }

    public function addSubgroup(Usergroup $subgroup): self
    {
        $this->subgroup->attach($subgroup);
        return $this;
    }

    public function removeSubgroup(Usergroup $subgroup): self
    {
        $this->subgroup->detach($subgroup);
        return $this;
    }

    public function getSubgroup(): ObjectStorage
    {
        return $this->subgroup;
    }
}
