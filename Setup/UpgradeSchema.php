<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to tech@dotpay.pl so we can send you a copy immediately.
 *
 * @author    Dotpay Team <tech@dotpay.pl>
 * @copyright PayPro S.A.
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Dotpay\Payment\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

    /**
 * Upgrade of required database schema during upgrading of the payment module.
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;

        $installer->startSetup();

        if(version_compare($context->getVersion(), '1.0.9.2', '<')) {

            $table = $installer->getConnection()
                ->newTable($installer->getTable('dotpay_order_retry_payments'))
                ->addColumn('entity_id', Table::TYPE_INTEGER, 10, ['identity' => true, 'unsigned' => true,
                    'nullable' => false, 'primary' => true, ], 'Payment id')
                ->addColumn('order_id', Table::TYPE_INTEGER, 10, ['nullable' => true, 'unsigned' => true], 'Id of the order')
                ->addColumn('url', Table::TYPE_TEXT, 255, ['nullable' => true], 'Payment url')
                ->addColumn('token', Table::TYPE_TEXT, 255, ['nullable' => true], 'Request token')
                ->addColumn('created_date', Table::TYPE_DATETIME, null, ['nullable' => true], 'Date when link was created')
                ->addForeignKey(
                    $installer->getFkName(
                        'dotpay_order_retry_payments',
                        'entity_id',
                        'sales_order',
                        'order_id'
                    ),
                    'order_id',
                    $installer->getTable('sales_order'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
                )
                ->setComment('Retry payment links generated for orders');

            $installer->getConnection()->createTable($table);
        }

        if(version_compare($context->getVersion(), '1.0.13', '<')) {

            $tableName = $setup->getTable('dotpay_instructions');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'title' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => 'Title of the wire transfer',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        $installer->endSetup();
    }
}
