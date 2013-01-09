# GoCard PHP API 

A PHP5.3+ interface that scrapes the Queensland Transport GoCard website to
retrieve your information.

## Installation

Add this to your composer.json by running 
`composer.phar require jwpage/gocard`.

## Usage

```php
$goCard = new \Jwpage\GoCard($cardNumber, $password);
$goCard->login();       // true
$goCard->getBalance();  // 10.00

$startDate = new \DateTime('2012-10-01');
$endDate   = new \DateTime('2012-12-01');
$goCard->getHistory($startDate, $endDate); // array of \Jwpage\GoCard\History items

$goCard->logout();      // true
```

## Running Tests

First, install PHPUnit with `composer.phar install --dev`, then run 
`./vendor/bin/phpunit`.
