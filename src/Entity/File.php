<?php

namespace Edisk\FileManager\Entity;

use DateTimeImmutable;
use Edisk\FileManager\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'file')]
#[ORM\Index(columns: ['url'], name: 'url')]
#[ORM\Index(columns: ['search', 'filename'], name: 'search', flags: ['fulltext'])]
#[ORM\Index(columns: ['directory_id'], name: 'directory_id')]
#[ORM\Index(columns: ['user_id'], name: 'user_id')]
#[ORM\Index(columns: ['create_date'], name: 'create_date')]
#[ORM\Index(columns: ['storage_id'], name: 'storage_id')]
#[ORM\Entity(repositoryClass: FileRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\MappedSuperclass]
abstract class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'import_service', type: 'string', length: 50, nullable: true, options: ['default' => null])]
    protected ?string $importService;

    #[ORM\Column(name: 'import_id', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $importId;

    #[ORM\Column(name: 'url', type: 'string', length: 200, nullable: false)]
    protected string $url;

    #[ORM\Column(name: 'path', type: 'string', length: 200, nullable: true, options: ['default' => null])]
    protected ?string $path;

    #[ORM\Column(name: 'storage_id', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $storageId;

    #[ORM\Column(name: 'create_date', type: 'datetime_immutable', nullable: false)]
    protected DateTimeImmutable $createDate;

    #[ORM\Column(name: 'user_id', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $userId;

    #[ORM\Column(name: 'directory_id', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $directoryId;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    protected string $filename;

    #[ORM\Column(name: 'filesize', type: 'bigint', nullable: false)]
    protected string $filesize;

    #[ORM\Column(type: 'string', length: 10, nullable: false)]
    protected string $extension;

    #[ORM\Column(type: 'text', length: 65535, nullable: true, options: ['default' => null])]
    protected ?string $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: ['default' => null])]
    protected ?string $search;

    #[ORM\Column(name: 'thumbs_up', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $thumbsUp = 0;

    #[ORM\Column(name: 'thumbs_down', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $thumbsDown = 0;

    #[ORM\Column(name: 'processed', type: 'smallint', nullable: false, options: ['default' => 0])]
    protected bool $processed = false;

    #[ORM\Column(name: 'deleted', type: 'smallint', nullable: false, options: ['default' => 0])]
    protected bool $deleted = false;

    #[ORM\Column(name: 'delete_date', type: 'datetime_immutable', nullable: true, options: ['default' => null])]
    protected ?DateTimeImmutable $deleteDate;

    #[ORM\Column(name: 'deleted_storage', type: 'smallint', nullable: false, options: ['default' => 0])]
    protected bool $deletedStorage = false;

    #[ORM\Column(name: 'views', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $views = 0;

    #[ORM\Column(name: 'downloads', type: 'integer', nullable: false, options: ['default' => 0])]
    protected int $downloads = 0;

    /**
     * @var null|Directory
     */
    #[ORM\JoinColumn(name: 'directory_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Directory::class, fetch: 'LAZY')]
    protected ?Directory $directory;

    #[ORM\Column(name: 'mime_type', type: 'string', length: 255, nullable: true, options: ['default' => null])]
    protected ?string $mimeType;

    /**
     * @var null|DateTimeImmutable
     */
    #[ORM\Column(name: 'last_update_date', type: 'datetime_immutable', nullable: true, options: ['default' => null])]
    protected ?DateTimeImmutable $lastUpdateDate;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getImportService(): ?string
    {
        return $this->importService;
    }

    public function setImportService(?string $importService): void
    {
        $this->importService = $importService;
    }

    public function getImportId(): ?int
    {
        return $this->importId;
    }

    public function setImportId(?int $importId): void
    {
        $this->importId = $importId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getStorageId(): ?int
    {
        return $this->storageId;
    }

    public function setStorageId(?int $storageId): void
    {
        $this->storageId = $storageId;
    }

    public function getCreateDate(): DateTimeImmutable
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeImmutable $createDate): void
    {
        $this->createDate = $createDate;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getDirectoryId(): ?int
    {
        return $this->directoryId;
    }

    public function setDirectoryId(?int $directoryId): void
    {
        $this->directoryId = $directoryId;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getFilesize(): int
    {
        return (int) $this->filesize;
    }

    public function setFilesize(int $filesize): void
    {
        $this->filesize = $filesize;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): void
    {
        $this->search = $search;
    }

    public function getThumbsUp(): int
    {
        return $this->thumbsUp;
    }

    public function setThumbsUp(int $thumbsUp): void
    {
        $this->thumbsUp = $thumbsUp;
    }

    public function getThumbsDown(): int
    {
        return $this->thumbsDown;
    }

    public function setThumbsDown(int $thumbsDown): void
    {
        $this->thumbsDown = $thumbsDown;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): void
    {
        $this->processed = $processed;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getDeleteDate(): ?DateTimeImmutable
    {
        return $this->deleteDate;
    }

    public function setDeleteDate(?DateTimeImmutable $deleteDate): void
    {
        $this->deleteDate = $deleteDate;
    }

    public function isDeletedStorage(): bool
    {
        return $this->deletedStorage;
    }

    public function setDeletedStorage(bool $deletedStorage): void
    {
        $this->deletedStorage = $deletedStorage;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function setViews(int $views): void
    {
        $this->views = $views;
    }

    public function getDownloads(): int
    {
        return $this->downloads;
    }

    public function setDownloads(int $downloads): void
    {
        $this->downloads = $downloads;
    }

    public function getDirectory(): ?Directory
    {
        return $this->directory;
    }

    public function setDirectory(?Directory $directory): void
    {
        $this->directory = $directory;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getLastUpdateDate(): ?DateTimeImmutable
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate(?DateTimeImmutable $lastUpdateDate): void
    {
        $this->lastUpdateDate = $lastUpdateDate;
    }
}
