<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Block\Forms\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Punchout2Go\Punchout\Model\ModuleVersion;
use TradeCentric\Invoice\Api\ModuleMetadataInterface;
use TradeCentric\Invoice\Helper\Data;
use Magento\Backend\Block\Template\Context;

/**
 * Class Version
 * @package Punchout2Go\Punchout\Block\Forms\Config
 */
class Version extends Field
{
    /**
     * @var ModuleMetadataInterface
     */
    protected $moduleVersion;

    /**
     * @param Context $templateContext
     * @param ModuleMetadataInterface $moduleVersion
     * @param array $data
     */
    public function __construct(
        Context $templateContext,
        ModuleMetadataInterface $moduleVersion,
        array $data = []
    ) {
        $this->moduleVersion = $moduleVersion;
        parent::__construct($templateContext, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return __(
            '<label class="label"><span>' . $this->moduleVersion->getModuleVersion(). '</span></label>'
        );
    }
}
