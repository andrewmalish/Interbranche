<?php
/**
 * @package     Interbranche\QuickView
 * @version     1.0.0
 */

namespace Interbranche\QuickView\Block;

use \Magento\Catalog\Block\Product\Context;

class QuickView extends \Magento\Catalog\Block\Product\ProductList\Item\Block
{
    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $localeFormat;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * QuickView constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $config
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Json\EncoderInterface $encoder
     * @param \Magento\Framework\Locale\FormatInterface $format
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $config,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Json\EncoderInterface $encoder,
        \Magento\Framework\Locale\FormatInterface $format,
        array $data = []
    ) {
        $this->localeFormat = $format;
        $this->jsonEncoder = $encoder;
        $this->urlHelper = $urlHelper;
        $this->productTypeConfig = $config;
        parent::__construct($context, $data);
    }

    /**
     * Get default qty - either as preconfigured, or as 1.
     * Also restricts it by minimal qty.
     *
     * @param null|\Magento\Catalog\Model\Product $product
     * @return int|float
     */
    public function getProductDefaultQty($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $qty = $this->getMinimalQty($product);
        $config = $product->getPreconfiguredValues();
        $configQty = $config->getQty();
        if ($configQty > $qty) {
            $qty = $configQty;
        }

        return $qty;
    }

    /**
     * Whether redirect to cart enabled
     *
     * @return bool
     */
    public function isRedirectToCartEnabled()
    {
        return $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check whether quantity field should be rendered
     *
     * @return bool
     */
    public function shouldRenderQuantity()
    {
        return !$this->productTypeConfig->isProductSet($this->getProduct()->getTypeId());
    }

    /**
     * Get Validation Rules for Quantity field
     *
     * @return array
     */
    public function getQuantityValidators()
    {
        $validators = [];
        $validators['required-number'] = true;
        return $validators;
    }

    /**
     * Check if product has required options
     *
     * @return bool
     */
    public function hasRequiredOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasRequiredOptions($this->getProduct());
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    public function hasOptions()
    {
        if ($this->getProduct()->getTypeInstance()->hasOptions($this->getProduct())) {
            return true;
        }
        return false;
    }

    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();

        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->localeFormat->getPriceFormat()
            ];
            return $this->jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $tierPrice['price']->getValue();
        }
        $config = [
            'productId'   => $product->getId(),
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'prices'      => [
                'oldPrice'   => [
                    'amount'      => $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(),
                    'adjustments' => []
                ],
                'basePrice'  => [
                    'amount'      => $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount(),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount'      => $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(),
                    'adjustments' => []
                ]
            ],
            'idSuffix'    => '_clone',
            'tierPrices'  => $tierPrices
        ];

        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->jsonEncoder->encode($config);
    }
}
