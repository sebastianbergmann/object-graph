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
 * @covers \SebastianBergmann\ObjectGraph\NodeReferenceCollectionIterator
 *
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeReference
 */
final class NodeReferenceCollectionIteratorTest extends TestCase
{
    public function testCanIterateNodeReferenceCollection(): void
    {
        foreach (new NodeReferenceCollection([new NodeReference(1)]) as $key => $nodeReference) {
            $this->assertIsInt($key);
            $this->assertInstanceOf(NodeReference::class, $nodeReference);
        }
    }
}
