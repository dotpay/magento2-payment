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

namespace Dotpay\Payment\Model\Resource\OrderRetryPayment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection of card brands.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string Name of column with identifier of saved card brands
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Pseudoconstructor with initialization of the collection.
     */
    protected function _construct()
    {
        $this->_init(
            'Dotpay\Payment\Model\OrderRetryPayment',
            'Dotpay\Payment\Model\Resource\OrderRetryPayment'
        );
    }
}
