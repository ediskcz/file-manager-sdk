<?php /** @noinspection ALL */

namespace Edisk\FileManager\Upload;

use League\Flysystem\Config;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\Visibility;
use Sovic\Gallery\Entity\GalleryItem;

class UploadManager
{
    private FilesystemOperator $filesystemOperator;

    public function setFilesystemOperator(FilesystemOperator $filesystemOperator): void
    {
        $this->filesystemOperator = $filesystemOperator;
    }

    /**
     * @param string $path
     * @return GalleryItem[]
     * @throws FilesystemException
     * @throws ImagickException
     */
    public function uploadPath(string $path): array
    {
        $uploadedItems = [];
        if (is_file($path)) {
            $uploadedItems[] = $this->handleUploadFromPath($path);
        }
        if (is_dir($path)) {
            $files = array_diff(scandir($path), ['.', '..']);
            foreach ($files as $file) {
                $uploadedItems[] = $this->handleUploadFromPath($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $uploadedItems;
    }

    /**
     * @throws ImagickException
     * @throws FilesystemException
     */
    private function handleUploadFromPath(string $path): GalleryItem
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException('invalid path');
        }


        $fileSystemFilename = $item->getId() . ($extension ? '.' . $extension : '');
        $storagePath = $this->getGalleryStoragePath();
        $fileSystemPath = $storagePath . DIRECTORY_SEPARATOR . $fileSystemFilename;

        $filesystem = $this->filesystemOperator;
        $filesystem->createDirectory($storagePath, [
            Config::OPTION_DIRECTORY_VISIBILITY => Visibility::PUBLIC,
        ]);
        $filesystem->write($fileSystemPath, file_get_contents($path));

        $item->setPath($fileSystemPath);
        $item->setIsTemp(false);
        $em->persist($item);
        $em->flush();

        return $item;
    }
}
