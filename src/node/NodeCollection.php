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
     * @var Node[]
     */
    private array $nodes;

    /**
     * @param Node[] $nodes
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $nodes)
    {
        foreach ($nodes as $node) {
            if (!$node instanceof Node) {
                throw new InvalidArgumentException(
                    '$nodes must only contain Node objects'
                );
            }

            $this->nodes[$node->getId()] = $node;
        }
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    /**
     * @return Node[]
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
