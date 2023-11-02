<?php

namespace Edisk\FileManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="video_subtitles",
 *     indexes={
 *         @ORM\Index(name="video_id", columns={"video_id"}),
 *     }
 * )
 */
class VideoSubtitles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="video_id", type="integer", nullable=false)
     */
    private int $videoId;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, options={})
     */
    private string $name;

    /**
     * @ORM\Column(name="filename", type="string", nullable=false, options={})
     */
    private string $filename;

    /**
     * @ORM\Column(name="filesize", type="integer", nullable=false, options={})
     */
    private int $filesize;

    /**
     * @ORM\Column(name="path", type="string", nullable=false, options={})
     */
    private string $path;

    /**
     * @ORM\Column(name="jwplayer_filesize", type="integer", nullable=true, options={"default"=null})
     */
    private ?int $jwplayerFilesize;

    /**
     * @ORM\Column(name="jwplayer_path", type="string", nullable=true, options={"default"=null})
     */
    private ?string $jwplayerPath;

    /**
     * @ORM\ManyToOne(targetEntity="Video", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private Video $video;

    /**
     * @ORM\Column(name="is_default", type="boolean", nullable=false, options={"default"=false})
     */
    private bool $isDefault = false;

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
