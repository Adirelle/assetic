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

use Assetic\Asset\AssetInterface;
use Assetic\Cache\ArrayCache;
use Assetic\Cache\CacheInterface;

/**
 * A visitor that stores the results of the visit in a cache.
 *
 * Use a state visitor to ensure the stored data are always fresh.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
abstract class CachingVisitor extends AbstractVisitor
{
    /** The state visitor.
     *
     * @var AssetStateVisitor
     */
    private $stateVisitor;

    /** The cache to store data into.
     *
     * @var CacheInterface
     */
    private $cache;

    /** Build the visitor.
     *
     * Will create its own state visitor and an array cache if no existing object are provided.
     *
     * @param AssetStateVisitor $stateVisitor An existing state visitor.
     * @param CacheInterface $cache A cache.
     */
    public function __construct(AssetStateVisitor $stateVisitor = null, CacheInterface $cache = null)
    {
        parent::__construct();
        $this->setStateVisitor($stateVisitor ?: new AssetStateVisitor());
        $this->setCache($cache ?: new ArrayCache());
    }

    public function clear()
    {
        parent::clear();
        $this->stateVisitor->clear();
    }

    /**
     * Change the cache.
     *
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Return the current cache.
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Change the state visitor.
     *
     * @param AssetStateVisitor $stateVisitor
     */
    public function setStateVisitor(AssetStateVisitor $stateVisitor)
    {
        $this->stateVisitor = $stateVisitor;
    }

    /**
     * Return the current state visitor.
     *
     * @return AssetStateVisitor
     */
    public function getStateVisitor()
    {
        return $this->stateVisitor;
    }

    protected function doVisit(AssetInterface $asset)
    {
        $key = sha1($this->stateVisitor->getHash($asset).$this->getSalt());

        if($this->cache->has($key)) {
            $lastModified = $this->stateVisitor->getLastModified($asset);
            if(!empty($lastModified) && $lastModified <= $this->cache->get("$key.lastModified")) {
                return $this->cache->get($key);
            }
            $this->cache->remove($key);
            $this->cache->remove("$key.lastModified");
        }

        $result = parent::doVisit($asset);

        $this->cache->set($key, $result);
        $this->cache->set("$key.lastModified", $lastModified ?: -1);

        return $result;
    }

    /** Return a salt used to calculate cache keys for this visitor.
     *
     * @return string
     */
    abstract protected function getSalt();
}
