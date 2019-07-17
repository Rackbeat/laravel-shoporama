<?php

namespace KgBot\Shoporama;


use KgBot\Shoporama\Builders\CustomerBuilder;
use KgBot\Shoporama\Builders\CustomerGroupBuilder;
use KgBot\Shoporama\Builders\CustomerInvoiceBuilder;
use KgBot\Shoporama\Builders\DraftOrderBuilder;
use KgBot\Shoporama\Builders\DraftPurchaseOrderBuilder;
use KgBot\Shoporama\Builders\EmployeeBuilder;
use KgBot\Shoporama\Builders\FieldBuilder;
use KgBot\Shoporama\Builders\InventoryAdjustmentBuilder;
use KgBot\Shoporama\Builders\InventoryMovementBuilder;
use KgBot\Shoporama\Builders\LocationBuilder;
use KgBot\Shoporama\Builders\LotBuilder;
use KgBot\Shoporama\Builders\OrderBuilder;
use KgBot\Shoporama\Builders\OrderShipmentBuilder;
use KgBot\Shoporama\Builders\PaymentTermBuilder;
use KgBot\Shoporama\Builders\PluginBuilder;
use KgBot\Shoporama\Builders\ProductBuilder;
use KgBot\Shoporama\Builders\ProductGroupBuilder;
use KgBot\Shoporama\Builders\ProductionOrderBuilder;
use KgBot\Shoporama\Builders\PurchaseOrderBuilder;
use KgBot\Shoporama\Builders\PurchaseOrderReceiptBuilder;
use KgBot\Shoporama\Builders\SupplierBuilder;
use KgBot\Shoporama\Builders\SupplierGroupBuilder;
use KgBot\Shoporama\Builders\SupplierInvoiceBuilder;
use KgBot\Shoporama\Builders\Variation\VariationBuilder;
use KgBot\Shoporama\Builders\WebhookBuilder;
use KgBot\Shoporama\Utils\Request;

class Shoporama
{
    /**
     * @var $request Request
     */
    protected $request;

    /**
     * Shoporama constructor.
     *
     * @param null $token API token
     * @param array $options Custom Guzzle options
     * @param array $headers Custom Guzzle headers
     */
    public function __construct($token = null, $options = [], $headers = [])
    {
        $this->initRequest($token, $options, $headers);
    }

    private function initRequest($token, $options = [], $headers = [])
    {
        $this->request = new Request($token, $options, $headers);
    }

    /**
     * @return \KgBot\Shoporama\Builders\SupplierBuilder
     */
    public function suppliers()
    {
        return new SupplierBuilder($this->request);
    }

    /**
     * @return \KgBot\Shoporama\Builders\ProductBuilder
     */
    public function products()
    {
        return new ProductBuilder($this->request);
    }

    /**
     * @return \KgBot\Shoporama\Builders\OrderBuilder
     */
    public function orders()
    {
        return new OrderBuilder($this->request);
    }

    /**
     * @return \KgBot\Shoporama\Builders\CustomerBuilder
     */
    public function customers()
    {
        return new CustomerBuilder($this->request);
    }

    /**
     * @return mixed
     */
    public function self()
    {
        return json_decode((string)$this->request->client->get('self')->getBody());
    }
}