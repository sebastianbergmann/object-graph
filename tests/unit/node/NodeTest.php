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

#[CoversClass(Node::class)]
#[UsesClass(NodeReference::class)]
#[UsesClass(NodeReferenceCollection::class)]
final class NodeTest extends TestCase
{
    private Node $node;

    protected function setUp(): void
    {
        $this->node = new Node(1, stdClass::class, ['foo' => new NodeReference(2)]);
    }

    public function testCanBeIdentified(): void
    {
        $this->assertEquals(1, $this->node->id());
    }

    public function testClassNameCanBeQueried(): void
    {
        $this->assertEquals(stdClass::class, $this->node->className());
    }

    public function testAttributesCanBeQueried(): void
    {
        $this->assertEquals(['foo' => new NodeReference(2)], $this->node->properties());
    }

    public function testReferencedNodesCanBeQueried(): void
    {
        $this->assertTrue($this->node->referencesNodes());
        $this->assertEquals(new NodeReferenceCollection(new NodeReference(2)), $this->node->referencedNodes());
    }
}
