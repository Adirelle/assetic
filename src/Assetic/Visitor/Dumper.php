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
use Assetic\Cache\CacheInterface;

/**
 * Dump the assets.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Dumper extends CachingVisitor implements DumperInterface
{
    /** The loader.
     *
     * @var Loader
     */
    private $loader;

    /** Create the dumper.
     *
     *  It will reuse the loader state visitor and cache if none are provided.
     *
     * @param Loader $loader A loader.
     * @param AssetStateVisitor $stateVisitor An optional cache visitor
     * @param CacheInterface $cache A cache.
     */
    public function __construct(Loader $loader, AssetStateVisitor $stateVisitor = null, CacheInterface $cache = null)
    {
        parent::__construct(
            $stateVisitor ?: $loader->getStateVisitor(),
            $cache ?: $loader->getCache()
        );
        $this->loader = $loader;
    }

    public function clear()
    {
        parent::clear();
        $this->loader->clear();
    }

    public function dump(AssetInterface $asset)
    {
        return $this->visit($asset);
    }

    public function visitLeaf(AssetInterface $leaf)
    {
        $this->loader->load($leaf);
        $clone = clone $leaf;
        return $clone->dump();
    }

    public function visitCollection(AssetCollectionInterface $collection)
    {
        return implode("\n", parent::visitCollection($collection));
    }

    protected function getSalt()
    {
        return 'dump';
    }
}
