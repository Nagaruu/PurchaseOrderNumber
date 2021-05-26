<?php
namespace AHT\PurchaseOrderFilter\Block;
use Magento\Framework\View\Element\Template;

class PurchaseOrder extends \Magento\Framework\View\Element\Template
{    
  
     protected $_orderCollectionFactory;

    public function __construct(
        Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context, $data);

    }

    public function getPurchaseOrderNumber() {
        $collection = $this->_orderCollectionFactory->create()
         ->addAttributeToSelect('*');
        foreach($collection as $item) {
            echo "<pre>";
            print_r(get_class_methods($item->getPayment()));
        }
    }

    public function getOrderCollectionByPurchaseOrder()
    {
         $collection = $this->_orderCollectionFactory->create()
          ->addAttributeToSelect('*');  
          $collection->getSelect()->join(
            array('payment' => $collection->getResource()->getTable('sales_order_payment')),
            'payment.parent_id = main_table.entity_id',
            array()
        );
        $collection->addFieldToFilter('po_number',array('like' => '%'.'LE'.'%'));
         return $collection;
     }

   public function getOrderCollectionByCustomerId($customerId)
   {
       $collection = $this->_orderCollectionFactory()->create($customerId)
         ->addFieldToSelect('*')
         ->addFieldToFilter('status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )
         ->setOrder(
                'created_at',
                'desc'
            );
 
     return $collection;

    }
}