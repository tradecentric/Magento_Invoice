<?php
declare(strict_types=1);

namespace TradeCentric\Invoice\Controller\Adminhtml\Sendinvoice;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;
use TradeCentric\Invoice\Api\InvoiceServiceInterface;
use TradeCentric\Invoice\Api\StoreLoggerInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;
use TradeCentric\Invoice\Helper\Data;

/**
 * Class MassAction
 * @package TradeCentric\Invoice\Controller\Adminhtml\Sendinvoice
 */
class MassAction extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = Index::ADMIN_RESOURCE;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var StoreLoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var InvoiceServiceInterface
     */
    protected $invoiceService;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassAction constructor.
     * @param Context $context
     * @param Filter $filter
     * @param Data $helper
     * @param StoreLoggerInterface $logger
     * @param InvoiceServiceInterface $invoiceService
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        Data $helper,
        StoreLoggerInterface $logger,
        InvoiceServiceInterface $invoiceService,
        InvoiceRepositoryInterface $invoiceRepository,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->invoiceService = $invoiceService;
        $this->invoiceRepository = $invoiceRepository;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection as $item) {
            try {
                if (!$this->helper->isEnabled($item->getStoreId())) {
                    throw new LocalizedException(__('Invoice export is disabled'));
                }
                $result = $this->invoiceService->sendInvoice($item);
                $this->invoiceRepository->save($item);
                if ($result)  {
                    $this->messageManager->addSuccessMessage('Invoice ' . $item->getIncrementId() . ' was successfully run');
                } else {
                    $this->messageManager->addErrorMessage('Invoice ' . $item->getIncrementId() . ' was run with the error');
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->messageManager->addErrorMessage('Invoice ' . $item->getIncrementId() . ' did not transfer');
            }
        }
        return $this->_redirect('sales/invoice/index');
    }
}
