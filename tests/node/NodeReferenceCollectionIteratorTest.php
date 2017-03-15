<?php
/*
 * This file is part of object-graph.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function testCanIterateNodeReferenceCollection()/*: void*/
    {
        $nodeReferenceCollection = new NodeReferenceCollection([new NodeReference(1)]);

        foreach ($nodeReferenceCollection as $key => $nodeReference) {
            $this->assertInternalType('int', $key);
            $this->assertInstanceOf(NodeReference::class, $nodeReference);
        }
    }
}
