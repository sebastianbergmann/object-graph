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

use const PATHINFO_EXTENSION;
use function exec;
use function pathinfo;
use function sprintf;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

/**
 * @codeCoverageIgnore
 *
 * @throws InvalidArgumentException
 */
function object_graph_dump(string $filename, $objectGraph): void
{
    $format = pathinfo($filename, PATHINFO_EXTENSION);
    $nodes  = (new Builder)->build($objectGraph);

    switch ($format) {
        case 'dot':
            $writer = new DotWriter;
            $writer->write($filename, $nodes);

            return;

        case 'pdf':
        case 'png':
        case 'svg':
            $tmpfile = tempnam(sys_get_temp_dir(), 'object_graph_dump');

            $writer = new DotWriter;
            $writer->write($tmpfile, $nodes);

            exec(
                sprintf(
                    'dot -T%s -o %s %s',
                    $format,
                    $filename,
                    $tmpfile
                )
            );

            unlink($tmpfile);

            return;

        default:
            throw new InvalidArgumentException(
                sprintf(
                    'Unknown format "%s"',
                    $format
                )
            );
    }
}
