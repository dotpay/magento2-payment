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

namespace Dotpay\Payment\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Dotpay\Model\CreditCard as SdkCreditCard;

/**
 * Resource model of saved credit cards for One Click method.
 */
class CreditCard extends AbstractDb
{
    /**
     * Pseudoconstructor with initialization of the model.
     */
    protected function _construct()
    {
        $this->_init('dotpay_credit_cards', 'entity_id');
    }

    /**
     * Process post data before saving.
     *
     * @param \Magento\Framework\Model\AbstractModel $object Model object containing data to save
     *
     * @return CreditCard
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getCustomerHash() == null) {
            $object->setCustomerHash(SdkCreditCard::generateNewUserId());
        }
        if ($object->getRegisterDate() == null) {
            $object->setRegisterDate(new \DateTime());
        }

        return parent::_beforeSave($object);
    }
}
