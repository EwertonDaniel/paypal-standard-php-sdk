<?php

namespace EwertonDaniel\PayPal\Tests;

use EwertonDaniel\PayPal\Auth;
use EwertonDaniel\PayPal\Exceptions\OrderException;
use EwertonDaniel\PayPal\Exceptions\ValidationException;
use EwertonDaniel\PayPal\Order;
use EwertonDaniel\PayPal\PurchaseUnit\Item;
use EwertonDaniel\PayPal\Traits\DisplayColor;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class OrderTest extends TestCase
{
    use DisplayColor;

    const RETURN_URL = 'https://example.com/returnUrl';
    const CANCEL_URL = 'https://example.com/cancelUrl';
    const NOTIFY_URL = 'https://example.com/notifyUrl';
    const CLIENT_ID = 'Client id';
    const CLIENT_SECRET = 'Client secret';
    protected Auth $auth;

    function setUp(): void
    {
        $this->auth = new Auth(self::CLIENT_ID, self::CLIENT_SECRET);
        $this->order = new Order($this->auth);
    }

    function testAutoPaypalRequestId(): void
    {
        $paypal_request_id = $this->order->setPaypalRequestId()
            ->getPaypalRequestId();
        print $this->success('Auto PayPal Request ID => OK');
        print $this->information($paypal_request_id, true);
        $this->assertTrue(Uuid::isValid($paypal_request_id));
    }

    function testPaypalRequestIdSetter(): void
    {
        $paypal_request_id = $this->order->setPaypalRequestId(Uuid::uuid4())
            ->getPaypalRequestId();
        print $this->success('PayPal Request ID Setter => OK');
        print $this->information($paypal_request_id, true);
        $this->assertTrue(Uuid::isValid($paypal_request_id));
    }

    function testDefaultReturnType(): void
    {
        $return_type = $this->order->getReturnType();
        if ($return_type) {
            print $this->success('Default Return Type => OK');
            print $this->information($return_type, true);
        }
        $this->assertEquals('representation', $return_type);
    }

    function testReturnTypeSetter(): void
    {
        $return_type = $this->order->setReturnType('minimal')->getReturnType();
        if ($return_type) {
            print $this->success('Return Type Setter => OK');
            print $this->information($return_type, true);
        }
        $this->assertEquals('minimal', $return_type);
    }

    /**
     * @throws ValidationException
     */
    function testIntentSetter(): void
    {
        $intent = $this->order->setIntent('AUTHORIZE')->getIntent();
        if ($intent) {
            print $this->success('Return Type Setter => OK');
            print $this->information($intent, true);
        }
        $this->assertEquals('AUTHORIZE', $intent);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    function testPurchaseUnit(): void
    {
        $value = random_int(19900, 29900);
        $purchase_unit = $this->order->purchaseUnit()
            ->setReferenceId()
            ->setDescription('I can write up to one hundred and twenty seven characters as a testDescription description...')
            ->setCurrencyCode('brl')
            ->addItemWithBasicData('Blacksaber', 1, $value)
            ->toArray();
        if (!empty($purchase_unit)) {
            print $this->success('Purchase Unit => OK', true);
        }
        $this->assertIsArray($purchase_unit);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    function testPaymentSource(): void
    {
        $payment_source = $this->order->paymentSource()
            ->paypal()
            ->experienceContext()
            ->setPaymentMethodPreference('IMMEDIATE_PAYMENT_REQUIRED')
            ->setBrandName(' Bounty Hunters\' Guild (BHG)')
            ->setLocale('en-US')
            ->setLandingPage('NO_PREFERENCE')
            ->setShippingPreference('NO_SHIPPING')
            ->setUserAction('PAY_NOW'
            )->setReturnUrl(self::RETURN_URL)
            ->toArray();
        if (!empty($payment_source)) {
            print $this->success('Payment Source => OK', true);
        }
        $this->assertIsArray($payment_source);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     * @throws GuzzleException
     */
    function testCreateOrder(): void
    {
        $item = new Item();
        $item->setName('Blacksaber Mandalore')->setQuantity(1)->setUnitAmount('BRL', 29900);
        $this->order
            ->setPaypalRequestId()
            ->setIntent('CAPTURE')
            ->purchaseUnit()
            ->setDiscount('BRL', 900)
            ->setCurrencyCode('BRL')
            ->addItem($item)
            ->setReferenceId()
            ->setDescription('I can write up to one hundred and twenty seven characters as a testDescription description...');
        $this->order->pushPurchaseUnit()
            ->paymentSource()
            ->paypal()
            ->experienceContext()
            ->setPaymentMethodPreference('IMMEDIATE_PAYMENT_REQUIRED')
            ->setBrandName(' Bounty Hunters Guild (BHG)')
            ->setLocale('pt-BR')
            ->setLandingPage('LOGIN')
            ->setShippingPreference('NO_SHIPPING')
            ->setUserAction('PAY_NOW')
            ->setReturnUrl(self::RETURN_URL);
        $response = $this->order->create();
        if (!empty($response)) {
            print $this->success('Order Creation => OK');
            print $this->information('ID: ' . $response['id']);
            print $this->information('Status: ' . $response['status']);
            print $this->information('Self Url: ' . $response['links']['self']['url']);
            print $this->information('Payer Action: ' . $response['links']['payer_action']['url'], true);
            $this->orderDetails($response['id']);
        }
        $this->assertIsArray($response);
    }


    /**
     * @throws GuzzleException
     * @throws ValidationException
     * @throws OrderException
     */
    function orderDetails(string|null $order_id = null): void
    {
        $detail = $this->order->setOrderId($order_id)->detail();
        print $this->success('Order Details => OK');
        print $this->information('ID: ' . $order_id);
        $this->assertIsArray($detail);
    }
}