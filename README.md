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
<?php
use function SebastianBergmann\ObjectGraph\object_graph_dump;

$cart = new ShoppingCart;
$cart->add(new ShoppingCartItem('Foo', new Money(123, new Currency('EUR')), 1));
$cart->add(new ShoppingCartItem('Bar', new Money(456, new Currency('EUR')), 1));

object_graph_dump('graph.png', $cart);
```

![Screenshot](screenshot.png)
