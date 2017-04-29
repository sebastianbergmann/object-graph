<?php
/*
 * This file is part of sebastian/object-graph.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SebastianBergmann\ObjectGraph;

final class NodeCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var Node[]
     */
    private $elements;

    /**
     * @param Node[] $elements
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $elements)
    {
        foreach ($elements as $element) {
            if (!$element instanceof Node) {
                throw new InvalidArgumentException;
            }

            $this->elements[$element->getId()] = $element;
        }
    }

    public function count(): int
    {
        return \count($this->elements);
    }

    /**
     * @return Node[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function findNodeById(int $id): Node
    {
        if (!isset($this->elements[$id])) {
            throw new OutOfBoundsException;
        }

        return $this->elements[$id];
    }

    public function getIterator(): NodeCollectionIterator
    {
        return new NodeCollectionIterator($this);
    }
}
