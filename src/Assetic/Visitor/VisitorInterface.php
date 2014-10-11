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

/** A visitor.
 *
 * Since PHP does not provide method overloading, AssetInterface::accept
 * is reponsible of calling the right method of the visitor.
 *
 * @author Adirelle <adirelle@gmail.com>
 */
interface VisitorInterface
{
    /** Visit an asset.
     *
     * This is the main entry point of any visit.
     *
     * @param VisitableInterface $asset The asset to visit.
     * @return mixed The result of the visit.
     */
    public function visit(AssetInterface $asset);

    /** Visit a collection.
     *
     * Do not call directly, use visit instead.
     *
     * @internal
     * @param AssetCollectionInterface $collection The collection to visit.
     * @return mixed The result of the visit.
     */
    public function visitCollection(AssetCollectionInterface $collection);

    /** Visit a leaf.
     *
     * Do not call directly, use visit instead.
     *
     * @internal
     * @param AssetInterface $leaf The asset to visit.
     * @return mixed The result of the visit.
     */
    public function visitLeaf(AssetInterface $leaf);
}
