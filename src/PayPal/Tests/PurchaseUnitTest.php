<?php

namespace EwertonDaniel\PayPal\Tests;

use EwertonDaniel\PayPal\Exceptions\ValidationException;
use EwertonDaniel\PayPal\PurchaseUnit;
use EwertonDaniel\PayPal\Shipping;
use EwertonDaniel\PayPal\Traits\DisplayColor;
use Exception;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class PurchaseUnitTest extends TestCase
{
    use DisplayColor;

    protected PurchaseUnit $purchase_unit;
    protected array $items = array();
    protected Shipping $shipping;

    protected function setUp(): void
    {
        $this->purchase_unit = new PurchaseUnit();
    }

    /**
     * @throws ValidationException
     */
    function testAutoReferenceId()
    {
        $this->purchase_unit->setReferenceId();
        $reference_id = $this->purchase_unit->getReferenceId();
        if ($reference_id) {
            print $this->success("Reference Id => OK");
            print $this->information($reference_id, true);
        }
        $this->assertTrue(Uuid::isValid($reference_id));
    }

    function testDescription()
    {
        $this->purchase_unit->setDescription('I can write up to one hundred and twenty seven characters as a testDescription description...');
        $description = $this->purchase_unit->getDescription();
        if (isset($description)) {
            print $this->success("Description => OK");
            print $this->information($description, true);
        }
        $this->assertTrue(strlen($description) <= 127);
    }


    function testDefaultCurrencyCode()
    {
        $amount = $this->purchase_unit->getAmount();
        if (isset($amount['currency_code'])) {
            print $this->success("Default Currency Code => OK");
            print $this->information($amount['currency_code'], true);
        }
        $this->assertEquals('USD', $amount['currency_code']);
    }

    function testSetCurrencyCode()
    {
        $this->purchase_unit->setCurrencyCode('EUR');
        $amount = $this->purchase_unit->getAmount();
        if (isset($amount['currency_code'])) {
            print $this->success("Currency Code  Setter => OK");
            print $this->information($amount['currency_code'], true);
        }
        $this->assertEquals('EUR', $amount['currency_code']);
    }

    /**
     * @throws ValidationException
     */
    function testSetValue()
    {
        $this->purchase_unit->setCurrencyCode('USD')
            ->addItemWithBasicData('Blacksaber Mandalore', 1, 29900)->toArray();
        $amount = $this->purchase_unit->getAmount();
        if (isset($amount['value'])) {
            print $this->success("Value Setter => OK");
            print $this->information($amount['value'], true);
        }
        $this->assertIsFloat($amount['value']);
    }

    function testInvoiceIdSetter()
    {
        $this->purchase_unit->setInvoiceId('my-invoice-0001');
        $invoice_id = $this->purchase_unit->getInvoiceId();
        if (isset($invoice_id)) {
            print $this->success("Invoice Id Setter => OK");
            print $this->information($invoice_id, true);
        }
        $this->assertEquals('my-invoice-0001', $invoice_id);
    }

    /**
     * @throws ValidationException
     */
    function testcreateItem()
    {
        $item = new PurchaseUnit\Item();
        $this->items[] = $item->setName('Darksaber')->setSku('MY-ITEM-01')
            ->setDescription('This lightsaber was stolen from your Jedi Temple by my ancestors during the fall of the Old Republic')
            ->setQuantity(1);
        if (!empty($this->items[0]->toArray())) {
            print $this->success("Item Creation => OK");
        }
        $this->assertIsArray($this->items[0]->toArray());
    }

    /**
     * @throws ValidationException
     */
    function testItemAdd()
    {
        $item = new PurchaseUnit\Item();
        $this->items[] = $item->setName('Darksaber')->setSku('MY-ITEM-02')
            ->setDescription('This lightsaber was stolen from your Jedi Temple by my ancestors during the fall of the Old Republic')
            ->setQuantity(1);
        $this->purchase_unit->addItem($this->items[0]);
        $items = $this->purchase_unit->getItems();
        if (!empty($items)) {
            print $this->success("Payee Setter => OK");
        }
        $this->assertIsArray($items);
    }

    /**
     * @throws Exception
     */
    function testPayeeSetter(): void
    {
        $this->purchase_unit->payee('djin@mandalore.com', 'KSR5PKT74QFGT');
        $payee = $this->purchase_unit->getPayee();
        if (!empty($payee)) {
            print $this->success("Payee Setter => OK");
        }
        $this->assertIsArray($payee);
    }

    /**
     * @throws Exception
     */
    function testPayeeClass(): void
    {
        $this->purchase_unit->setPayee(new PurchaseUnit\Payee('djin@mandalore.com', 'KSR5PKT74QFGT'));
        $payee = $this->purchase_unit->getPayee();
        if (!empty($payee)) {
            print $this->success("Payee Class => OK");
        }
        $this->assertIsArray($payee);
    }

    /**
     * @throws ValidationException
     */
    function testShippingSetter(): void
    {
        $this->purchase_unit->shipping()
            ->setName('Djin Djarin')
            ->setType('PICKUP_IN_PERSON')
            ->address()
            ->setLineOne('Sundari Royal Palace')
            ->setLineTwo('Sundari')
            ->setAdminAreaOne('Mandalore')
            ->setCountryCode('US')
            ->setPostalCode('19771118');
        $shipping = $this->purchase_unit->getShipping();
        if (!empty($shipping)) {
            print $this->success("Shipping Setter => OK");
        }
        $this->assertIsArray($shipping);
    }

    /**
     * @throws ValidationException
     */
    function testShippingClass(): void
    {
        $this->shipping = new Shipping();
        $this->shipping->setName('Djin Djarin')
            ->setType('PICKUP_IN_PERSON')
            ->address()
            ->setLineOne('Sundari Royal Palace')
            ->setLineTwo('Sundari')
            ->setAdminAreaOne('Mandalore')
            ->setCountryCode('US')
            ->setPostalCode('19771118');
        $this->purchase_unit->setShipping($this->shipping);
        $shipping = $this->purchase_unit->getShipping();
        if (!empty($shipping)) {
            print $this->success("Shipping Class => OK");
        }
        $this->assertIsArray($shipping);
    }

    /**
     * @throws Exception
     */
    function testSoftDescriptorSetter()
    {
        $id = random_int(1, 9) + random_int(200, 299);
        $this->purchase_unit->setSoftdescriptor('my-invoice-000' . $id);
        $soft_descriptor = $this->purchase_unit->getSoftDescriptor();
        if ($soft_descriptor) {
            print $this->success("Soft Descriptor Setter => OK");
            print $this->information($soft_descriptor, true);
        }
        $this->assertEquals('my-invoice-000' . $id, $soft_descriptor);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    function testToArray(): void
    {
        $id = random_int(1, 9) + random_int(100, 199);
        $this->setItem();
        $this->purchase_unit->setReferenceId(Uuid::uuid1()->toString());
        $this->purchase_unit->setDescription('I can write up to one hundred and twenty seven characters as a testToArray description...');
        $this->purchase_unit->setCurrencyCode('EUR');
        $this->purchase_unit->setInvoiceId('my-invoice-0' . $id + random_int(1, 55));
        $this->purchase_unit->setSoftdescriptor('my-invoice-00' . $id);
        $array = $this->purchase_unit->toArray();
        if (!empty($array)) {
            print $this->success("To Array => OK");
        }
        $this->assertIsArray($array);
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    function testString(): void
    {
        $id = random_int(1, 9) + random_int(100, 199);
        $this->purchase_unit->setReferenceId(Uuid::uuid4()->toString());
        $this->setItem();
        $this->purchase_unit->setDescription('I can write up to one hundred and twenty seven characters as a testString description...');
        $this->purchase_unit->setCurrencyCode('MXN');
        $this->purchase_unit->setInvoiceId('my-invoice-0'. $id + random_int(1, 55));
        $this->purchase_unit->setSoftdescriptor('my-invoice-000' . random_int(1, 9) + rand(91, 99));
        $string = $this->purchase_unit->__toString();
        print $this->success("To Array => OK");
        print $this->information($string, true);
        $this->assertJson($string);
    }

    /**
     * @return void
     * @throws ValidationException
     * @throws Exception
     */
    protected function setItem(): void
    {
        $number = random_int(1, 9) + rand(10, 90);
        $item = new PurchaseUnit\Item();
        $this->items[] = $item->setName('Blacksaber Mandalore')->setQuantity(1)->setUnitAmount('BRL',29900);
        $this->purchase_unit->addItem($this->items[0]);
        $this->purchase_unit->payee('djin@mandalore.com', 'KSR5PKT74QFGT');
        $this->purchase_unit->shipping()
            ->setName('Djin Djarin')
            ->setType('PICKUP_IN_PERSON')
            ->address()
            ->setLineOne('Sundari Royal Palace')
            ->setLineTwo('Sundari')
            ->setAdminAreaOne('Mandalore')
            ->setCountryCode('US')
            ->setPostalCode('19771118');
    }
}