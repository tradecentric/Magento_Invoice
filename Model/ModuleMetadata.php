<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model;

use Magento\Framework\App\ProductMetadataInterface;
use TradeCentric\Invoice\Api\ModuleMetadataInterface;

/**
 * Class ModuleMetadata
 * @package TradeCentric\Invoice\Model
 */
class ModuleMetadata implements ModuleMetadataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var ModuleVersion
     */
    protected $moduleVersion;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleVersion $moduleVersion
     * @param string $moduleName
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        ModuleVersion $moduleVersion,
        string $moduleName = 'TradeCentric_Invoice'
    ) {
        $this->productMetadata = $productMetadata;
        $this->moduleVersion = $moduleVersion;
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->moduleVersion->getModuleVersion($this->moduleName);
    }

    /**
     * @return string
     */
    public function getMagentoVersion(): string
    {
        return $this->productMetadata->getVersion();
    }
}
