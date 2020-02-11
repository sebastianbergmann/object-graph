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

final class DotWriter
{
    public function write(string $filename, NodeCollection $nodes): void
    {
        $buffer = <<<EOT
digraph G {
    graph [fontsize=30 labelloc="t" label="" splines=true overlap=false rankdir = "LR"];
    ratio = auto;


EOT;

        foreach ($nodes as $node) {
            $attributes = '';

            foreach ($node->getAttributes() as $name => $value) {
                if ($value instanceof NodeReference) {
                    $value = '#' . $value->getId();
                } elseif (\is_array($value)) {
                    $value = $this->arrayToString($value);
                } else {
                    $value = \htmlspecialchars(\var_export($value, true), \ENT_SUBSTITUTE);
                }

                $attributes .= \sprintf(
                    '<tr><td align="left" valign="top">%s</td><td align="left" valign="top">%s</td></tr>',
                    $name,
                    $value
                );
            }

            $buffer .= \sprintf(
                '    "object%d" [style="filled,bold", penwidth="%d", fillcolor="white", fontname="Courier New", shape="Mrecord", label=<<table border="0" cellborder="0" cellpadding="3" bgcolor="white"><tr><td bgcolor="black" align="left"><font color="white">#%d</font></td><td bgcolor="black" align="right"><font color="white">%s</font></td></tr>%s</table>>];' . \PHP_EOL,
                $node->getId(),
                $node->getId() === 1 ? 2 : 1,
                $node->getId(),
                \str_replace('\\', '\\\\', $node->getClassName()),
                $attributes
            );
        }

        $buffer .= \PHP_EOL;

        foreach ($nodes as $node) {
            $processedReferencedNodes = [];

            foreach ($node->getReferencedNodes() as $referencedNode) {
                if (isset($processedReferencedNodes[$referencedNode->getId()])) {
                    continue;
                }

                $buffer .= \sprintf(
                    '    object%d -> object%d;' . \PHP_EOL,
                    $node->getId(),
                    $referencedNode->getId()
                );

                $processedReferencedNodes[$referencedNode->getId()] = true;
            }
        }

        $buffer .= '}' . \PHP_EOL;

        \file_put_contents($filename, $buffer);
    }

    private function arrayToString(array $array): string
    {
        $buffer = '<table border="0" cellborder="0" cellpadding="1" bgcolor="white"><tr><td align="left" valign="top" colspan="3">&#91;</td></tr>';

        foreach ($array as $key => $value) {
            if ($value instanceof NodeReference) {
                $value = '#' . $value->getId();
            } elseif (\is_array($value)) {
                $value = $this->arrayToString($value);
            } else {
                $value = \var_export($value, true);
            }

            $buffer .= \sprintf(
                '<tr><td></td><td align="left" valign="top">%s =&gt; </td><td align="left" valign="top">%s</td></tr>',
                $key,
                $value
            );
        }

        return $buffer . '<tr><td align="left" valign="top" colspan="3">&#93;</td></tr></table>';
    }
}
