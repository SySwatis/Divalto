<?php
/**
 * @category   Divalto
 * @package    Divalto_ExtraPrice
 * @subpackage Model
 */
namespace Divalto\ExtraPrice\Model\Plugin;

use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\LayoutInterface;

/**
 * Class AfterPrice
 * @package Divalto\ExtraPrice\Model\Plugin
 */
class AfterPrice
{
    /**
     * Hold final price code
     *
     * @var string
     */
    const FINAL_PRICE = 'final_price';

    /**
     * Hold tier price code
     *
     * @var string
     */
    const TIER_PRICE = 'tier_price';

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var []
     */
    protected $afterPriceHtml = [];

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout
    ){
        $this->layout = $layout;
    }

    /**
     * Plugin for price rendering in order to display after price information
     *
     * @param Render $subject
     * @param $renderHtml string
     * @return string
     */
    public function aroundRender(Render $subject, \Closure $closure, ...$params)
    {
        // run default render first
        $renderHtml = $closure(...$params);

        try{
            // Get Price Code and Product
            list($priceCode, $productInterceptor) = $params;
            $emptyTierPrices = empty($productInterceptor->getTierPrice());

            // If it is final price block and no tier prices exist set additional render
            // If it is tier price block and tier prices exist set additional render
            if ((static::FINAL_PRICE === $priceCode && $emptyTierPrices) || (static::TIER_PRICE === $priceCode && !$emptyTierPrices)) {
                $renderHtml .= $this->getAfterPriceHtml($productInterceptor);
            }
        } catch (\Exception $ex) {
            // if an error occurs, just render the default since it is preallocated
            return $renderHtml;
        }

        return $renderHtml;
    }

    /**
     * Renders and caches the after price html
     *
     * @return null|string
     */
    protected function getAfterPriceHtml(SaleableInterface $product)
    {
        // check if product is available
        if (!$product) return '';

        // if a grouped product is given we need the current child
        if ($product->getTypeId() == 'grouped') {
            $product = $product->getPriceInfo()
                ->getPrice(FinalPrice::PRICE_CODE)
                ->getMinProduct();

            // check if we found a product
            if (!$product) return '';
        }

        // check if price for current product has been rendered before
        if (!array_key_exists($product->getId(), $this->afterPriceHtml)) {
            $afterPriceBlock = $this->layout->createBlock(
                'Divalto\ExtraPrice\Block\AfterPrice',
                'extraprice_afterprice_' . $product->getId(),
                ['product' => $product]
            );

            // use different templates for configurables and other product types
            if ($product->getTypeId() == 'configurable') {
                $templateFile = 'Divalto_ExtraPrice::configurable/extraprice.phtml';
            } else {
                $templateFile = 'Divalto_ExtraPrice::extraprice.phtml';
            }

            $afterPriceBlock->setTemplate($templateFile);
            $this->afterPriceHtml[$product->getId()] = $afterPriceBlock->toHtml();
        }

        return $this->afterPriceHtml[$product->getId()];
    }
}
