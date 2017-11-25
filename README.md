# object-graph

Provides useful operations on PHP object graphs.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF.svg?style=flat-square)](https://php.net/)
[![Latest Stable Version](https://img.shields.io/packagist/v/sebastian/object-graph.svg?style=flat-square)](https://packagist.org/packages/sebastian/object-graph)
[![Build Status](https://img.shields.io/travis/sebastianbergmann/object-graph/master.svg?style=flat-square)](https://travis-ci.org/sebastianbergmann/object-graph)
[![Code Coverage](https://img.shields.io/codecov/c/github/sebastianbergmann/object-graph.svg?style=flat-square)](https://codecov.io/gh/sebastianbergmann/object-graph)

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

    composer require sebastian/object-graph

If you only need this library during development, for instance to run your project's test suite, then you should add it as a development-time dependency:

    composer require --dev sebastian/object-graph

## Usage

### Object Graph Visualization with GraphViz

```php
<?php declare(strict_types=1);
use function SebastianBergmann\ObjectGraph\object_graph_dump;

$cart = new ShoppingCart;
$cart->add(new ShoppingCartItem('Foo', new Money(123, new Currency('EUR')), 1));
$cart->add(new ShoppingCartItem('Bar', new Money(456, new Currency('EUR')), 1));

object_graph_dump('graph.png', $cart);
```

![Screenshot](example/example.svg)

The `object_graph_dump()` function supports the [DOT Graph Description Language](https://en.wikipedia.org/wiki/DOT_(graph_description_language)) (`.dot`), [Portable Document Format](https://en.wikipedia.org/wiki/Portable_Document_Format) (`.pdf`), [Portable Network Graphics](https://en.wikipedia.org/wiki/Portable_Network_Graphics) (`.png`), and [Scalable Vector Graphics](https://en.wikipedia.org/wiki/Scalable_Vector_Graphics) (`.svg`) output formats.

The generation of PDF, PNG, and SVG files requires the [GraphViz](http://www.graphviz.org/) `dot` binary to be on the `$PATH`.
