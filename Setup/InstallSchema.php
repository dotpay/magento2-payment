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
 * @copyright Dotpay
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

namespace Dotpay\Payment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Installation of required database schema during installing of the payment module.
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface   $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        $this->installCardBrandsTable($installer);
        $this->installCreditCardsTable($installer);
        $this->installInstructionsTable($installer);
        $this->installRetryPaymentsTable($installer);

        $installer->endSetup();
    }

    /**
     * Install table for card brands.
     *
     * @param \Dotpay\Payment\Setup\SchemaSetupInterface $installer
     */
    private function installCardBrandsTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('dotpay_card_brands'))
            ->addColumn('entity_id', Table::TYPE_INTEGER, null, ['identity' => true,
                'nullable' => false, 'primary' => true, 'unsigned' => true, ], 'Brand id')
            ->addColumn('name', Table::TYPE_TEXT, 255, ['nullable' => false], 'Brand name')
            ->addColumn('logo', Table::TYPE_TEXT, 255, ['nullable' => false], 'Url of credit card brand logo')
            ->setComment('Brands of saved credit cards');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Instal table for credit cards.
     *
     * @param \Dotpay\Payment\Setup\SchemaSetupInterface $installer
     */
    private function installCreditCardsTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('dotpay_credit_cards'))
            ->addColumn('entity_id', Table::TYPE_INTEGER, 10, ['identity' => true, 'unsigned' => true,
                'nullable' => false, 'primary' => true, ], 'Brand id')
            ->addColumn('order_id', Table::TYPE_INTEGER, 10, ['nullable' => true, 'unsigned' => true], 'Id of first order where the card was used')
            ->addColumn('customer_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Id of card owner')
            ->addColumn('brand_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true], 'Id of card brand')
            ->addColumn('mask', Table::TYPE_TEXT, 255, ['nullable' => true], 'Masked number of the card')
            ->addColumn('customer_hash', Table::TYPE_TEXT, 255, ['nullable' => true], 'Hash of card')
            ->addColumn('card_id', Table::TYPE_TEXT, 255, ['nullable' => true], 'Card identificatior sent from Dotpay')
            ->addColumn('register_date', Table::TYPE_DATETIME, null, ['nullable' => true], 'Date when card was registered')
            ->addForeignKey(
                $installer->getFkName(
                    'dotpay_credit_cards',
                    'entity_id',
                    'sales_order',
                    'order_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )->addForeignKey(
                $installer->getFkName(
                    'dotpay_credit_cards',
                    'entity_id',
                    'customer_entity',
                    'customer_id'
                ),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    'dotpay_credit_cards',
                    'entity_id',
                    'dotpay_card_brands',
                    'brand_id'
                ),
                'brand_id',
                $installer->getTable('dotpay_card_brands'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Credit cards saved by One Click functionality');

        $installer->getConnection()->createTable($table);
    }

    /**
     * Install table for instructions of completing payments.
     *
     * @param \Dotpay\Payment\Setup\SchemaSetupInterface $installer
     */
    private function installInstructionsTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('dotpay_instructions'))
            ->addColumn('entity_id', Table::TYPE_INTEGER, 10, ['identity' => true, 'unsigned' => true,
                'nullable' => false, 'primary' => true, ], 'Instruction id')
            ->addColumn('order_id', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Id of an order having the payment which uses this instruction')
            ->addColumn('number', Table::TYPE_TEXT, 255, ['nullable' => false], 'Number of payment in Dotpay')
            ->addColumn('bank_account', Table::TYPE_TEXT, 255, ['nullable' => true], 'Number of target Dotpay bank account shown to client')
            ->addColumn('channel', Table::TYPE_INTEGER, 10, ['nullable' => false, 'unsigned' => true], 'Id of used payment channel')
            ->addColumn('hash', Table::TYPE_TEXT, 255, ['nullable' => false], 'Hash of payment')
            ->addColumn('amount', Table::TYPE_DECIMAL, '12,4', ['nullable' => false], 'Amount of money to pay')
            ->addColumn('currency', Table::TYPE_TEXT, 255, ['nullable' => false], 'Currency of payment')
            ->addForeignKey(
                $installer->getFkName(
                    'dotpay_instructions',
                    'entity_id',
                    'sales_order',
                    'order_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Instructions for non-auutomatic payments');

        $installer->getConnection()->createTable($table);
    }

    private function installRetryPaymentsTable(\Magento\Framework\Setup\SchemaSetupInterface $installer)
    {
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
}
