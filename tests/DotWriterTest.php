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
 * @covers \SebastianBergmann\ObjectGraph\DotWriter
 *
 * @uses \SebastianBergmann\ObjectGraph\Builder
 * @uses \SebastianBergmann\ObjectGraph\Node
 * @uses \SebastianBergmann\ObjectGraph\NodeCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeCollectionIterator
 * @uses \SebastianBergmann\ObjectGraph\NodeReference
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollectionIterator
 */
final class DotWriterTest extends TestCase
{
    public function testCanGenerateDotMarkupFromNodeCollection(): void
    {
        $a      = new stdClass;
        $a->foo = '<? bar';
        $b      = new stdClass;
        $b->bar = 'foo';
        $a->bar = $b;
        $a->baz = [[$b], true];

        $nodes = (new Builder)->build($a);

        $this->assertStringEqualsFile(
            __DIR__ . '/_fixture/graph.dot',
            (new DotWriter)->render($nodes)
        );
    }
}
