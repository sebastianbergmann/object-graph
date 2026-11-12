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

use function iterator_to_array;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(NodeCollectionIterator::class)]
#[UsesClass(Builder::class)]
#[UsesClass(Node::class)]
#[UsesClass(NodeCollection::class)]
final class NodeCollectionIteratorTest extends TestCase
{
    public function testCanIterateNodeCollection(): void
    {
        $a      = new stdClass;
        $a->foo = 'bar';

        $nodes = (new Builder)->build($a);

        $this->assertSame([1 => $nodes->findNodeById(1)], iterator_to_array($nodes));
    }
}
