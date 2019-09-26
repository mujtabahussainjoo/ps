<?php

namespace Serole\Specialproduct\Cron;
 
class Specialproduct
{
	protected $logger;
	
	protected $indexerFactory;
 
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface,
		\Magento\Indexer\Model\IndexerFactory $indexerFactory
	) {
		$this->logger = $loggerInterface;
		$this->indexerFactory = $indexerFactory;
	}
 
	public function execute() {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$date = date("Y-m-d");
		$date = $date." 00:00:00";
		$query = "SELECT entity_id FROM  `catalog_product_entity_decimal` 
		          WHERE  `attribute_id` =76 
				  AND  `value` >0 
				  AND entity_id IN (SELECT entity_id FROM  `catalog_product_entity_datetime` WHERE  `attribute_id` =78 AND ( `value` >=  '$date' OR `value` IS NULL))";
		$result = $connection->fetchAll($query); 
	    
        $deleteQuery = "delete from `catalog_category_product` WHERE  `category_id` =124";	
        $connection->query($deleteQuery); 
   
        if(count($result) > 0)
		{
			foreach($result as $prod)
			{
				$prodId = $prod['entity_id'];
				$insertQuery = "insert into `catalog_category_product` (category_id, product_id) VALUES ('124', '$prodId')";
				$connection->query($insertQuery); 
			}
		}	
		$indexerIds = array('catalog_category_product','catalog_product_category','catalog_product_price','catalog_product_attribute','cataloginventory_stock','catalogrule_product','catalogsearch_fulltext');
		foreach ($indexerIds as $indexerId) {
          $indexer = $this->indexerFactory->create();
		  $indexer->load($indexerId);
		  $indexer->reindexAll();	
        }
        echo "Done";		

	}
}