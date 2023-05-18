<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\RequestBuilder;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;
use TradeCentric\Invoice\Api\RequestBuildElementInterface;
use TradeCentric\Invoice\Api\RequestBuilderInterface;
use Magento\Weee\Block\Item\Price\Renderer;

/**
 * Class ParamsRequestElement
 * @package TradeCentric\Invoice\Model\RequestBuilder
 */
class InvoiceItemsParamsRequestElement implements RequestBuildElementInterface
{
    /** @var Renderer */
    protected $renderer;

    public function __construct(
        Renderer $renderer
    ) {
        $this->renderer = $renderer;
    }

    /**
     * @param RequestBuilderInterface $requestBuilder
     * @param InvoiceInterface $invoice
     */
    public function build(RequestBuilderInterface $requestBuilder, InvoiceInterface $invoice): void
    {
        $result = [];
        /** @var InvoiceItemInterface $invoiceItem */
        foreach ($invoice->getAllItems() as $invoiceItem) {
            $orderItem = $invoiceItem->getOrderItem();
            if (!$orderItem || $orderItem->getParentItemId()) {
                continue;
            }

            $this->renderer->setItem($invoiceItem);

            $result[] = array(
                'line_number' => $orderItem->getExtensionAttributes()->getLineNumber(),
                'quantity' => $invoiceItem->getQty() ,
                'part_id' => $invoiceItem->getSku(),
                'aux_part_id' => '',
                'description' => $invoiceItem->getName(),
                'price' => $this->renderer->getUnitDisplayPriceExclTax(),
                'subtotal' => $this->renderer->getRowDisplayPriceExclTax(),
                'tax' => $invoiceItem->getTaxAmount(),
                'discount' => $invoiceItem->getDiscountAmount(),
                'row_total' => $this->renderer->getRowDisplayPriceInclTax()
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
