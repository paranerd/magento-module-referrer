<?php

namespace Paranerd\Referrer\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface    $context)
	{
    $installer = $setup;

    $installer->startSetup();

	$bonusTable = $installer->getTable('pending_bonus');

	if ($installer->getConnection()->isTableExists($bonusTable) != true) {
		$table = $installer->getConnection()
			->newTable($bonusTable)
			->addColumn(
				'id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'identity' => true,
					'unsigned' => true,
					'nullable' => false,
					'primary' => true
				],
				'ID'
			)
			->addColumn(
				'customer_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'nullable' => false,
				],
				'Customer ID'
			)
			->addColumn(
				'order_id',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'nullable' => false,
				],
				'Order ID'
			)
			->addColumn(
				'amount',
				\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
				null,
				[
					'nullable' => false,
				],
				'Amount'
			)
			->addColumn(
				'created_at',
				\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
				null,
				[
					'nullable' => false,
					'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
				],
				'Created At'
			)
			->setComment('Pending Bonus Table')
			->setOption('type', 'InnoDB')
			->setOption('charset', 'utf8');
		$installer->getConnection()->createTable($table);
	}

    $eavTable = $installer->getTable('customer_entity');

    $columns = [
        'referrer' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length' => 255,
            'nullable' => false,
            'comment' => 'The referrer',
        ],
        'custom_id' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length' => 255,
            'nullable' => false,
            'comment' => 'My Custom',
        ],
        'level' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length' => 255,
            'nullable' => false,
            'comment' => 'My Level',
        ],
        'employee' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length' => 255,
            'nullable' => false,
            'comment' => 'Company Employee',
        ],
        'last_referred' => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length' => 255,
            'nullable' => false,
            'comment' => 'Last Referred',
        ],
        'bonus' => [
            'type'		=> \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			'length' => 255,
            'nullable'	=> false,
            'comment'	=> 'Bonus',
        ],
    ];

    $connection = $installer->getConnection();
    foreach ($columns as $name => $definition) {
        $connection->addColumn($eavTable, $name, $definition);
    }

    $installer->endSetup();
}
}