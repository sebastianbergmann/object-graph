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
 * @covers \SebastianBergmann\ObjectGraph\NodeReference
 */
final class NodeReferenceTest extends TestCase
{
    /**
     * @var NodeReference
     */
    private $nodeReference;

    protected function setUp(): void
    {
        $this->nodeReference = new NodeReference(1);
    }

    public function testCanBeIdentified(): void
    {
        $this->assertEquals(1, $this->nodeReference->getId());
    }
}
