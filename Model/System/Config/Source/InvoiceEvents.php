<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class InvoiceEvents implements ArrayInterface
{
    public const MANUAL = 1;
    public const ON_INVOICE = 2;
    public const ON_COMPLETE = 3;


    public function toOptionArray()
    {
        $options = [
            [
                'value' => static::MANUAL,
                'label' => __('Manual Invoicing'),
            ],
            [
                'value' => static::ON_INVOICE,
                'label' => __('Sales Order Invoice (Register)')
            ],
            [
                'value' => static::ON_COMPLETE,
                'label' => __('Sales Order (Status Complete)')
            ]
        ];

        return $options;
    }

}
