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

/**
 * @covers \SebastianBergmann\ObjectGraph\NodeCollection
 *
 * @uses \SebastianBergmann\ObjectGraph\Builder
 * @uses \SebastianBergmann\ObjectGraph\NodeCollectionIterator
 * @uses \SebastianBergmann\ObjectGraph\Node
 */
final class NodeCollectionTest extends TestCase
{
    /**
     * @var NodeCollection
     */
    private $nodeCollection;

    protected function setUp(): void
    {
        $a      = new \stdClass;
        $a->foo = 'bar';

        $builder              = new Builder;
        $this->nodeCollection = $builder->build($a);
    }

    public function testIsCountable(): void
    {
        $this->assertCount(1, $this->nodeCollection);
    }

    public function testIsIterateable(): void
    {
        foreach ($this->nodeCollection as $key => $node) {
            $this->assertInternalType('int', $key);
            $this->assertInstanceOf(Node::class, $node);
        }
    }

    public function testNodeCanBeFoundById(): void
    {
        $this->assertInstanceOf(Node::class, $this->nodeCollection->findNodeById(1));
    }

    public function testCanOnlyBeCreatedFromArrayOfNodeObjects(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NodeCollection([null]);
    }

    public function testNodeThatDoesNotExistCannotBeRetrieved(): void
    {
        $this->expectException(OutOfBoundsException::class);

        $this->nodeCollection->findNodeById(2);
    }
}
