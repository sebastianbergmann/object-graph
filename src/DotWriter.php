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

use const ENT_SUBSTITUTE;
use const PHP_EOL;
use function file_put_contents;
use function htmlspecialchars;
use function is_array;
use function sprintf;
use function str_replace;
use function var_export;

final class DotWriter
{
    /**
     * @codeCoverageIgnore
     */
    public function write(string $filename, NodeCollection $nodes): void
    {
        file_put_contents($filename, $this->render($nodes));
    }

    public function render(NodeCollection $nodes): string
    {
        $buffer = <<<'EOT'
digraph G {
    graph [fontsize=30 labelloc="t" label="" splines=true overlap=false rankdir = "LR"];
    ratio = auto;


EOT;

        foreach ($nodes as $node) {
            $attributes = '';

            foreach ($node->attributes() as $name => $value) {
                if ($value instanceof NodeReference) {
                    $value = '#' . $value->id();
                } elseif (is_array($value)) {
                    $value = $this->arrayToString($value);
                } else {
                    $value = htmlspecialchars(var_export($value, true), ENT_SUBSTITUTE);
                }

                $attributes .= sprintf(
                    '<tr><td align="left" valign="top">%s</td><td align="left" valign="top">%s</td></tr>',
                    $name,
                    $value
                );
            }

            $buffer .= sprintf(
                '    "object%d" [style="filled,bold", penwidth="%d", fillcolor="white", fontname="Courier New", shape="Mrecord", label=<<table border="0" cellborder="0" cellpadding="3" bgcolor="white"><tr><td bgcolor="black" align="left"><font color="white">#%d</font></td><td bgcolor="black" align="right"><font color="white">%s</font></td></tr>%s</table>>];' . PHP_EOL,
                $node->id(),
                $node->id() === 1 ? 2 : 1,
                $node->id(),
                str_replace('\\', '\\\\', $node->className()),
                $attributes
            );
        }

        $buffer .= PHP_EOL;

        foreach ($nodes as $node) {
            $processedReferencedNodes = [];

            foreach ($node->referencedNodes() as $referencedNode) {
                if (isset($processedReferencedNodes[$referencedNode->id()])) {
                    continue;
                }

                $buffer .= sprintf(
                    '    object%d -> object%d;' . PHP_EOL,
                    $node->id(),
                    $referencedNode->id()
                );

                $processedReferencedNodes[$referencedNode->id()] = true;
            }
        }

        return $buffer . '}' . PHP_EOL;
    }

    private function arrayToString(array $array): string
    {
        $buffer = '<table border="0" cellborder="0" cellpadding="1" bgcolor="white"><tr><td align="left" valign="top" colspan="3">&#91;</td></tr>';

        foreach ($array as $key => $value) {
            if ($value instanceof NodeReference) {
                $value = '#' . $value->id();
            } elseif (is_array($value)) {
                $value = $this->arrayToString($value);
            } else {
                $value = var_export($value, true);
            }

            $buffer .= sprintf(
                '<tr><td></td><td align="left" valign="top">%s =&gt; </td><td align="left" valign="top">%s</td></tr>',
                $key,
                $value
            );
        }

        return $buffer . '<tr><td align="left" valign="top" colspan="3">&#93;</td></tr></table>';
    }
}
