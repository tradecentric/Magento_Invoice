<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Ui\Component\Control;

use Magento\Ui\Component\Control\Action;

/**
 * Class PdfAction
 */
class Transfer extends Action
{
    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        /** @var \Magento\Framework\View\Element\UiComponent\Context $context */
        $context = $this->getContext();
        $config['url'] = $context->getUrl('tradecentric/sendinvoice/massAction');
        $this->setData('config', (array) $config);
        parent::prepare();
    }
}
