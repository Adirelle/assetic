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

use Assetic\Asset\AssetReference;

class AssetReferenceTest extends \PHPUnit_Framework_TestCase
{
    private $am;
    private $ref;

    protected function setUp()
    {
        $this->am = $this->getMock('Assetic\\AssetManager');
        $this->ref = new AssetReference($this->am, 'foo');
    }

    /**
     * @dataProvider getMethodAndRetVal
     */
    public function testMethods($method, $returnValue)
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method($method)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($returnValue, $this->ref->$method(), '->'.$method.'() returns the asset value');
    }

    public function getMethodAndRetVal()
    {
        return array(
            array('getContent', 'asdf'),
            array('getSourceRoot', 'asdf'),
            array('getSourcePath', 'asdf'),
            array('getTargetPath', 'asdf'),
            array('getLastModified', 123),
        );
    }

    public function testLazyFilters()
    {
        $this->am->expects($this->never())->method('get');
        $this->ref->ensureFilter($this->getMock('Assetic\\Filter\\FilterInterface'));
    }

    public function testFilterFlush()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())->method('ensureFilter');
        $asset->expects($this->once())
            ->method('getFilters')
            ->will($this->returnValue(array()));

        $this->ref->ensureFilter($this->getMock('Assetic\\Filter\\FilterInterface'));

        $this->assertInternalType('array', $this->ref->getFilters(), '->getFilters() flushes and returns filters');
    }

    public function testSetContent()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('setContent')
            ->with('asdf');

        $this->ref->setContent('asdf');
    }

    public function testLoad()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('load')
            ->with($filter);

        $this->ref->load($filter);
    }

    public function testDump()
    {
        $filter = $this->getMock('Assetic\\Filter\\FilterInterface');
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));
        $asset->expects($this->once())
            ->method('dump')
            ->with($filter);

        $this->ref->dump($filter);
    }

    public function testAllSimpleAsset()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $this->assertEquals([$asset], $this->ref->all());
    }

    public function testGetIteratorSimpleAsset()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->once())
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $iter = $this->ref->getIterator();

        $this->assertSame($asset, $iter->current());
        $iter->next();
        $this->assertFalse($iter->valid());
    }

    public function testCollectionMethodsSimpleAsset()
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->am->expects($this->exactly(3))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        $asset->expects($this->never())
            ->method('addLeaf');

        $asset->expects($this->never())
            ->method('removeLeaf');

        $asset->expects($this->never())
            ->method('replaceLeaf');

        $needle = $this->getMock('Assetic\\Asset\\AssetInterface');
        $leaf = $this->getMock('Assetic\\Asset\\AssetInterface');

        $this->ref->add($leaf);
        $this->ref->removeLeaf($leaf);
        $this->ref->replaceLeaf($needle, $leaf);
    }

    /*
     * @dataProvider getCollectionMethodsArgsAndRetVals
     /
    public function testCollectionMethodsCollection($method, $args, $retVal)
    {
        $asset = $this->getMock('Assetic\\Asset\\AssetCollectionInterface');

        $this->am->expects($this->exactly(2))
            ->method('get')
            ->with('foo')
            ->will($this->returnValue($asset));

        call_user_func_array(
            [$asset->expects($this->once())->method($method), 'with'],
            $args
        )->will($this->returnValue($retVal));

        $this->assertEquals($retVal, call_user_func_array([$this->ref, $method], $args));
    }

    public function getCollectionMethodsArgsAndRetVals()
    {
        $leaf = $this->getMock('Assetic\\Asset\\AssetInterface');
        $needle = $this->getMock('Assetic\\Asset\\AssetInterface');

        return [
            ['all',         [],                     'test'],
            ['getIterator', [],                     'test'],
            ['add',         [$leaf],                 null ],
            ['removeLeaf',  [$leaf, false],          null ],
            ['replaceLeaf', [$needle, $leaf, false], null ],
        ];
    }
     */

}
