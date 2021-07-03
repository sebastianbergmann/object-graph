<?php declare(strict_types=1);
/*
 * This file is part of sebastian/object-graph.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\ObjectGraph;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\ObjectGraph\TestFixture\ChildClass;
use stdClass;

/**
 * @covers \SebastianBergmann\ObjectGraph\Builder
 *
 * @uses \SebastianBergmann\ObjectGraph\Node
 * @uses \SebastianBergmann\ObjectGraph\NodeCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeCollectionIterator
 * @uses \SebastianBergmann\ObjectGraph\NodeReference
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollection
 */
final class BuilderTest extends TestCase
{
    private Builder $builder;

    protected function setUp(): void
    {
        $this->builder = new Builder;
    }

    public function testCanBuildNodeCollectionFromSingleObject(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(stdClass::class, $node->className());
        $this->assertEquals(['foo' => 'bar'], $node->attributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromSingleObjectStoredInArray(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build([$a]);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(stdClass::class, $node->className());
        $this->assertEquals(['foo' => 'bar'], $node->attributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromSingleObjectStoredInNestedArray(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build([[$a]]);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(stdClass::class, $node->className());
        $this->assertEquals(['foo' => 'bar'], $node->attributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromArrayWithMultipleReferencesToTheSameObject(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build([$a, $a]);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(stdClass::class, $node->className());
        $this->assertEquals(['foo' => 'bar'], $node->attributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromArrayWithMultipleObjects(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $b      = new stdClass;
        $b->bar = 'foo';

        $nodeCollection = $this->builder->build([$a, $b]);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(stdClass::class, $nodeOne->className());
        $this->assertEquals(['foo' => 'bar'], $nodeOne->attributes());
        $this->assertFalse($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->attributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectThatAggregatesAnotherObject(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';
        $b      = new stdClass;
        $b->bar = 'foo';
        $a->bar = $b;

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(stdClass::class, $nodeOne->className());
        $this->assertEquals('bar', $nodeOne->attributes()['foo']);
        $this->assertEquals($nodeTwo->id(), $nodeOne->attributes()['bar']->id());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->attributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectThatAggregatesAnotherObjectInArray(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';
        $b      = new stdClass;
        $b->bar = 'foo';
        $a->bar = [$b];

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(stdClass::class, $nodeOne->className());
        $this->assertEquals('bar', $nodeOne->attributes()['foo']);
        $this->assertEquals($nodeTwo->id(), $nodeOne->attributes()['bar'][0]->id());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->attributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectThatAggregatesAnotherObjectInNestedArray(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';
        $b      = new stdClass;
        $b->bar = 'foo';
        $a->bar = [[$b]];

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(stdClass::class, $nodeOne->className());
        $this->assertEquals('bar', $nodeOne->attributes()['foo']);
        $this->assertEquals($nodeTwo->id(), $nodeOne->attributes()['bar'][0][0]->id());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->attributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectsWithCyclicReference(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $b      = new stdClass;
        $b->bar = 'foo';

        $a->b = $b;
        $b->a = $a;

        $nodeCollection = $this->builder->build([$a, $b]);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(stdClass::class, $nodeOne->className());
        $this->assertEquals($nodeTwo->id(), $nodeOne->attributes()['b']->id());
        $this->assertEquals('bar', $nodeOne->attributes()['foo']);
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals($nodeOne->id(), $nodeTwo->attributes()['a']->id());
        $this->assertEquals('foo', $nodeTwo->attributes()['bar']);
        $this->assertTrue($nodeTwo->referencesNodes());
    }

    public function testCanProcessInheritedAttributes(): void
    {
        $a = new ChildClass;

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(ChildClass::class, $node->className());
        $this->assertEquals(['SebastianBergmann\ObjectGraph\TestFixture\ParentClass::foo' => 'bar'], $node->attributes());
        $this->assertFalse($node->referencesNodes());
    }
}
