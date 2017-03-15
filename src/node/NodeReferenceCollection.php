<?php
/*
 * This file is part of object-graph.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace SebastianBergmann\ObjectGraph;

final class NodeReferenceCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var NodeReference[]
     */
    private $elements;

    /**
     * @param NodeReference[] $elements
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $elements)
    {
        foreach ($elements as $element) {
            if (!$element instanceof NodeReference) {
                throw new InvalidArgumentException;
            }
        }

        $this->elements = $elements;
    }

    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @return NodeReference[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    public function getIterator(): NodeReferenceCollectionIterator
    {
        return new NodeReferenceCollectionIterator($this);
    }
}
