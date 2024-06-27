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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\ObjectGraph\TestFixture\ChildClass;
use stdClass;

#[CoversClass(Builder::class)]
#[UsesClass(Node::class)]
#[UsesClass(NodeCollection::class)]
#[UsesClass(NodeCollectionIterator::class)]
#[UsesClass(NodeReference::class)]
#[UsesClass(NodeReferenceCollection::class)]
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
        $this->assertEquals(['foo' => 'bar'], $node->properties());
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
        $this->assertEquals(['foo' => 'bar'], $node->properties());
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
        $this->assertEquals(['foo' => 'bar'], $node->properties());
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
        $this->assertEquals(['foo' => 'bar'], $node->properties());
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
        $this->assertEquals(['foo' => 'bar'], $nodeOne->properties());
        $this->assertFalse($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->properties());
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
        $this->assertEquals('bar', $nodeOne->properties()['foo']);
        $this->assertEquals($nodeTwo->id(), $nodeOne->properties()['bar']->id());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->properties());
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
        $this->assertEquals('bar', $nodeOne->properties()['foo']);
        $this->assertEquals($nodeTwo->id(), $nodeOne->properties()['bar'][0]->id());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->properties());
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
        $this->assertEquals('bar', $nodeOne->properties()['foo']);
        $this->assertEquals($nodeTwo->id(), $nodeOne->properties()['bar'][0][0]->id());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->properties());
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
        $this->assertEquals($nodeTwo->id(), $nodeOne->properties()['b']->id());
        $this->assertEquals('bar', $nodeOne->properties()['foo']);
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(stdClass::class, $nodeTwo->className());
        $this->assertEquals($nodeOne->id(), $nodeTwo->properties()['a']->id());
        $this->assertEquals('foo', $nodeTwo->properties()['bar']);
        $this->assertTrue($nodeTwo->referencesNodes());
    }

    public function testCanProcessInheritedAttributes(): void
    {
        $a = new ChildClass;

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(ChildClass::class, $node->className());
        $this->assertEquals(['SebastianBergmann\ObjectGraph\TestFixture\ParentClass::foo' => 'bar'], $node->properties());
        $this->assertFalse($node->referencesNodes());
    }
}
