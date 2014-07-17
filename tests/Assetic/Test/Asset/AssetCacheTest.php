<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\AssetCache;

class AssetCacheTest extends \PHPUnit_Framework_TestCase
{
    private $inner;
    private $cache;
    private $asset;

    protected function setUp()
    {
        $this->inner = $this->getMock('Assetic\\Asset\\AssetInterface');
        $this->cache = $this->getMock('Assetic\\Cache\\CacheInterface');

        $this->asset = new AssetCache($this->inner, $this->cache);
    }

    public function testLoadFromCache()
    {
        $content = 'asdf';
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array($filter)));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnValue($content));
        $this->inner->expects($this->once())
            ->method('setContent')
            ->with($content);

        $this->asset->load($filter);
    }

    public function testLoadToCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(false));
        $this->inner->expects($this->once())->method('load');
        $this->inner->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue($content));
        $this->cache->expects($this->once())
            ->method('set')
            ->with($this->isType('string'), $content);

        $this->asset->load();
    }

    public function testDumpFromCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(true));
        $this->cache->expects($this->once())
            ->method('get')
            ->with($this->isType('string'))
            ->will($this->returnValue($content));

        $this->assertEquals($content, $this->asset->dump(), '->dump() returns the cached value');
    }

    public function testDumpToCache()
    {
        $content = 'asdf';

        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));
        $this->cache->expects($this->once())
            ->method('has')
            ->with($this->isType('string'))
            ->will($this->returnValue(false));
        $this->inner->expects($this->once())
            ->method('dump')
            ->will($this->returnValue($content));
        $this->cache->expects($this->once())
            ->method('set')
            ->with($this->isType('string'), $content);

        $this->assertEquals($content, $this->asset->dump(), '->dump() returns the dumped value');
    }

    public function testEnsureFilter()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $this->inner->expects($this->once())->method('ensureFilter');
        $this->inner->expects($this->exactly(2))
            ->method('getLastModified')
            ->will($this->returnValue(123));

        $this->asset->setLastModified(145);
        $this->asset->ensureFilter($filter);
        $this->assertEquals(123, $this->asset->getLastModified(), '->ensureFilter() clears cached last modified');
    }

    public function testGetFilters()
    {
        $this->inner->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));

        $this->assertInternalType('array', $this->asset->getFilters(), '->getFilters() returns the inner asset filters');
    }

    public function testGetContent()
    {
        $this->inner->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('asdf'));

        $this->assertEquals('asdf', $this->asset->getContent(), '->getContent() returns the inner asset content');
    }

    public function testSetContent()
    {
        $this->inner->expects($this->once())
            ->method('setContent')
            ->with('asdf');

        $this->asset->setContent('asdf');
    }

    public function testGetSourceRoot()
    {
        $this->inner->expects($this->once())
            ->method('getSourceRoot')
            ->will($this->returnValue('asdf'));

        $this->assertEquals('asdf', $this->asset->getSourceRoot(), '->getSourceRoot() returns the inner asset source root');
    }

    public function testGetSourcePath()
    {
        $this->inner->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue('asdf'));

        $this->assertEquals('asdf', $this->asset->getSourcePath(), '->getSourcePath() returns the inner asset source path');
    }

    public function testGetLastModified()
    {
        $this->inner->expects($this->once())
            ->method('getLastModified')
            ->will($this->returnValue(123));

        $this->assertEquals(123, $this->asset->getLastModified(), '->getLastModified() returns the inner asset last modified');
        $this->assertEquals(123, $this->asset->getLastModified(), '->getLastModified() caches the inner asset last modified');
    }

    public function testSetLastModified()
    {
        $this->inner->expects($this->once())
            ->method('getLastModified')
            ->will($this->returnValue(null));

        $this->asset->setLastModified(145);

        $this->assertEquals(145, $this->asset->getLastModified(), '->setLastModified() accepts any value when inner asset returns null');
    }

    public function testSetLastModifiedGreaterValue()
    {
        $this->inner->expects($this->once())
            ->method('getLastModified')
            ->will($this->returnValue(123));

        $this->asset->setLastModified(145);

        $this->assertEquals(145, $this->asset->getLastModified(), '->setLastModified() accepts values greater than existing value');
    }

    public function testSetLastModifiedLowerValue()
    {
        $this->inner->expects($this->once())
            ->method('getLastModified')
            ->will($this->returnValue(123));

        $this->asset->setLastModified(10);

        $this->assertEquals(123, $this->asset->getLastModified(), '->setLastModified() ignores values lower than existing value');
    }

    public function testClearLastModified()
    {
        $this->inner->expects($this->exactly(2))
            ->method('getLastModified')
            ->will($this->returnValue(123));

        $this->asset->setLastModified(145);

        $this->assertEquals(145, $this->asset->getLastModified());

        $this->asset->clearLastModified();
        $this->assertEquals(123, $this->asset->getLastModified(), '->clearLastModified() clears cached last modified');
    }

}
