<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\RequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use TradeCentric\Invoice\Api\RequestBuildElementInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;

/**
 * Class ParamsRequestElement
 * @package TradeCentric\Invoice\Model\RequestBuilder
 */
class InvoiceDetailsParamsRequestElement implements RequestBuildElementInterface
{
    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        $order = $invoice->getOrder();
        $requestBuilder->addParams([
            'params' => [
                'invoice' => [
                    'details' => [
                        'currency' => $order->getOrderCurrencyCode(),
                        'subtotal' => $invoice->getSubtotal(),
                        'total' => $invoice->getGrandTotal(),
                        'tax' => $invoice->getTaxAmount(),
                        'tax_title' => '',
                        'discount' => $invoice->getDiscountAmount(),
                        'discount_title' => $order->getDiscountDescription(),
                        'shipping' => $invoice->getShippingAmount(),
                        'shipping_title' => $order->getShippingDescription(),
                        'ship_to' => $invoice->getShippingAddress() ? $invoice->getShippingAddress()->getData() : [],
                        'bill_to' => $invoice->getBillingAddress() ? $invoice->getBillingAddress()->getData() : []
                    ]
                ]
            ]
        ]);
    }
}
