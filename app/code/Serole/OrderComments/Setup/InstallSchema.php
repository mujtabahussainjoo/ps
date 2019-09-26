<?php
namespace Serole\OrderComments\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();
 
        if ($connection->tableColumnExists('sales_order_status_history', 'admin_user') === false) {
            $connection
                ->addColumn(
                    $setup->getTable('sales_order_status_history'),
                    'admin_user',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 0,
                        'comment' => 'Admin User Name'
                    ]
                );
        }
        $installer->endSetup();
	}
}