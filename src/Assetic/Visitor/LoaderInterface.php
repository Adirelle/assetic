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

/**
 *
 * @author Adirelle <adirelle@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Load the asset content into memory and apply the filters.
     *
     * @param AssetInterface $asset
     */
    public function load(AssetInterface $asset);
}
