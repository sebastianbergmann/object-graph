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

final class NodeReferenceCollection implements Countable, IteratorAggregate
{
    /**
     * @var NodeReference[]
     */
    private array $references;

    /**
     * @param NodeReference[] $references
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $references)
    {
        foreach ($references as $reference) {
            if (!$reference instanceof NodeReference) {
                throw new InvalidArgumentException(
                    '$references must only contain NodeReference objects'
                );
            }
        }

        $this->references = $references;
    }

    public function count(): int
    {
        return count($this->references);
    }

    /**
     * @return NodeReference[]
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
