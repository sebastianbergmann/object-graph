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

final class NodeReferenceCollectionIterator implements \Iterator
{
    /**
     * @var NodeReference[]
     */
    private $elements;

    /**
     * @var int
     */
    private $position;

    public function __construct(NodeReferenceCollection $collection)
    {
        $this->elements = $collection->getElements();
    }

    public function rewind()/*: void*/
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < \count($this->elements);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): NodeReference
    {
        return $this->elements[$this->position];
    }

    public function next()/*: void*/
    {
        $this->position++;
    }
}
