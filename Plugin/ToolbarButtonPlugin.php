<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Plugin;

use Magento\Backend\Block\Widget\Button\ButtonList;
use Magento\Backend\Block\Widget\Button\Toolbar as ToolbarContext;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\AbstractBlock;
use TradeCentric\Invoice\Helper\Data;
use TradeCentric\Invoice\Controller\Adminhtml\Sendinvoice\Index;


/**
 * Class ToolbarButtonPlugin
 * @package TradeCentric\Invoice\Plugin
 */
class ToolbarButtonPlugin
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * ToolbarButtonPlugin constructor.
     * @param AuthorizationInterface $authorization
     * @param Data $helper
     */
    public function __construct(
        AuthorizationInterface $authorization,
        Data $helper
    ) {
        $this->authorization = $authorization;
        $this->helper = $helper;
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
        if (!$this->helper->isEnabled($invoice->getStoreId())) {
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
