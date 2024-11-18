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
use stdClass;

#[CoversClass(NodeCollection::class)]
#[UsesClass(Builder::class)]
#[UsesClass(Node::class)]
#[UsesClass(NodeCollectionIterator::class)]
final class NodeCollectionTest extends TestCase
{
    private NodeCollection $nodeCollection;

    protected function setUp(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $this->nodeCollection = (new Builder)->build($a);
    }

    public function testIsCountable(): void
    {
        $this->assertCount(1, $this->nodeCollection);
    }

    public function testIsIterateable(): void
    {
        foreach ($this->nodeCollection as $key => $node) {
            $this->assertInstanceOf(Node::class, $node);
        }
    }

    public function testNodeCanBeAccessedById(): void
    {
        $this->assertSame(1, $this->nodeCollection->findNodeById(1)->id());
    }

    public function testNodeThatDoesNotExistCannotBeAccessedById(): void
    {
        $this->expectException(OutOfBoundsException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $this->nodeCollection->findNodeById(2);
    }
}
