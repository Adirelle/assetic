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
use Assetic\AssetManager;
use SplObjectStorage;

/**
 * A visitor that visits each element only once.
 *
 * Results are stored for later use.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
abstract class AbstractVisitor implements VisitorInterface
{
    /**
     *
     * @var SplObjectStorage
     */
    private $visited;

    public function __construct()
    {
        $this->visited = new SplObjectStorage();
    }

    /**
     * Clear the internal state.
     */
    public function clear()
    {
        $this->visited = new SplObjectStorage();
    }

    public function visit(AssetInterface $asset)
    {
        if($this->visited->contains($asset)) {
            return $this->visited[$asset];
        }
        $result = $this->doVisist($asset);
        $this->visited->attach($asset, $result);
        return $result;
    }

    public function visitCollection(AssetCollectionInterface $collection)
    {
        $results = [];
        foreach($collection as $key => $asset) {
            $results[$key] = $this->visit($asset);
        }
        return $results;
    }

    /** Convenience method to visit every assets of an asset manager.
     *
     * @param AssetManager $manager The asset manager
     * @return array The results of the visits.
     */
    public function visitManager(AssetManager $manager)
    {
        $results = [];
        foreach($manager->getNames() as $name) {
            $results[$name] = $this->visit($manager->get($name));
        }
        return $results;
    }

    /** Actually visit an asset.
     *
     * To be overriden by subclasses.
     *
     * @param AssetInterface $asset The asset to visit.
     * @return mixed The result of the visit.
     */
    protected function doVisit(AssetInterface $asset)
    {
        return $asset->accept($this);
    }

}
