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
use SebastianBergmann\ObjectEnumerator\Exception as ObjectEnumeratorException;
use SebastianBergmann\ObjectReflector\Exception as ObjectReflectorException;
use SebastianBergmann\ObjectReflector\ObjectReflector;
use SplObjectStorage;

final class Builder
{
    /**
     * @throws RuntimeException
     */
    public function build(array|object $objectGraph): NodeCollection
    {
        /** @psalm-var SplObjectStorage<object,int> */
        $map   = new SplObjectStorage;
        $id    = 1;
        $nodes = [];

        try {
            $objects = (new Enumerator)->enumerate($objectGraph);
            // @codeCoverageIgnoreStart
        } catch (ObjectEnumeratorException $e) {
            throw new RuntimeException(
                $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
            // @codeCoverageIgnoreEnd
        }

        foreach ($objects as $object) {
            $map[$object] = $id++;
        }

        foreach ($objects as $object) {
            $attributes = [];

            try {
                $reflectedAttributes = (new ObjectReflector)->getAttributes($object);
                // @codeCoverageIgnoreStart
            } catch (ObjectReflectorException $e) {
                throw new RuntimeException(
                    $e->getMessage(),
                    (int) $e->getCode(),
                    $e
                );
                // @codeCoverageIgnoreEnd
            }

            foreach ($reflectedAttributes as $name => $value) {
                if (is_array($value)) {
                    $value = $this->processArray($value, $map);
                } elseif (is_object($value)) {
                    $value = new NodeReference($map[$value]);
                }

                $attributes[$name] = $value;
            }

            $nodes[] = new Node($map[$object], get_class($object), $attributes);
        }

        return new NodeCollection(...$nodes);
    }

    /**
     * @psalm-param SplObjectStorage<object,int> $map
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
