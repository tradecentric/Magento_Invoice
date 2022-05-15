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
class InvoiceItemsParamsRequestElement implements RequestBuildElementInterface
{
    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        $result = [];
        foreach ($invoice->getAllItems() as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();
            if (!$orderItem || $orderItem->getParentItemId()) {
                continue;
            }
            $lineLumber = $orderItem->getLineNumber();
            $result[] = array(
                'line_number' => $lineLumber,
                'quantity' => $invoiceItem->getQty() ,
                'part_id' => $invoiceItem->getSku(),
                'aux_part_id' => '',
                'description' => $invoiceItem->getName(),
                'price' => $invoiceItem->getPrice(),
                'subtotal' => $invoiceItem->getRowTotal(),
                'tax' => $invoiceItem->getTaxAmount(),
                'discount' => $invoiceItem->getDiscountAmount(),
                'row_total' => $invoiceItem->getRowTotalInclTax()
            );
        }
        $requestBuilder->addParams([
            'params' => [
                'invoice' => [
                    'items' => $result
                ]
            ]
        ]);
    }
}
