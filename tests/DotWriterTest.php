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
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * @covers \SebastianBergmann\ObjectGraph\DotWriter
 *
 * @uses \SebastianBergmann\ObjectGraph\Builder
 * @uses \SebastianBergmann\ObjectGraph\NodeCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeCollectionIterator
 * @uses \SebastianBergmann\ObjectGraph\Node
 * @uses \SebastianBergmann\ObjectGraph\NodeReference
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollection
 * @uses \SebastianBergmann\ObjectGraph\NodeReferenceCollectionIterator
 */
final class DotWriterTest extends TestCase
{
    /**
     * @var DotWriter
     */
    private $dotWriter;

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var string
     */
    private $actualFile;

    protected function setUp(): void
    {
        $this->dotWriter  = new DotWriter;
        $this->root       = vfsStream::setup();
        $this->actualFile = vfsStream::url('root') . '/graph.dot';
    }

    public function testCanGenerateDotMarkupFromNodeCollection(): void
    {
        $a      = new \stdClass;
        $a->foo = '<? bar';
        $b      = new \stdClass;
        $b->bar = 'foo';
        $a->bar = $b;
        $a->baz = [[$b], true];

        $builder = new Builder;
        $nodes   = $builder->build($a);

        $this->dotWriter->write($this->actualFile, $nodes);

        $this->assertFileEquals(__DIR__ . '/_fixture/graph.dot', $this->actualFile);
    }
}
