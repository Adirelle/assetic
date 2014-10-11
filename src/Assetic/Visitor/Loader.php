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

/**
 * Load the assets.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class Loader extends CachingVisitor implements LoaderInterface
{
    public function load($asset)
    {
        return $this->visit($asset);
    }

    public function visitLeaf(AssetInterface $leaf)
    {
        $leaf->load();
        return $leaf->getContent();
    }

    /**
     * {@inheriteddoc}
     */
    public function visitCollection(AssetCollectionInterface $collection)
    {
        $content = implode("", parent::visitCollection($collection));
        $collection->setContent($content);
        return $content;
    }

    /**
     * {@inheriteddoc}
     */
    protected function getSalt()
    {
        return 'load';
    }
}
