<?php

namespace EwertonDaniel\PayPal\Traits\Order;

use Echosistema\SHR\Http;
use EwertonDaniel\PayPal\Auth;
use EwertonDaniel\PayPal\Configuration\Configuration;
use EwertonDaniel\PayPal\Exceptions\OrderException;
use EwertonDaniel\PayPal\Exceptions\PayPalAuthenticationException;
use EwertonDaniel\PayPal\Exceptions\ValidationException;
use EwertonDaniel\PayPal\Order;
use EwertonDaniel\PayPal\PaymentSource;
use EwertonDaniel\PayPal\PurchaseUnit;
use GuzzleHttp\Exception\GuzzleException;
use Ramsey\Uuid\Uuid;

trait OrderSetters
{
    protected null|Auth $auth;
    /**
     * @var string
     */
    protected string $paypal_request_id;
    /**
     * @var string
     * The intent to either capture payment immediately or authorize a payment for an order after order creation.
     *  The possible values are:
     *      CAPTURE. The merchant intends to capture payment immediately after the customer makes a payment.
     *      AUTHORIZE. The merchant intends to authorize a payment and place funds on hold after the customer makes a payment.
     *          Authorized payments are best captured within three days of authorization but are available to capture for up to 29 days.
     *          After the three-day honor period, the original authorized payment expires, and you must re-authorize the payment.
     *          You must make a separate request to capture payments on demand.
     *          This intent is not supported when you have more than one `purchase_unit` within your order.
     */
    protected string $intent;
    /**
     * @var array|PurchaseUnit
     * An array of purchase units.
     * Each purchase unit establishes a contract between a payer and the payee.
     * Each purchase unit represents either a full or partial order that the payer intends to purchase from the payee.
     */
    protected array|PurchaseUnit $purchase_unit;
    protected array $purchase_units;
    protected array|PaymentSource $payment_source;
    protected Configuration $configuration;
    protected null|string $url;
    protected Http $http;
    protected string $return_type;

    /**
     * @throws GuzzleException
     * @throws PayPalAuthenticationException
     */
    public function setAuth(string $client_id, string $client_secret, bool $is_production = false): static
    {
        $this->auth = new Auth($client_id, $client_secret, $is_production);
        return $this;
    }

    /**
     * @param string|null $paypal_request_id
     * @return Order
     */
    public function setPaypalRequestId(string|null $paypal_request_id = null): static
    {
        $this->paypal_request_id = $paypal_request_id ?? Uuid::uuid1()->toString();
        return $this;
    }

    /**
     * @param string $intent
     * @return $this
     * @throws ValidationException
     */
    public function setIntent(string $intent): static
    {
        $intent = strtoupper($intent);
        if (!in_array($intent, ['CAPTURE', 'AUTHORIZE'])) {
            throw new ValidationException('Entered intent is not a valid value!, Valid values: CAPTURE, AUTHORIZE');
        }
        $this->intent = $intent;
        return $this;
    }

    /**
     * @param PurchaseUnit $purchase_unit
     * @return $this
     */
    public function setPurchaseUnit(PurchaseUnit $purchase_unit): static
    {
        $this->purchase_units[] = $purchase_unit->toArray();
        return $this;
    }

    /**
     * @param bool $class
     * @return PaymentSource|array
     * @throws ValidationException
     */
    public function getPaymentSource(bool $class = false): PaymentSource|array
    {
        $payment_source = is_array($this->payment_source) ? $this->payment_source : $this->getPaymentSourceClassOrFallback($class);
        return !empty($payment_source) ? $payment_source : throw new ValidationException('The payment source is empty');
    }

    /**
     * @param PaymentSource $payment_source
     * @return Order
     */
    public function setPaymentSource(PaymentSource $payment_source): static
    {
        $this->payment_source = $payment_source;
        return $this;
    }

    private function getPaymentSourceClassOrFallback(bool $class): PaymentSource|array
    {
        return $class ? $this->payment_source : $this->payment_source->toArray();
    }

    public function setReturnType(string $return_type): static
    {
        $this->return_type = $return_type;
        return $this;
    }

    public function setOrderId(string $order_id): static
    {
        $this->order_id = $order_id;
        return $this;
    }

    /**
     * @throws OrderException
     */
    protected function setUrl(string $endpoint): void
    {
        $url = $this->configuration->setIsProduction($this->auth->is_production)->getUrl();
        if (!$url) throw new OrderException();
        $endpoint = $this->configuration->getEndpoint($endpoint);
        if (!isset($endpoint['uri'])) throw new OrderException();
        $this->url = $url . $endpoint['uri'];
    }

}