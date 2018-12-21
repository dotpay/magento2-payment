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
 * Interface of card brand model.
 */
interface OrderRetryPaymentInterface
{
    /**
     * Column name of brand id in database.
     */
    const LINK_ID = 'entity_id';

    /**
     * Column name of brand name in database.
     */
    const ORDER_ID = 'order_id';

    /**
     * Column name of brand logo in database.
     */
    const URL = 'url';

    /**
     * Column name of brand logo in database.
     */
    const TOKEN = 'token';

    /**
     * Return id of card brand.
     *
     * @return int
     */
    public function getId();

    /**
     * Set id of card brand.
     *
     * @param int $id id of card brand
     *
     * @return $this
     */
    public function setId($id);

    /**
     * Return name of card brand.
     *
     * @return string
     */
    public function getOrderId();

    /**
     * Set name of card brand.
     *
     * @param string $orderId Name of card brand
     *
     * @return $this
     */
    public function setOrderId($orderId);

    /**
     * Return url of card brand's logo.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set url of card brand's logo.
     *
     * @param string $url Url of card brand
     *
     * @return $this
     */
    public function setUrl($url);

    /**
     * Return url of card brand's logo.
     *
     * @return string
     */
    public function getToken();

    /**
     * Set url of card brand's logo.
     *
     * @param string $token Url of card brand
     *
     * @return $this
     */
    public function setToken($token);

    /**
     * Return url of card brand's logo.
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set url of card brand's logo.
     *
     * @param string $createdAt Url of card brand
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
