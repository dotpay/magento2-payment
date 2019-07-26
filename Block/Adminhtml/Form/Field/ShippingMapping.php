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

namespace Dotpay\Payment\Block\Adminhtml\Form\Field;


/**
 * Shipping mapping form elements displayed in admin configuration of Dotpay payments.
 */
class ShippingMapping extends \Magento\Config\Block\System\Config\Form\Field
{


    const DOTPAY_METHODS = ['COURIER', 'POCZTA_POLSKA', 'PICKUP_POINT', 'PACZKOMAT', 'PACZKA_W_RUCHU', 'PICKUP_SHOP'];
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];

    /**
     * @var \Magento\Shipping\Model\Config $_shippingMethods A shipping config instance
     */

    protected $_shippingConfig = [];

    /**
     * @var string Location of template file
     */
    protected $_template = 'Dotpay_Payment::form/field/shippingMapping.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Shipping\Model\Config $shippingConfig, array $data = [])
    {
        $this->_shippingConfig = $shippingConfig;
        parent::__construct($context, $data);
    }

    /**
     * Add a column to array-grid
     *
     * @param string $name
     * @param array $params
     * @return void
     */
    public function addColumn($name, $label)
    {
        $this->_columns[$name] = [
            'label' => $label
        ];
    }

    /**
     * Get the grid and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        $html = $this->_toHtml();
        // doh, the object is used as singleton!
        return $html;
    }

    /**
     * Get name for cell element
     *
     * @param string $rowId
     * @param string $columnName
     * @return string
     */
    protected function _getCellInputElementId($columnName)
    {
        return $this->getElement()->getId() . '_' . $columnName;
    }

    /**
     * Get id for cell element
     *
     * @param string $columnName
     * @return string
     */
    protected function _getCellInputElementName($columnName)
    {
        return $this->getElement()->getName() . '[' . $columnName . ']';
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        foreach($this->_getStoreShippingMethods() as $method)
        {
            $this->addColumn($method['code'], __($method['title']));
        }

    }

    /**
     * Get options for a dropdown
     *
     * @param string $columnName
     * @return string
     */
    protected function _getOptions($columnName)
    {
        $array = $this->getElement()->getValue();

        if($array && isset($array[$columnName])) {
            $value = $array[$columnName];
        } else {
            $value = false;
        }

        $html = '<option value="">-</option>';
        foreach (self::DOTPAY_METHODS as $method)
        {
            $html .= '<option value="' . $method . '"'. ($value === $method ? ' selected' : '') .'>' . __($method) . '</option>';
        }
        return $html;
    }

    /**
     * Render array cell
     *
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCell($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new \Exception('Wrong column name specified.');
        }
        $column = $this->_columns[$columnName];
        $inputName = $this->_getCellInputElementName($columnName);

        return '<select id="' . $this->_getCellInputElementId($columnName) . '" name="' . $inputName .'">' .
            $this->_getOptions($columnName) .
            '</select>';
    }

    /**
     * Render block HTML
     *
     * @return string
     * @throws \Exception
     */
    protected function _toHtml()
    {

        $this->_prepareToRender();

        if (empty($this->_columns)) {
            throw new \Exception('At least one column must be defined.');
        }
        return parent::_toHtml();
    }


    /**
     * Returns columns array
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }


    /**
     * Returns array of shipping methods available in store
     *
     * @return array
     */
    private function _getStoreShippingMethods()
    {
        $allCarriers = $this->_shippingConfig->getAllCarriers();
        foreach ($allCarriers as $shippigCode => $shippingModel) {
            $shippingTitle = $this->_scopeConfig->getValue('carriers/'.$shippigCode.'/title');
            $methods[] = array(
                'title' => $shippingTitle,
                'code' => $shippigCode
            );
        }
        return $methods;
    }

}
