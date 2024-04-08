[![Generic badge](https://img.shields.io/github/last-commit/ewertondaniel/standard-paypal-php-sdk)](https://github.com/EwertonDaniel/standard-paypal-php-sdk.git)
[![Generic badge](https://img.shields.io/badge/stable-v1.0.2-blue.svg)](https://github.com/EwertonDaniel/standard-paypal-php-sdk)
[![Twitter](https://img.shields.io/twitter/follow/dsrewerton?style=social)](https://twitter.com/dsrewerton)

# PayPal Standard PHP SDK

_This library provides developers with a simple set of bindings to help you
integrate [PayPal Standard](https://developer.paypal.com/home) to a website
and start receiving payments._

## 🛠 Requirements

`php >= 8.1`

`guzzlehttp/guzzle >= 7.0.1`

`echosistema/simple-http-request" >= 1.0.1`

## 💻 Installation

First time using PayPal? Create your PayPal account
in [PayPal](https://www.paypal.com/br/webapps/mpp/account-selection), if you don’t have one already.

Download [Composer](https://getcomposer.org/) if not already installed

On your project directory run on the command line `"composer require ewertondaniel/paypal-standard-php-sdk"`
for `PHP 8.1`;

That's it! **PayPal Standard PHP SDK** has been successfully installed!

## 🧑‍💻 Examples

### 🔓 Getting authorization

```php

use EwertonDaniel\PayPal\Auth;

        $authentication = new Auth($client_id, $client_secret, $is_production);
        $authentication->getScopes();
        $authentication->getAccessToken();
        $authentication->getTokenType();
        $authentication->getAppId();
        $authentication->getExpiresIn();
        $authentication->getNonce();

```

### 💲 Create an Order

```php

use EwertonDaniel\PayPal\Order;

        $order = new Order($authentication);
        
        //Set Purchase Unit
        $order->setPaypalRequestId()
            ->setIntent('CAPTURE')
            ->purchaseUnit()
            ->setCurrencyCode('BRL')
            ->addItemWithBasicData('Blacksaber Mandalore', 1, 29900) // string $name, int $quantity, int $value
            ->setReferenceId()
            ->setDescription('I can write up to one hundred and twenty seven characters as a description...');
            
        // Set Payment Source    
        $order->pushPurchaseUnit()
            ->paymentSource()
            ->paypal()
            ->experienceContext()
            ->setPaymentMethodPreference('IMMEDIATE_PAYMENT_REQUIRED')
            ->setBrandName('Bounty Hunters Guild (BHG)') // Company name
            ->setLocale('pt-BR')
            ->setLandingPage('LOGIN')
            ->setShippingPreference('NO_SHIPPING')
            ->setUserAction('PAY_NOW')
            ->setReturnUrl('https://example.com/returnUrl')
            ->setNotificationUrl('https://example.com/notifyUrl')
            ->setCancelUrl('https://example.com/cancelUrl');
            
        $response = $order->create();

```

### ℹ Order Details

```php

use EwertonDaniel\PayPal\Order;
        $order_id = $_POST['token'];
        $detail = $order->setOrderId($order_id)->detail();

```

## 📖 Documentation

### 🔗 Visit the PayPal for further information regarding:

[PayPal REST APIs Documentation](https://developer.paypal.com/api/rest/)
