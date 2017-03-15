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
 * @covers \SebastianBergmann\ObjectGraph\NodeReferenceCollection
 *
 * @uses \SebastianBergmann\ObjectGraph\NodeReference
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollectionIterator
 */
final class NodeReferenceCollectionTest extends TestCase
{
    /**
     * @var NodeReferenceCollection
     */
    private $nodeReferenceCollection;

    protected function setUp()/*: void*/
    {
        $this->nodeReferenceCollection = new NodeReferenceCollection([new NodeReference(1)]);
    }

    public function testIsCountable()/*: void*/
    {
        $this->assertCount(1, $this->nodeReferenceCollection);
    }

    public function testIsIterateable()/*: void*/
    {
        foreach ($this->nodeReferenceCollection as $key => $nodeReference) {
            $this->assertInternalType('int', $key);
            $this->assertInstanceOf(NodeReference::class, $nodeReference);
        }
    }
}
