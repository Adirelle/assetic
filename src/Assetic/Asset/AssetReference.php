<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\AssetManager;
use Assetic\Filter\FilterInterface;

/**
 * A reference to an asset in the asset manager.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Adirelle <adirelle@gmail.com>
 */
class AssetReference implements \IteratorAggregate, AssetCollectionInterface
{
    private $am;
    private $name;
    private $filters = array();

    public function __construct(AssetManager $am, $name)
    {
        $this->am = $am;
        $this->name = $name;
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    public function getFilters()
    {
        $this->flushFilters();

        return $this->callAsset(__FUNCTION__);
    }

    public function clearFilters()
    {
        $this->filters = array();
        $this->callAsset(__FUNCTION__);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->flushFilters();

        return $this->callAsset(__FUNCTION__, array($additionalFilter));
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        $this->flushFilters();

        return $this->callAsset(__FUNCTION__, array($additionalFilter));
    }

    public function getContent()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setContent($content)
    {
        $this->callAsset(__FUNCTION__, array($content));
    }

    public function getSourceRoot()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getSourcePath()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getSourceDirectory()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getTargetPath()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setTargetPath($targetPath)
    {
        $this->callAsset(__FUNCTION__, array($targetPath));
    }

    public function getLastModified()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getVars()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getValues()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setValues(array $values)
    {
        $this->callAsset(__FUNCTION__, array($values));
    }

    public function add(AssetInterface $asset)
    {
        $asset = $this->getAsset();

        if ($asset instanceof AssetCollectionInterface) {
            return $asset->add($asset);
        }
    }

    public function all()
    {
        $this->flushFilters();

        $asset = $this->getAsset();

        if ($asset instanceof AssetCollectionInterface) {
            return $asset->all();
        } else {
            return [ $asset ];
        }
    }

    public function removeLeaf(AssetInterface $leaf, $graceful = false)
    {
        $asset = $this->getAsset();

        if ($asset instanceof AssetCollectionInterface) {
            return $asset->removeLeaf($leaf, $graceful);
        }
    }

    public function replaceLeaf(AssetInterface $needle, AssetInterface $replacement, $graceful = false)
    {
        $asset = $this->getAsset();

        if ($asset instanceof AssetCollectionInterface) {
            return $asset->replaceLeaf($needle, $replacement, $graceful);
        }
    }

    public function getIterator()
    {
        $asset = $this->getAsset();

        if ($asset instanceof \IteratorAggregate) {
            return $asset->getIterator();
        } else {
            return new \ArrayIterator([$asset]);
        }
    }

    // private

    private function getAsset()
    {
        return $this->am->get($this->name);
    }

    private function callAsset($method, $arguments = array())
    {
        $asset = $this->getAsset();

        return call_user_func_array(array($asset, $method), $arguments);
    }

    private function flushFilters()
    {
        $asset = $this->getAsset();

        while ($filter = array_shift($this->filters)) {
            $asset->ensureFilter($filter);
        }
    }

}
