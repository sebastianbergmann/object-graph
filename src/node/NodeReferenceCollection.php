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

use function array_values;
use function count;
use Countable;
use IteratorAggregate;

final class NodeReferenceCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<NodeReference>
     */
    private array $references;

    public function __construct(NodeReference ...$references)
    {
        $this->references = array_values($references);
    }

    public function count(): int
    {
        return count($this->references);
    }

    /**
     * @psalm-return list<NodeReference>
     */
    public function getReferences(): array
    {
        return $this->references;
    }

    public function getIterator(): NodeReferenceCollectionIterator
    {
        return new NodeReferenceCollectionIterator($this);
    }
}
