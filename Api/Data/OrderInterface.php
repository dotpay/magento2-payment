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

namespace Dotpay\Payment\Api\Data;

/**
 * Interface providing ids of new Dotpay order statuses.
 */
interface OrderInterface
{
    /**
     * Id of status pending.
     */
    const STATUS_PENDING = 'dotpay_pending';

    /**
     * Id of status complete.
     */
    const STATUS_COMPLETE = 'dotpay_complete';

    /**
     * Id of status canceled.
     */
    const STATUS_CANCELED = 'dotpay_canceled';

    /**
     * Id of status possible duplication.
     */
    const STATUS_DUPLICATE = 'dotpay_duplicate';

    /**
     * Id of status refunded.
     */
    const STATUS_REFUND_NEW = 'dotpay_refund_new';

    /**
     * Id of status refunded.
     */
    const STATUS_REFUNDED = 'dotpay_refunded';

    /**
     * Id of status failed to refund.
     */
    const STATUS_REFUND_FAILED = 'dotpay_refund_failed';
}
