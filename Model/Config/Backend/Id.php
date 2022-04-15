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

namespace Dotpay\Payment\Model\Config\Backend;

use Dotpay\Validator\Id as IdValidator;
use Dotpay\Exception\BadParameter\IdException;

/**
 * Model of seller identifier used in module configuration.
 */
class Id extends Value
{
    /**
     * Check if the given seller identifier is correct.
     *
     * @return $this
     *
     * @throws PinException Thrown if identifier value is incorrect
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if ($this->isEnabled() && !IdValidator::validate($value)) {
            throw new IdException($value);
        }

        return parent::beforeSave();
    }


}
