<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * Validates the TradeCentric invoice export URL on save to require an
 * encrypted transport (mitigating MITM) and block private/loopback/
 * link-local hosts (mitigating SSRF via config misconfiguration) (CN-804).
 */
class Url extends Value
{
    const ALLOWED_SCHEMES = ['https'];

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $url = trim((string) $this->getValue());

        if ($url === '') {
            return parent::beforeSave();
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            throw new LocalizedException(__('Invoice URL is not a valid URL.'));
        }

        if (!in_array(strtolower($parts['scheme']), self::ALLOWED_SCHEMES, true)) {
            throw new LocalizedException(__('Invoice URL must use the https scheme.'));
        }

        if ($this->isDisallowedHost($parts['host'])) {
            throw new LocalizedException(
                __('Invoice URL may not point to a private, loopback, or link-local address.')
            );
        }

        return parent::beforeSave();
    }

    /**
     * @param string $host
     * @return bool
     */
    private function isDisallowedHost(string $host): bool
    {
        $isLiteralIp = (bool) filter_var($host, FILTER_VALIDATE_IP);
        $ip = $isLiteralIp ? $host : gethostbyname($host);

        if (!$isLiteralIp && $ip === $host) {
            // DNS resolution failed - fail closed rather than silently allow.
            return true;
        }

        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
