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

use SebastianBergmann\ObjectEnumerator\Enumerator;
use SebastianBergmann\ObjectReflector\ObjectReflector;

final class Builder
{
    /**
     * @throws \SebastianBergmann\ObjectEnumerator\InvalidArgumentException
     * @throws \SebastianBergmann\ObjectReflector\InvalidArgumentException
     */
    public function build($objectGraph): NodeCollection
    {
        /** @var int[] $map */
        $map        = new \SplObjectStorage;
        $enumerator = new Enumerator;
        $id         = 1;
        $nodes      = [];

        foreach ($enumerator->enumerate($objectGraph) as $object) {
            $map[$object] = $id++;
        }

        foreach ($enumerator->enumerate($objectGraph) as $object) {
            $attributes = [];
            $reflector  = new ObjectReflector;

            foreach ($reflector->getAttributes($object) as $name => $value) {
                if (\is_array($value)) {
                    $value = $this->processArray($value, $map);
                } elseif (\is_object($value)) {
                    $value = new NodeReference($map[$value]);
                }

                $attributes[$name] = $value;
            }

            $nodes[] = new Node($map[$object], \get_class($object), $attributes);
        }

        return new NodeCollection($nodes);
    }

    private function processArray(array $array, \SplObjectStorage $map): array
    {
        /** @var int[] $map */
        $result = [];

        foreach ($array as $key => $value) {
            if (\is_array($value)) {
                $value = $this->processArray($value, $map);
            } elseif (\is_object($value)) {
                $value = new NodeReference($map[$value]);
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
