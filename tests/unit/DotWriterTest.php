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

#[CoversClass(DotWriter::class)]
#[UsesClass(Builder::class)]
#[UsesClass(Node::class)]
#[UsesClass(NodeCollection::class)]
#[UsesClass(NodeCollectionIterator::class)]
#[UsesClass(NodeReference::class)]
#[UsesClass(NodeReferenceCollection::class)]
#[UsesClass(NodeReferenceCollectionIterator::class)]
final class DotWriterTest extends TestCase
{
    public function testCanGenerateDotMarkupFromNodeCollection(): void
    {
        $a          = new stdClass;
        $a->foo     = '<? bar';
        $b          = new stdClass;
        $b->bar     = 'foo';
        $a->bar     = $b;
        $a->baz     = [[$b], true];
        $a->qux     = ['{', '}', '|'];
        $a->fred    = ['{' => 1, '}' => 2, '|' => 3];
        $a->{'{|}'} = 'thud';

        $nodes = (new Builder)->build($a);

        $this->assertStringEqualsFile(
            __DIR__ . '/../_fixture/graph.dot',
            (new DotWriter)->render($nodes),
        );
    }
}
