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
class InvoiceHeaderParamsRequestElement implements RequestBuildElementInterface
{
    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        $payment = $invoice->getOrder()->getPayment();
        $requestBuilder->addParams([
            'params' => [
                'invoice' => [
                    'header' => [
                        'ext_order_id' => $payment->getPoNumber(),
                        'ext_invoice_id' => $invoice->getIncrementId(),
                        'order_request_id' => $payment->getAdditionalInformation('request_id')
                    ]
                ]
            ]
        ]);
    }
}
