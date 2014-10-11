<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Visitor;

use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;

/**
 * Write asset(s) to disk.
 *
 * The flags control the behavior of the writer:
 * - WRITE_LEAVES: write asset leaves.
 * - WRITE_COLLECTIONS: write collections.
 * - WRITE_FORCE: ignore modification times.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Writer extends AbstractVisitor
{
    /**
     * Write leaves.
     */
    const WRITE_LEAVES = 0x1;

    /**
     * Write collections.
     */
    const WRITE_COLLECTIONS = 0x2;

    /**
     * Ignore modification times.
     */
    const WRITE_FORCE = 0x4;

    /**
     *
     * @var integer
     */
    private $flags;

    /**
     * @var AssetStateVisitor
     */
    private $stateVisitor;

    /**
     *
     * @var Dumper
     */
    private $dumper;

    public function __construct(Dumper $dumper, AssetStateVisitor $stateVisitor = null, $flags = self::WRITE_COLLECTIONS)
    {
        parent::__construct();
        $this->stateVisitor = $stateVisitor ?: $dumper->getStateVisitor();
        $this->dumper = $dumper;
        $this->flags = $flags;
    }

    /** Write a asset to disk.
     *
     * @param AssetInterface $asset The asset to write.
     */
    public function write(AssetInterface $asset)
    {
        $this->visit($asset);
    }

    public function visitLeaf(AssetInterface $leaf)
    {
        if(0 !== $this->flag & self::WRITE_LEAVES) {
            $this->writeAsset($leaf);
        }
    }

    public function visitCollection(AssetCollectionInterface $collection)
    {
        parent::visitCollection($collection);
        if(0 !== $this->flag & self::WRITE_COLLECTIONS) {
            $this->writeAsset($leaf);
        }
    }

    /** Actually write an asset.
     *
     * Skip up-to-date files, unless WRITE_FORCE is set.
     *
     * @param AssetInterface $asset
     */
    protected function writeAsset(AssetInterface $asset)
    {
        $targetPath = $asset->getTargetPath();
        if(!$targetPath) {
            return;
        }
        if(file_exists($targetPath) && 0 === $this->flag & self::WRITE_FORCE) {
            if(0 !== $mtime = $this->stateVisitor->getLastModified($asset) && $mtime < filemtime($targetPath)) {
                return;
            }
        }
        $this->put($targetPath, $this->dumper->dump($asset));
    }

    /** Write a content into a file.
     *
     * Creates the directory if need be.
     *
     * @param string $path The file full path.
     * @param string $content The content.
     */
    protected function putFile($path, $content)
    {
        if(!is_dir($dir = dirname($path))) {
            $this->mkdir($dir);
        }
        file_put_contents($path, $conttnt);
    }

    /** Create a directory.
     *
     * @param string $path The directory path.
     */
    protected function mkdir($path)
    {
        mkdir($path, 0777, true);
    }
}
