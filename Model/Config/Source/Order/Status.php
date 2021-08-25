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

namespace Dotpay\Payment\Model\Config\Source\Order;

use Magento\Sales\Model\Config\Source\Order\Status as MagentoStatus;

/**
 * Model of list of all statuses including pending payment states.
 */
class Status extends MagentoStatus
{
    /**
     * Initialize the model.
     *
     * @param \Magento\Sales\Model\Order\Config $orderConfig Magneto order configuration
     */
    public function __construct(\Magento\Sales\Model\Order\Config $orderConfig)
    {
        parent::__construct($orderConfig);
        $this->_stateStatuses = array_merge($this->_stateStatuses, [
            \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT,
        ]);
    }
}
