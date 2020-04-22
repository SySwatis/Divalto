<?php
/**
 * @category   Divalto
 * @package    Divalto_ExtraPrice
 * @subpackage Block
 */
namespace Divalto\ExtraPrice\Block;

/**
 * Class AfterPrice
 * @package Divalto\ExtraPrice\Block
 */
class AfterPrice extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Divalto\ExtraPrice\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Divalto\ExtraPrice\Helper\Data $helper
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
        \Divalto\ExtraPrice\Helper\Data $helper,
        \Magento\Catalog\Model\Product $product,
		array $data = []
	){
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_helper = $helper;
        $this->_product = $product;
		parent::__construct($context, $data);
	}

    /**
     * Returns the configuration if module is enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        $moduleEnabled = $this->_helper->getGeneralConfig('enabled');

        $attributeCode = $this->_helper->getGeneralConfig('attribute_code') ?? 'extra_price_unit_amount';

        $productAmount = $this->getProduct()->getData($attributeCode);

        return $moduleEnabled && !empty($productAmount);
    }


	/**
	 * Retrieve current product
	 *
	 * @return \Magento\Catalog\Model\Product
	 */
	public function getProduct()
	{
        return $this->_product;
	}

    /**
     * Returns the base price information
     */
    public function getExtraPriceText()
    {
        return $this->_helper->getExtraPriceText($this->getProduct());
    }

    public function getDisplayExtraPriceTax()
    {
         return $this->_helper->displayPriceIncludingTax() ? 'Incl. Tax' : 'Excl. Tax';
    }

}