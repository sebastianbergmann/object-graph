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
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * @internal This class is not covered by the backward compatibility promise
 */
final class Node
{
    private int $id;
    private string $className;

    /**
     * @var array<string, mixed>
     */
    private array $properties;
    private ?NodeReferenceCollection $referencedNodes = null;

    /**
     * @param array<string, mixed> $properties
     */
    public function __construct(int $id, string $className, array $properties)
    {
        $this->id         = $id;
        $this->className  = $className;
        $this->properties = $properties;
    }

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return $this->properties;
    }

    public function className(): string
    {
        return $this->className;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function referencedNodes(): NodeReferenceCollection
    {
        if ($this->referencedNodes !== null) {
            return $this->referencedNodes;
        }

        $referencedNodes = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(
                $this->properties,
                RecursiveArrayIterator::CHILD_ARRAYS_ONLY,
            ),
        );

        foreach ($iterator as $element) {
            if ($element instanceof NodeReference) {
                $referencedNodes[] = $element;
            }
        }

        $this->referencedNodes = new NodeReferenceCollection(...$referencedNodes);

        return $this->referencedNodes;
    }

    public function referencesNodes(): bool
    {
        return count($this->referencedNodes()) > 0;
    }
}
