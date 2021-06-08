<?php

namespace App\Service;

use Demontpx\ParsedownBundle\Parsedown;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class MarkdownParser
 *
 * @package App\Service
 */
class MarkdownParser
{
    /**
     * @var AdapterInterface
     */
    private $cache;
    /**
     * @var Parsedown
     */
    private $parseDown;

    /**
     * MarkdownParser constructor.
     *
     * @param AdapterInterface $cache
     * @param Parsedown        $parseDown
     */
    public function __construct(AdapterInterface $cache, Parsedown $parseDown)
    {
        $this->cache = $cache;
        $this->parseDown = $parseDown;
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function parse(string $source): string
    {
        return $this->cache->get(
            'markdown_' . md5($source),
            function () use ($source) {
                return $this->parseDown->text($source);
            }
        );
    }
}
