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

        // parse_url() keeps the enclosing brackets on an IPv6 host (e.g. "[::1]").
        $host = trim($parts['host'], '[]');

        if ($this->isDisallowedHost($host)) {
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
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : $this->resolveHost($host);

        if (empty($ips)) {
            // No IP literal and no DNS resolution - fail closed rather than silently allow.
            return true;
        }

        foreach (array_unique($ips) as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve both A and AAAA records so an allowed public IPv4 address
     * can't mask a disallowed private/reserved IPv6 address (or vice versa).
     *
     * @param string $host
     * @return string[]
     */
    private function resolveHost(string $host): array
    {
        // dns_get_record() emits a warning when a record type can't be resolved;
        // that's an expected outcome here, not an error to surface.
        set_error_handler(static function (): bool {
            return true;
        });

        try {
            $records = array_merge(
                dns_get_record($host, DNS_A) ?: [],
                dns_get_record($host, DNS_AAAA) ?: []
            );
        } finally {
            restore_error_handler();
        }

        $ips = [];
        foreach ($records as $record) {
            $ips[] = $record['ip'] ?? $record['ipv6'] ?? null;
        }

        return array_values(array_filter($ips));
    }
}
