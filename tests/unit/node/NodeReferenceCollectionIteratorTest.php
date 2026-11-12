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

#[CoversClass(NodeReferenceCollectionIterator::class)]
#[UsesClass(NodeReference::class)]
#[UsesClass(NodeReferenceCollection::class)]
final class NodeReferenceCollectionIteratorTest extends TestCase
{
    public function testCanIterateNodeReferenceCollection(): void
    {
        $nodeReference = new NodeReference(1);

        $this->assertSame([$nodeReference], iterator_to_array(new NodeReferenceCollection($nodeReference)));
    }
}
