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

use function get_class;
use function is_array;
use function is_object;
use SebastianBergmann\ObjectEnumerator\Enumerator;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SplObjectStorage;

final class Builder
{
    /**
     * @throws \SebastianBergmann\ObjectEnumerator\InvalidArgumentException
     * @throws \SebastianBergmann\ObjectReflector\InvalidArgumentException
     */
    public function build($objectGraph): NodeCollection
    {
        $map        = new SplObjectStorage;
        $enumerator = new Enumerator;
        $id         = 1;
        $nodes      = [];

        foreach ($enumerator->enumerate($objectGraph) as $object) {
            $map[$object] = $id++;
        }

        foreach ($enumerator->enumerate($objectGraph) as $object) {
            $attributes = [];

            foreach ((new ObjectReflector)->getAttributes($object) as $name => $value) {
                if (is_array($value)) {
                    $value = $this->processArray($value, $map);
                } elseif (is_object($value)) {
                    $value = new NodeReference($map[$value]);
                }

                $attributes[$name] = $value;
            }

            $nodes[] = new Node($map[$object], get_class($object), $attributes);
        }

        return new NodeCollection($nodes);
    }

    private function processArray(array $array, SplObjectStorage $map): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->processArray($value, $map);
            } elseif (is_object($value)) {
                $value = new NodeReference($map[$value]);
            }

            $result[$key] = $value;
        }

        return $result;
    }
}
