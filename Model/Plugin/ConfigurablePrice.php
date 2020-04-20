<?php
/**
 * @category   Divalto
 * @package    Divalto_ExtraPrice
 * @subpackage Model
 */
namespace Divalto\ExtraPrice\Model\Plugin;

/**
 * Class ConfigurablePrice
 * @package Divalto\ExtraPrice\Model\Plugin
 */
class ConfigurablePrice
{
    /**
     * @var \Divalto\ExtraPrice\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $_jsonDecoder;

    /**
     * Constructor
     *
     * @param \Divalto\ExtraPrice\Helper\Data $helper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     */
    public function __construct(
        \Divalto\ExtraPrice\Helper\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder
    ){
        $this->_helper = $helper;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
    }

    /**
     * Plugin for configurable price rendering. Iterates over configurable's simples and adds the base price
     * to price configuration.
     *
     * @param \Magento\Framework\Pricing\Render $subject
     * @param $json string
     * @return string
     */
    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $json)
    {
        $config = $this->_jsonDecoder->decode($json);

        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($subject->getAllowProducts() as $product) {
            $extraPriceText = $this->_helper->getExtraPriceText($product);

            if (empty($extraPriceText)) {
                // if simple has no configured base price, us at least the base price of configurable
                $extraPriceText = $this->_helper->getExtraPriceText($subject->getProduct());
            }

            $config['optionPrices'][$product->getId()]['divalto_extraprice_text'] = $extraPriceText;
        }

        return $this->_jsonEncoder->encode($config);
    }
}