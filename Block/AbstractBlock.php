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

namespace Dotpay\Payment\Block;

/**
 * Abstract block for other Dotpay blocks.
 */
abstract class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry Magento registry
     */
    protected $coreRegistry;

    /**
     * Initialize the block.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Return data value located under the given key.
     *
     * @param string/null $key Key of data field
     *
     * @return mixed
     */
    public function getPageData($key = null)
    {
        $allData = $this->coreRegistry->registry('data');
        if ($key) {
            if (isset($allData[$key])) {
                return $allData[$key];
            } else {
                return null;
            }
        }

        return $allData;
    }
}
