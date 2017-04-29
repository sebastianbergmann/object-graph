# object-graph

Provides useful operations on PHP object graphs.

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
