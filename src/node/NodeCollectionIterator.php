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

use function count;
use Iterator;

final class NodeCollectionIterator implements Iterator
{
    /**
     * @psalm-var array<int,Node>
     */
    private array $nodes;

    private int $position = 1;

    public function __construct(NodeCollection $nodes)
    {
        $this->nodes = $nodes->asArray();
    }

    public function rewind(): void
    {
        $this->position = 1;
    }

    public function valid(): bool
    {
        return $this->position <= count($this->nodes);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): Node
    {
        return $this->nodes[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
