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
use Countable;
use IteratorAggregate;

final class NodeCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var array<int,Node>
     */
    private array $nodes = [];

    public function __construct(Node ...$nodes)
    {
        foreach ($nodes as $node) {
            $this->nodes[$node->getId()] = $node;
        }
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    /**
     * @psalm-return array<int,Node>
     */
    public function asArray(): array
    {
        return $this->nodes;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function findNodeById(int $id): Node
    {
        if (!isset($this->nodes[$id])) {
            throw new OutOfBoundsException;
        }

        return $this->nodes[$id];
    }

    public function getIterator(): NodeCollectionIterator
    {
        return new NodeCollectionIterator($this);
    }
}
