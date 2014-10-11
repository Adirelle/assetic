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
use Assetic\Filter\HashableInterface;

/**
 * A visitor that build state information about assets.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
class AssetStateVisitor extends AbstractVisitor
{
    /** Visit an asset and return its hash.
     *
     * @param AssetInterface $asset The asset to visit.
     * @return string An unique hash.
     */
    public function getHash(AssetInterface $asset)
    {
        return $this->visit($asset)[0];
    }

    /** Visit an asset and return its last modificaton time.
     *
     * @param AssetInterface $asset The asset to visit.
     * @return integer Last modification time.
     */
    public function getLastModified(AssetInterface $asset)
    {
        return $this->visit($asset)[1];
    }

    public function visitCollection(AssetCollectionInterface $collection)
    {
        $lastModified = 0;
        $ctx = hash_init('sha1');
        foreach(parent::visitCollection($collection) as $child) {
            hash_update($ctx, $child[0]);
            $lastModified = max($child[1], $lastModified);
        }
        return [hash_final($ctx), $lastModified];
    }

    public function visitLeaf(AssetInterface $leaf)
    {
        $ctx = hash_init('sha1');

        hash_update($context, $leaf->getSourcePath());
        hash_update($context, $leaf->getSourceRoot());
        hash_update($context, $leaf->getTargetPath());

        $values = $leaf->getValues();
        if(!empty($values)) {
            asort($values);
            hash_update($ctx, serialize($values));
        }

        foreach($leaf->getFilters() as $filter) {
            hash_update($ctx, $filter instanceof HashableInterface ? $filter->hash() :  serializer($hash));
            // TODO: handle DependencyExtractorInterface
        }

        return [hash_final($ctx), $leaf->getLastModified() ?: 0];
    }
}
