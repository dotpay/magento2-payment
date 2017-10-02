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

namespace Dotpay\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use Dotpay\Payment\Api\Data\CardBrandInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Dotpay\Model\CardBrand as SdkCardBrand;

/**
 * Model of credit card's brand
 */
class CardBrand extends AbstractModel implements CardBrandInterface, IdentityInterface
{
    /**
     * Identifier of cache tag.
     */
    const CACHE_TAG = 'dotpay_card_brand';

    /**
     * Pseudoconstructor for binding model with its resource.
     */
    protected function _construct()
    {
        $this->_init('Dotpay\Payment\Model\Resource\CardBrand');
    }

    /**
     * Return list of identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Return id of card brand.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::BRAND_ID);
    }

    /**
     * Set id of card brand.
     *
     * @param int $id id of card brand
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::BRAND_ID, $id);
    }

    /**
     * Return name of card brand.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name of card brand.
     *
     * @param string $name Name of card brand
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Return url of card brand's logo.
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->getData(self::LOGO);
    }

    /**
     * Set url of card brand's logo.
     *
     * @param string $logo Url of card brand
     *
     * @return $this
     */
    public function setLogo($logo)
    {
        return $this->setData(self::LOGO, $logo);
    }

    /**
     * Return SDK model of credit card's brand.
     *
     * @return SdkCardBrand
     */
    public function getSdkObject()
    {
        return new SdkCardBrand($this->getName(), $this->getLogo());
    }
}
