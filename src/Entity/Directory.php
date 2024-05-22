<?php

namespace Edisk\FileManager\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'directory')]
#[ORM\Index(columns: ['user_id'], name: 'user_id')]
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\MappedSuperclass]
abstract class Directory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 100, nullable: false)]
    protected string $name;

    #[ORM\Column(name: 'user_id', type: 'integer', nullable: false)]
    protected int $userId;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
