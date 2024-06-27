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
use stdClass;

/**
 * @covers \SebastianBergmann\ObjectGraph\NodeCollectionIterator
 *
 * @uses \SebastianBergmann\ObjectGraph\Builder
 * @uses \SebastianBergmann\ObjectGraph\Node
 * @uses \SebastianBergmann\ObjectGraph\NodeCollection
 */
final class NodeCollectionIteratorTest extends TestCase
{
    public function testCanIterateNodeCollection(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        foreach ((new Builder)->build($a) as $key => $node) {
            $this->assertIsInt($key);
            $this->assertInstanceOf(Node::class, $node);
        }
    }
}
