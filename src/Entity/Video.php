<?php

namespace Edisk\FileManager\Entity;

use Edisk\FileManager\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 * @ORM\Table(
 *     name="video",
 *     indexes={
 *         @ORM\Index(name="files_id", columns={"files_id"}),
 *         @ORM\Index(name="hash", columns={"hash"}),
 *     }
 * )
 */
class Video
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="files_id", type="integer", nullable=false)
     */
    private int $filesId;

    /**
     * @ORM\Column(name="resolution", type="string", length=20, nullable=true, options={"default"=null})
     */
    private ?string $resolution;

    /**
     * @ORM\Column(name="duration", type="string", length=20, nullable=true, options={"default"=null})
     */
    private ?string $duration;

    /**
     * @ORM\Column(name="codec", type="string", length=100, nullable=true, options={"default"=null})
     */
    private ?string $codec;

    /**
     * @ORM\Column(name="bitrate", type="integer", nullable=true, options={"default"=null})
     */
    private ?string $bitrate;

    /**
     * @ORM\Column(name="frequency", type="string", length=20, nullable=true, options={"default"=null})
     */
    private ?string $frequency;

    /**
     * @ORM\Column(name="audio_codec", type="string", length=100, nullable=true, options={"default"=null})
     */
    private ?string $audioCodec;

    /**
     * @ORM\Column(name="audio_bitrate", type="integer", nullable=true, options={"default"=null})
     */
    private ?string $audioBitrate;

    /**
     * @ORM\Column(name="audio_channels", type="integer", nullable=true, options={"default"=null})
     */
    private ?string $audioChannels;

    /**
     * @ORM\Column(name="audio_language", type="string", length=2, nullable=true, options={"default"=null})
     */
    private ?string $audioLanguage;

    /**
     * @ORM\Column(name="release_year", type="integer", nullable=true, options={"default"=null})
     */
    private ?int $releaseYear;

    /**
     * @ORM\Column(name="hd", type="smallint", nullable=false, options={"default"=0})
     */
    private bool $hd = false;

    /**
     * @ORM\OneToOne(targetEntity="File", fetch="LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="files_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private File $file;

    /**
     * @ORM\Column(name="has_subtitles", type="boolean", nullable=false, options={"default"=false})
     */
    private bool $hasSubtitles = false;

    /**
     * @ORM\Column(name="hash", type="string", length=12, nullable=false, options={"default"=NULL})
     */
    private string $hash;

    /**
     * @ORM\Column(name="poster_path", type="string", length=200, nullable=true, options={"default"=null})
     */
    private ?string $posterPath;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFilesId(): int
    {
        return $this->filesId;
    }

    public function setFilesId(int $filesId): void
    {
        $this->filesId = $filesId;
    }

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(?string $resolution): void
    {
        $this->resolution = $resolution;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): void
    {
        $this->duration = $duration;
    }

    public function getCodec(): ?string
    {
        return $this->codec;
    }

    public function setCodec(?string $codec): void
    {
        $this->codec = $codec;
    }

    public function getBitrate(): ?int
    {
        return $this->bitrate;
    }

    public function setBitrate(?int $bitrate): void
    {
        $this->bitrate = $bitrate;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(?string $frequency): void
    {
        $this->frequency = $frequency;
    }

    public function getAudioCodec(): ?string
    {
        return $this->audioCodec;
    }

    public function setAudioCodec(?string $audioCodec): void
    {
        $this->audioCodec = $audioCodec;
    }

    public function getAudioBitrate(): ?int
    {
        return $this->audioBitrate;
    }

    public function setAudioBitrate(?int $audioBitrate): void
    {
        $this->audioBitrate = $audioBitrate;
    }

    public function getAudioChannels(): ?int
    {
        return $this->audioChannels;
    }

    public function setAudioChannels(?int $audioChannels): void
    {
        $this->audioChannels = $audioChannels;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?int $releaseYear): void
    {
        $this->releaseYear = $releaseYear;
    }

    public function getAudioLanguage(): ?string
    {
        return $this->audioLanguage;
    }

    public function setAudioLanguage(?string $audioLanguage): void
    {
        $this->audioLanguage = $audioLanguage;
    }

    public function isHd(): bool
    {
        return $this->hd;
    }

    public function setHd(bool $hd): void
    {
        $this->hd = $hd;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): void
    {
        $this->file = $file;
    }

    public function hasSubtitles(): bool
    {
        return $this->hasSubtitles;
    }

    public function setHasSubtitles(bool $hasSubtitles): void
    {
        $this->hasSubtitles = $hasSubtitles;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getPosterPath(): ?string
    {
        return $this->posterPath;
    }

    public function setPosterPath(?string $posterPath): void
    {
        $this->posterPath = $posterPath;
    }
}