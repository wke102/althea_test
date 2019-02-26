<?php
namespace Karl\Test\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'entity_id';
	protected $_eventPrefix = 'catalog_product_entity_collection';
	protected $_eventObject = 'post_collection';	

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	// protected function _construct()
	// {
	// 	$this->_init('Karl\Test\Model\Post', 'Karl\Test\Model\ResourceModel\Post');
	// }

	// const YOUR_TABLE = 'alt_catalog_product_entity';


	public function __construct(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
	) {
        
        $this->_init('Karl\Test\Model\Post', 'Karl\Test\Model\ResourceModel\Post');

         parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );

        $this->storeManager = $storeManager;

    }
   

    protected function _initSelect()
    {
        parent::_initSelect();

        $pending_sub = new \Zend_Db_Expr("(SELECT SUM(oi.qty_invoiced) as qty_pending, product_id FROM alt_sales_order o 
										LEFT JOIN alt_sales_order_item oi ON o.entity_id = oi.order_id
										WHERE o.status = 'pending'
										GROUP BY oi.product_id)");

        $processing_sub = new \Zend_Db_Expr("(SELECT SUM(oi.qty_invoiced) as qty_processing, product_id FROM alt_sales_order o 
											LEFT JOIN alt_sales_order_item oi on o.entity_id = oi.order_id
											WHERE o.status = 'processing'
											GROUP BY oi.product_id)");

        $this->getSelect()->joinLeft(
                ['secondTable' => $this->getTable('cataloginventory_stock_item')],
                'main_table.entity_id = secondTable.product_id',
                ['qty']
            );

        $this->getSelect()
        ->joinLeft(
        	['t' => $pending_sub],
        	't.product_id = main_table.entity_id',
        	['qty_pending'] 
        )
        ->joinLeft(
        	['x' => $processing_sub],
        	'x.product_id = main_table.entity_id',
        	['qty_processing'] 
        )        
        ->columns([
        	'qty_pending' => new \Zend_Db_Expr("COALESCE(SUM(qty_pending), 0)"),
        	'qty_processing' => new \Zend_Db_Expr("COALESCE(SUM(qty_processing), 0)"),
        	'qty_actual' => new \Zend_Db_Expr("(qty - COALESCE(SUM(qty_pending), 0) - COALESCE(SUM(qty_processing), 0))"),
        ])
        
        ->group('main_table.entity_id');

    }

}
