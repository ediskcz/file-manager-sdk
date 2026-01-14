<?php

namespace Edisk\FileManager\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'video_subtitles')]
#[ORM\Index(name: 'video_id', columns: ['video_id'])]
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\MappedSuperclass]
abstract class VideoSubtitles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'video_id', type: 'integer', nullable: false)]
    protected int $videoId;

    #[ORM\Column(name: 'name', type: 'string', nullable: false, options: [])]
    protected string $name;

    #[ORM\Column(name: 'filename', type: 'string', nullable: false, options: [])]
    protected string $filename;

    #[ORM\Column(name: 'filesize', type: 'integer', nullable: false, options: [])]
    protected int $filesize;

    #[ORM\Column(name: 'path', type: 'string', nullable: false, options: [])]
    protected string $path;

    #[ORM\Column(name: 'jwplayer_filesize', type: 'integer', nullable: true, options: ['default' => null])]
    protected ?int $jwplayerFilesize;

    #[ORM\Column(name: 'jwplayer_path', type: 'string', nullable: true, options: ['default' => null])]
    protected ?string $jwplayerPath;

    #[ORM\JoinColumn(name: 'video_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Video::class, cascade: ['persist'], fetch: 'LAZY')]
    protected Video $video;

    #[ORM\Column(name: 'is_default', type: 'boolean', nullable: false, options: ['default' => false])]
    protected bool $isDefault = false;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getVideoId(): int
    {
        return $this->videoId;
    }

    public function setVideoId(int $videoId): void
    {
        $this->videoId = $videoId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
        return $this->filesize;
    }

    public function setFilesize(int $filesize): void
    {
        $this->filesize = $filesize;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getJwplayerFilesize(): ?int
    {
        return $this->jwplayerFilesize;
    }

    public function setJwplayerFilesize(?int $jwplayerFilesize): void
    {
        $this->jwplayerFilesize = $jwplayerFilesize;
    }

    public function getJwplayerPath(): ?string
    {
        return $this->jwplayerPath;
    }

    public function setJwplayerPath(?string $jwplayerPath): void
    {
        $this->jwplayerPath = $jwplayerPath;
    }

    public function getVideo(): Video
    {
        return $this->video;
    }

    public function setVideo(Video $video): void
    {
        $this->video = $video;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }
}
