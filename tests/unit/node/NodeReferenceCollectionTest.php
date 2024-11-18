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

#[CoversClass(NodeReferenceCollection::class)]
#[UsesClass(NodeReference::class)]
#[UsesClass(NodeReferenceCollectionIterator::class)]
final class NodeReferenceCollectionTest extends TestCase
{
    private NodeReferenceCollection $nodeReferenceCollection;

    protected function setUp(): void
    {
        $this->nodeReferenceCollection = new NodeReferenceCollection(new NodeReference(1));
    }

    public function testIsCountable(): void
    {
        $this->assertCount(1, $this->nodeReferenceCollection);
    }

    public function testIsIterable(): void
    {
        foreach ($this->nodeReferenceCollection as $key => $nodeReference) {
            $this->assertInstanceOf(NodeReference::class, $nodeReference);
        }
    }
}
