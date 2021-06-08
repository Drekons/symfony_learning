<?php

namespace App\Service;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class FileUploader
 *
 * @package App\Service
 */
class FileUploader
{
    /**
     * @var SluggerInterface
     */
    private $slugger;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * FileUploader constructor.
     *
     * @param  FilesystemInterface  $articlesFilesystem
     * @param  SluggerInterface     $slugger
     */
    public function __construct(FilesystemInterface $articlesFilesystem, SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->filesystem = $articlesFilesystem;
    }

    /**
     * Загрузка файла
     *
     * @param  File         $file
     * @param  string|null  $oldFileName
     *
     * @return string
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function uploadFile(File $file, ?string $oldFileName = null): string
    {
        $fileName = $this->makeFileName($file);

        $this->write($file->getPathname(), $fileName);

        if ($oldFileName) {
            $this->delete($oldFileName);
        }

        return $fileName;
    }

    /**
     * Сгенерировать имя файла
     *
     * @param  File  $file
     *
     * @return string
     */
    private function makeFileName(File $file): string
    {
        return $this->slugger
            ->slug(
                pathinfo(
                    $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename(),
                    PATHINFO_FILENAME
                )
            )
            ->append('-' . uniqid())
            ->append('.' . $file->guessExtension())
            ->toString();
    }

    /**
     * Запись файла
     *
     * @param  string  $from
     * @param          $to
     *
     * @throws \League\Flysystem\FileExistsException
     * @throws \Exception
     */
    private function write(string $from, $to)
    {
        $stream = fopen($from, 'r');
        $result = $this->filesystem->writeStream($to, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        if (!$result) {
            throw new \Exception("Не удалось записать файл: $to");
        }
    }

    /**
     * Удалить файл
     *
     * @param  string  $filePath
     *
     * @throws \League\Flysystem\FileNotFoundException
     * @throws \Exception
     */
    private function delete(string $filePath)
    {
        if ($this->filesystem->has($filePath)) {
            $result = $this->filesystem->delete($filePath);

            if (!$result) {
                throw new \Exception("Ошибка удаления файла: $filePath");
            }
        }
    }
}
