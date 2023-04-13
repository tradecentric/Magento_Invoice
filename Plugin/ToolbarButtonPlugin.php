<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Plugin;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\AbstractBlock;
use TradeCentric\Invoice\Api\InvoiceServiceInterface;
use TradeCentric\Invoice\Helper\Data;
use TradeCentric\Invoice\Controller\Adminhtml\Sendinvoice\Index;

/**
 * Class ToolbarButtonPlugin
 * @package TradeCentric\Invoice\Plugin
 */
class ToolbarButtonPlugin
{
    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var InvoiceServiceInterface
     */
    protected $invoiceService;

    /**
     * @param AuthorizationInterface $authorization
     * @param InvoiceServiceInterface $invoiceService
     */
    public function __construct(
        AuthorizationInterface $authorization,
        InvoiceServiceInterface $invoiceService
    ) {
        $this->authorization = $authorization;
        $this->invoiceService = $invoiceService;
    }

    /**
     * @param ToolbarContext $toolbar
     * @param AbstractBlock $context
     * @param ButtonList $buttonList
     * @return array|void
     */
    public function beforePushButtons(
        ToolbarContext $toolbar,
        AbstractBlock $context,
        ButtonList $buttonList
    ) {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Invoice\View $context */
        if (!$context instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\View) {
            return [$context, $buttonList];
        }

        if (!$this->authorization->isAllowed(Index::ADMIN_RESOURCE)) {
            return [$context, $buttonList];
        }

        $invoice = $context->getInvoice();
        if (!$this->invoiceService->isOrderReadyToManualInvoiceSend($invoice->getOrder())) {
            return [$context, $buttonList];
        }

        if ($invoice->getData('is_exported')) {
            return [$context, $buttonList];
        }

        $buttonList->add('transfer',
            [
                'label'     => 'Transfer',
                'class'     => 'go',
                'onclick'   => 'setLocation("'.
                    $context->getUrl(
                    'tradecentric/sendinvoice',
                    ['invoice_id' => $invoice->getId()]) . '")'
            ]
        );

        return [$context, $buttonList];
    }
}
