<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Test\Unit\Model\System\Config\Backend;

use TradeCentric\Invoice\Model\System\Config\Backend\Url;

/**
 * Test double that returns a canned DNS resolution result instead of
 * performing a real lookup, so the "hostname resolves to a public/private
 * IP" behavior can be tested deterministically without live DNS.
 */
class UrlWithStubbedDns extends Url
{
    /**
     * @var string[]
     */
    private $stubbedIps = [];

    /**
     * @param string[] $ips
     * @return void
     */
    public function setStubbedIps(array $ips): void
    {
        $this->stubbedIps = $ips;
    }

    /**
     * @param string $host
     * @return string[]
     */
    protected function resolveHost(string $host): array
    {
        return $this->stubbedIps;
    }
}
