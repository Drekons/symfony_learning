<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemInterface;

class ClearMediaFixtures extends Fixture
{
    private const IGNORE = ['.gitignore'];
    /**
     * @var FilesystemInterface
     */
    private $articlesFilesystem;

    /**
     * ClearMediaFixtures constructor.
     *
     * @param  FilesystemInterface  $articlesFilesystem
     */
    public function __construct(FilesystemInterface $articlesFilesystem)
    {
        $this->articlesFilesystem = $articlesFilesystem;
    }

    public function load(ObjectManager $manager)
    {
        $list = $this->articlesFilesystem->listContents('', true);

        foreach ($list as $item) {
            if (in_array($item['path'], self::IGNORE) || in_array($item['filename'], self::IGNORE)) {
                continue;
            }
            $this->articlesFilesystem->delete($item['path']);
        }
    }
}
