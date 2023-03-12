<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Model\InvoiceService;

class InvoiceRegistrationContext
{
    /** @var bool  */
    private $isRegistered = false;

    /**
     * @param bool|null $isRegistered
     * @return bool
     */
    public function isInvoiceRegistered(bool $isRegistered = null): bool
    {
        if ($isRegistered !== null) {
            $this->isRegistered = $isRegistered;
        }
        return $this->isRegistered;
    }
}