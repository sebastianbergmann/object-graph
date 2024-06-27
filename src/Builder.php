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

use function is_array;
use function is_object;
use SebastianBergmann\ObjectEnumerator\Enumerator;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SplObjectStorage;

final class Builder
{
    /**
     * @param array<mixed>|object $objectGraph
     *
     * @throws RuntimeException
     */
    public function build(array|object $objectGraph): NodeCollection
    {
        /** @var SplObjectStorage<object,int> $map */
        $map   = new SplObjectStorage;
        $id    = 1;
        $nodes = [];

        $objects = (new Enumerator)->enumerate($objectGraph);

        foreach ($objects as $object) {
            $map[$object] = $id++;
        }

        foreach ($objects as $object) {
            $attributes = [];

            $reflectedAttributes = (new ObjectReflector)->getProperties($object);

            foreach ($reflectedAttributes as $name => $value) {
                if (is_array($value)) {
                    $value = $this->processArray($value, $map);
                } elseif (is_object($value)) {
                    $value = new NodeReference($map[$value]);
                }

                $attributes[$name] = $value;
            }

            $nodes[] = new Node($map[$object], $object::class, $attributes);
        }

        return new NodeCollection(...$nodes);
    }

    /**
     * @param array<mixed>                 $array
     * @param SplObjectStorage<object,int> $map
     *
     * @return array<mixed>
     */
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
