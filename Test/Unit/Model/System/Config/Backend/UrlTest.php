<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Test\Unit\Model\System\Config\Backend;

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
            'public https host' => ['https://api.tradecentric.com/invoice'],
            'public https host, other domain' => ['https://api.example.com/invoice'],
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
        ];
    }

    /**
     * @param string $value
     * @return Url
     */
    private function createModel(string $value): Url
    {
        $objectManager = new ObjectManager($this);
        /** @var Url $model */
        $model = $objectManager->getObject(Url::class);
        $model->setValue($value);

        return $model;
    }
}
