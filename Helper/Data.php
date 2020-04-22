<?php
/**
 * @category   Divalto
 * @package    Divalto_ExtraPrice
 * @subpackage Helper
 */
namespace Divalto\ExtraPrice\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Helper\Data as TaxHelper;

/**
 * Class Data
 * @package Divalto\ExtraPrice\Helper
 */
class Data extends AbstractHelper
{

    const XML_PATH_EXTRAPRICE = 'extraprice/';

    /**
     * @var PriceHelper
     */
    protected $priceHelper;

    /**
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PriceHelper $priceHelper
     */

    public function __construct(
        Context $context,
        PriceHelper $priceHelper,
        TaxHelper $taxHelper
    ){
        $this->priceHelper = $priceHelper;
        $this->taxHelper = $taxHelper;
        parent::__construct($context);
    }

    /**
     * Returns the extra price text
     *
     * @param Product $product
     * @return mixed
     */
    public function getExtraPriceText(Product $product)
    {
        $ExtraPrice = $this->getExtraPrice($product);

        if (!$ExtraPrice) return '';
        return $this->priceHelper->currency($ExtraPrice);
    }

    /**
     * Calculates the extra price for given product
     *
     * @return float|string
     */
    public function getExtraPrice(Product $product, $taxInc = false)
    {
        if($taxInc) {
            $productPrice = round($product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(), PriceCurrencyInterface::DEFAULT_PRECISION);
        } else {
            $productPrice = round($product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount(), PriceCurrencyInterface::DEFAULT_PRECISION);
        }

        $extraPriceProductUnit = $product->getData('extra_price_unit_amount') ?? 1;

        $ExtraPrice = $productPrice / $extraPriceProductUnit;

        return $ExtraPrice;
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_EXTRAPRICE .'general/'. $code, $storeId);
    }

    public function displayPriceIncludingTax() 
    {
         return $this->taxHelper->displayPriceIncludingTax();

    }
}
