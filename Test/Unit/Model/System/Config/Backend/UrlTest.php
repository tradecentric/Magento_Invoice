<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Test\Unit\Model\System\Config\Backend;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use TradeCentric\Invoice\Model\System\Config\Backend\Url;

class UrlTest extends TestCase
{
    /**
     * @param string $value
     * @return void
     * @dataProvider validUrlDataProvider
     */
    public function testBeforeSaveAllowsValidUrl(string $value): void
    {
        $model = $this->createModel($value);
        $model->beforeSave();
        $this->addToAssertionCount(1);
    }

    /**
     * @return array
     */
    public function validUrlDataProvider(): array
    {
        return [
            'empty value is allowed' => [''],
            // Literal IPs only - a hostname here would depend on live DNS
            // resolution succeeding in whatever environment runs this test.
            'public ipv4 literal' => ['https://8.8.8.8/invoice'],
            'public ipv6 literal' => ['https://[2001:4860:4860::8888]/invoice'],
        ];
    }

    /**
     * @param string $value
     * @return void
     * @dataProvider invalidUrlDataProvider
     */
    public function testBeforeSaveRejectsInvalidUrl(string $value): void
    {
        $this->expectException(LocalizedException::class);
        $this->createModel($value)->beforeSave();
    }

    /**
     * @return array
     */
    public function invalidUrlDataProvider(): array
    {
        return [
            'not a url' => ['not-a-url'],
            'disallowed scheme: file' => ['file:///etc/passwd'],
            'disallowed scheme: plain http' => ['http://api.example.com/invoice'],
            'loopback ip' => ['https://127.0.0.1/invoice'],
            'link-local ip' => ['https://169.254.169.254/latest/meta-data'],
            'private ip 10.x' => ['https://10.0.0.5/invoice'],
            'private ip 172.16.x' => ['https://172.16.0.5/invoice'],
            'private ip 192.168.x' => ['https://192.168.0.5/invoice'],
            'unresolvable host' => ['https://this-host-should-not-resolve.invalid/invoice'],
            'ipv6 loopback literal' => ['https://[::1]/invoice'],
            'ipv6 link-local literal' => ['https://[fe80::1]/invoice'],
            'ipv6 unique-local literal' => ['https://[fd00::1]/invoice'],
        ];
    }

    /**
     * @param string $value
     * @return void
     * @dataProvider developerModeAllowedHostDataProvider
     */
    public function testBeforeSaveAllowsPrivateAndLoopbackHostsInDeveloperMode(string $value): void
    {
        $model = $this->createModel($value, State::MODE_DEVELOPER);
        $model->beforeSave();
        $this->addToAssertionCount(1);
    }

    /**
     * @return array
     */
    public function developerModeAllowedHostDataProvider(): array
    {
        return [
            'loopback ip' => ['https://127.0.0.1/invoice'],
            'link-local ip' => ['https://169.254.169.254/latest/meta-data'],
            'private ip 192.168.x' => ['https://192.168.0.5/invoice'],
            'ipv6 loopback literal' => ['https://[::1]/invoice'],
        ];
    }

    /**
     * @return void
     */
    public function testBeforeSaveStillRejectsNonHttpsInDeveloperMode(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createModel('http://127.0.0.1/invoice', State::MODE_DEVELOPER)->beforeSave();
    }

    /**
     * @return void
     */
    public function testBeforeSaveStillRejectsMalformedUrlInDeveloperMode(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createModel('not-a-url', State::MODE_DEVELOPER)->beforeSave();
    }

    /**
     * @return void
     */
    public function testBeforeSaveAllowsHostnameThatResolvesToPublicIp(): void
    {
        $model = $this->createModelWithStubbedDns('https://mock-invoice-endpoint.test/invoice', ['93.184.216.34']);
        $model->beforeSave();
        $this->addToAssertionCount(1);
    }

    /**
     * @return void
     */
    public function testBeforeSaveRejectsHostnameThatResolvesToPrivateIp(): void
    {
        $this->expectException(LocalizedException::class);
        $this->createModelWithStubbedDns('https://mock-invoice-endpoint.test/invoice', ['10.0.0.5'])->beforeSave();
    }

    /**
     * @param string $value
     * @param string $mode
     * @return Url
     */
    private function createModel(string $value, string $mode = State::MODE_DEFAULT): Url
    {
        $appState = $this->createMock(State::class);
        $appState->method('getMode')->willReturn($mode);

        $objectManager = new ObjectManager($this);
        /** @var Url $model */
        $model = $objectManager->getObject(Url::class, ['appState' => $appState]);
        $model->setValue($value);

        return $model;
    }

    /**
     * @param string $value
     * @param string[] $ips
     * @return UrlWithStubbedDns
     */
    private function createModelWithStubbedDns(string $value, array $ips): UrlWithStubbedDns
    {
        $appState = $this->createMock(State::class);
        $appState->method('getMode')->willReturn(State::MODE_DEFAULT);

        $objectManager = new ObjectManager($this);
        /** @var UrlWithStubbedDns $model */
        $model = $objectManager->getObject(UrlWithStubbedDns::class, ['appState' => $appState]);
        $model->setValue($value);
        $model->setStubbedIps($ips);

        return $model;
    }
}
