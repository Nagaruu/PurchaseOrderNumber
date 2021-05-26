<?php
namespace AHT\PurchaseOrderFilter\Plugin;

/**
 * Class AddDataToOrdersGrid
 */
class AddDataToOrdersGrid
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AddDataToOrdersGrid constructor.
     *
     * @param \Psr\Log\LoggerInterface $customLogger
     * @param array $data
     */
    public function __construct(
        \Psr\Log\LoggerInterface $customLogger
    ) {
        $this->logger   = $customLogger;
    }

    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
     * @param $requestName
     * @return mixed
     */
    public function afterGetReport($subject, $collection, $requestName)
    {
        if ($requestName !== 'sales_order_grid_data_source') {
            return $collection;
        }
        if ($collection->getMainTable() === $collection->getResource()->getTable('sales_order_grid')) { // check table sales_order_grid
            try {
                $purchaseordernumber = $collection->getResource()->getTable('sales_order_payment'); // get table sales_order_payment
                $collection->getSelect()->joinLeft(  // join two table
                    ['pon' => $purchaseordernumber],
                    'pon.parent_id = main_table.entity_id',
                    ['po_number']
                );
            } catch (\Zend_Db_Select_Exception $selectException) {
                // Do nothing in that case
                $this->logger->log(100, $selectException);
            }
        }
        return $collection;
    }
}