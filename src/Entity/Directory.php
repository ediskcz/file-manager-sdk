<?php

namespace Edisk\FileManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(),
 * @ORM\Table(
 *     name="directory",
 *     indexes={
 *         @ORM\Index(name="user_id", columns={"user_id"}),
 *     }
 * )
 */
class Directory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private string $name;

    /**
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private int $userId;

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
