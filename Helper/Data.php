<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package TradeCentric\Invoice\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_TRADECENTRIC_INVOICE_ENABLED = 'tradecentric_invoice/settings/active';
    const XML_PATH_TRADECENTRIC_DEBUG_ENABLED = 'tradecentric_invoice/settings/logging';
    const XML_PATH_TRADECENTRIC_INVOICE_URI = 'tradecentric_invoice/api/url';
    const XML_PATH_TRADECENTRIC_INVOICE_API_KEY = 'tradecentric_invoice/api/key';

    /**
     * @param null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_TRADECENTRIC_INVOICE_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getInvoiceUrl($storeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            static::XML_PATH_TRADECENTRIC_INVOICE_URI,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getInvoiceApiKey($storeId = null)
    {
        return (string) $this->scopeConfig->getValue(
            static::XML_PATH_TRADECENTRIC_INVOICE_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isLogging($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            static::XML_PATH_TRADECENTRIC_DEBUG_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
