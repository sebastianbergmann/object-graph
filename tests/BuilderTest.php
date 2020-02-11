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

/**
 * @covers \SebastianBergmann\ObjectGraph\Builder
 *
 * @uses \SebastianBergmann\ObjectGraph\NodeCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeCollectionIterator
 * @uses \SebastianBergmann\ObjectGraph\Node
 * @uses \SebastianBergmann\ObjectGraph\NodeReference
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollection
 */
final class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private $builder;

    protected function setUp(): void
    {
        $this->builder = new Builder;
    }

    public function testCanBuildNodeCollectionFromSingleObject(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(\stdClass::class, $node->getClassName());
        $this->assertEquals(['foo' => 'bar'], $node->getAttributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromSingleObjectStoredInArray(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build([$a]);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(\stdClass::class, $node->getClassName());
        $this->assertEquals(['foo' => 'bar'], $node->getAttributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromSingleObjectStoredInNestedArray(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build([[$a]]);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(\stdClass::class, $node->getClassName());
        $this->assertEquals(['foo' => 'bar'], $node->getAttributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromArrayWithMultipleReferencesToTheSameObject(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $nodeCollection = $this->builder->build([$a, $a]);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(\stdClass::class, $node->getClassName());
        $this->assertEquals(['foo' => 'bar'], $node->getAttributes());
        $this->assertFalse($node->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromArrayWithMultipleObjects(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $b      = new \stdClass;
        $b->bar = 'foo';

        $nodeCollection = $this->builder->build([$a, $b]);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(\stdClass::class, $nodeOne->getClassName());
        $this->assertEquals(['foo' => 'bar'], $nodeOne->getAttributes());
        $this->assertFalse($nodeOne->referencesNodes());
        $this->assertEquals(\stdClass::class, $nodeTwo->getClassName());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->getAttributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectThatAggregatesAnotherObject(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';
        $b      = new \stdClass;
        $b->bar = 'foo';
        $a->bar = $b;

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(\stdClass::class, $nodeOne->getClassName());
        $this->assertEquals('bar', $nodeOne->getAttributes()['foo']);
        $this->assertEquals($nodeTwo->getId(), $nodeOne->getAttributes()['bar']->getId());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(\stdClass::class, $nodeTwo->getClassName());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->getAttributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectThatAggregatesAnotherObjectInArray(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';
        $b      = new \stdClass;
        $b->bar = 'foo';
        $a->bar = [$b];

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(\stdClass::class, $nodeOne->getClassName());
        $this->assertEquals('bar', $nodeOne->getAttributes()['foo']);
        $this->assertEquals($nodeTwo->getId(), $nodeOne->getAttributes()['bar'][0]->getId());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(\stdClass::class, $nodeTwo->getClassName());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->getAttributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectThatAggregatesAnotherObjectInNestedArray(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';
        $b      = new \stdClass;
        $b->bar = 'foo';
        $a->bar = [[$b]];

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(\stdClass::class, $nodeOne->getClassName());
        $this->assertEquals('bar', $nodeOne->getAttributes()['foo']);
        $this->assertEquals($nodeTwo->getId(), $nodeOne->getAttributes()['bar'][0][0]->getId());
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(\stdClass::class, $nodeTwo->getClassName());
        $this->assertEquals(['bar' => 'foo'], $nodeTwo->getAttributes());
        $this->assertFalse($nodeTwo->referencesNodes());
    }

    public function testCanBuildNodeCollectionFromObjectsWithCyclicReference(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $b      = new \stdClass;
        $b->bar = 'foo';

        $a->b = $b;
        $b->a = $a;

        $nodeCollection = $this->builder->build([$a, $b]);

        $this->assertCount(2, $nodeCollection);

        $nodeOne = $nodeCollection->findNodeById(1);
        $nodeTwo = $nodeCollection->findNodeById(2);

        $this->assertEquals(\stdClass::class, $nodeOne->getClassName());
        $this->assertEquals($nodeTwo->getId(), $nodeOne->getAttributes()['b']->getId());
        $this->assertEquals('bar', $nodeOne->getAttributes()['foo']);
        $this->assertTrue($nodeOne->referencesNodes());
        $this->assertEquals(\stdClass::class, $nodeTwo->getClassName());
        $this->assertEquals($nodeOne->getId(), $nodeTwo->getAttributes()['a']->getId());
        $this->assertEquals('foo', $nodeTwo->getAttributes()['bar']);
        $this->assertTrue($nodeTwo->referencesNodes());
    }

    public function testCanProcessInheritedAttributes(): void
    {
        $a = new ChildClass;

        $nodeCollection = $this->builder->build($a);

        $this->assertCount(1, $nodeCollection);

        $node = $nodeCollection->findNodeById(1);

        $this->assertEquals(ChildClass::class, $node->getClassName());
        $this->assertEquals(['SebastianBergmann\ObjectGraph\TestFixture\ParentClass::foo' => 'bar'], $node->getAttributes());
        $this->assertFalse($node->referencesNodes());
    }
}
