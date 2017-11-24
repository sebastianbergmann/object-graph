<?php declare(strict_types=1);
use function SebastianBergmann\ObjectGraph\object_graph_dump;

require __DIR__ . '/../vendor/autoload.php';

final class Currency
{
    private $isoCode;

    public function __construct(string $isoCode)
    {
        $this->isoCode = $isoCode;
    }
}

final class Money
{
    private $amount;
    private $currency;

    public function __construct(int $amount, Currency $currency)
    {
        $this->amount   = $amount;
        $this->currency = $currency;
    }
}

final class ShoppingCartItem
{
    private $description;
    private $unitPrice;
    private $quantity;

    public function __construct(string $description, Money $unitPrice, int $quantity)
    {
        $this->description = $description;
        $this->unitPrice   = $unitPrice;
        $this->quantity    = $quantity;
    }
}

final class ShoppingCart
{
    private $items = [];

    public function add(ShoppingCartItem $item): void
    {
        $this->items[] = $item;
    }
}

$cart = new ShoppingCart;
$cart->add(new ShoppingCartItem('Foo', new Money(123, new Currency('EUR')), 1));
$cart->add(new ShoppingCartItem('Bar', new Money(456, new Currency('EUR')), 1));

object_graph_dump(__DIR__ . '/example.pdf', $cart);
object_graph_dump(__DIR__ . '/example.png', $cart);
object_graph_dump(__DIR__ . '/example.svg', $cart);
