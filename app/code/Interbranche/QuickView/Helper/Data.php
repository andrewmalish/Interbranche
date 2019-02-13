<?php
/**
 * @package     Interbranche\QuickView
 * @version     1.0
 */
namespace Interbranche\QuickView\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\View\Element\BlockFactory;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepo;
use Magento\Catalog\Helper\Image;


/**
 * Class Data
 * @package Interbranche\QuickView\Helper
 */
class Data extends AbstractHelper {
    /**
     * @var \Magento\Catalog\Model\Product\Gallery\ReadHandler
     */
    protected $images;

    /**
     * @var ProductRepo
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Config\View
     */
    private $configView;

    /**
     * View config model
     *
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $viewConfig;

    /**
     * @var \Magento\Catalog\Block\Product\View\Gallery
     */
    private $galleryBlock;

    /**
     * @var \Interbranche\QuickView\Block\Price
     */
    private $priceBlock;

    /**
     * @var \Interbranche\QuickView\Block\QuickView
     */
    private $quickView;

    /**
     * @var \Interbranche\AttributeBadges\Helper\BadgeHelper
     */
    private $badgeHelper;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    private $cartHelper;

    /**
     * @var \Magento\Framework\View\Element\FormKey
     */
    private $formKey;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Magento\Framework\View\DesignInterface
     */
    private $design;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param BlockFactory $blockFactory
     * @param ScopeConfig $scopeConfig
     * @param ProductRepo $productRepository
     * @param \Magento\Catalog\Model\Product\Gallery\ReadHandler $images
     * @param \Magento\Framework\View\ConfigInterface $config
     * @param \Magento\Catalog\Block\Product\View\Gallery $gallery
     * @param \Interbranche\QuickView\Block\Price $price
     * @param \Interbranche\QuickView\Block\QuickView $quickView
     * @param \Interbranche\AttributeBadges\Helper\BadgeHelper $badgeHelper
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\View\Element\FormKey $formKey
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\View\LayoutInterface $layout
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        BlockFactory $blockFactory,
        ScopeConfig $scopeConfig,
        ProductRepo $productRepository,
        \Magento\Catalog\Model\Product\Gallery\ReadHandler $images,
        \Magento\Framework\View\ConfigInterface $config,
        \Magento\Catalog\Block\Product\View\Gallery $gallery,
        \Interbranche\QuickView\Block\Price $price,
        \Interbranche\QuickView\Block\QuickView $quickView,
        \Interbranche\AttributeBadges\Helper\BadgeHelper $badgeHelper,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\View\Element\FormKey $formKey,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $this->images = $images;
        $this->blockFactory = $blockFactory;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->viewConfig = $config;
        $this->galleryBlock = $gallery;
        $this->priceBlock = $price;
        $this->quickView = $quickView;
        $this->badgeHelper = $badgeHelper;
        $this->cartHelper = $cartHelper;
        $this->formKey = $formKey;
        $this->layout = $layout;
        $this->design = $design;
        $this->layout->getUpdate()->addHandle('default');
        $this->design->setDesignTheme($this->design->getConfigurationDesignTheme());
        parent::__construct($context);

    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductDataAsJson(\Magento\Catalog\Api\Data\ProductInterface $product) {
        $this->galleryBlock->setData('product', $product);
        $result['success']          = true;
        $result['galleryJs']        = $this->getGallery();
        $result['productPrice']     = $this->getPrice($product);
        $result['hasSpecialPrice']  = $product->hasSpecialPrice();
        $result['productLink']      = $product->getProductUrl();
        $result['addToCartUrl']     = $this->cartHelper->getAddUrl($product);
        $result['formKey']          = $this->formKey->toHtml();
        $result['isSaleable']       = $product->isSaleable();
        $result['optionsContainer'] = $this->getOptionsContainer($product);
        $result['hasOptions']       = $this->hasOptions($product);
        $result['productOptions']   = $result['hasOptions'] ? $this->getOptionBlock($product) : '';
        $result['addToCart']        = $this->getAddToCart($product);
        $result['priceBoxJs']       = $this->getPriceBox($product);
        $result['badge']            = $this->getBadge($product) ?: '';
        $result['uom']              = $product->getAttributeText('uom');
        $result['videoBlock']       = $this->getProductVideoGallery($product);

        $product = $this->images->execute($product);
        $result = array_merge($product->getData(), $result);

        return json_encode($result);
    }

    /**
     * @param $product
     * @return mixed
     */
    private function getProductVideoGallery($product)
    {
        return $this->layout
            ->createBlock('\Magento\ProductVideo\Block\Product\View\Gallery')
            ->setTemplate('Magento_ProductVideo::product/view/gallery.phtml')
            ->setProduct($product)
            ->toHtml();
    }

    /**
     * @param $product
     * @return \Interbranche\AttributeBadges\Model\AttributeBadge|mixed|null|string
     */
    private function getBadge($product)
    {
        $badge = $this->badgeHelper->getAttributeBadgeByProductId($product->getId());
        if($badge != null && $this->badgeHelper->isAttributeBadgesEnabledHere('product') !== false
            && $this->badgeHelper->isGlobalEnable()) {
            $badge = $badge->getData();
            return $badge;
        }
        return '';
    }

    /**
     * @param $product
     * @return string
     */
    private function getPriceBox($product)
    {
        $this->quickView->setProduct($product);
        $priceJs = '
            <script>
                require([
                    "jquery",
                    "priceBox"
                ], function($){
                    var dataPriceBoxSelector = "[data-role=priceBox]",
                    dataProductIdSelector = "[data-product-id=' . $product->getId() . ']",
                    priceBoxes = $(dataPriceBoxSelector + dataProductIdSelector);
                    priceBoxes = priceBoxes.filter(function(index, elem){
                        return !$(elem).find(".price-from").length;
                    });
                    priceBoxes.priceBox({"priceConfig": ' . $this->quickView->getJsonConfig() . '});
                    });
            </script>';
        return $priceJs;
    }
    /**
     * @param $product
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAddToCart($product)
    {
        return $this->layout
            ->createBlock('Interbranche\QuickView\Block\QuickView')
            ->setTemplate('Magento_Catalog::product/quickview/addto_button.phtml')
            ->setProduct($product)
            ->toHtml();
    }

    /**
     * @param $product
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getOptionBlock($product)
    {
        $optionWrapper = $this->layout
            ->createBlock('Interbranche\QuickView\Block\QuickView')
            ->setTemplate('Magento_Catalog::product/quickview/wrapper.phtml')
            ->setProduct($product);

        if ($product->getTypeId() == 'configurable') {
            $configurableOption = $this->layout
                ->createBlock('Magento\ConfigurableProduct\Block\Product\View\Type\Configurable')
                ->setTemplate('Interbranche_QuickView::product/category/type/options/configurable.phtml');

            $optionWrapper->setChild('options_configurable', $configurableOption);
        }
        $productOptions = $this->layout
            ->createBlock('Magento\Catalog\Block\Product\View\Options')
            ->setTemplate('Magento_Catalog::product/view/options.phtml')
            ->setProduct($product);

        $defaulOption = $this->layout
            ->createBlock('Magento\Catalog\Block\Product\View\Options\Type\DefaultType')
            ->setTemplate('Magento_Catalog::product/view/options/type/default.phtml');

        $productOptions->setChild('product.info.options.default', $defaulOption);

        $textOption = $this->layout
            ->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Text')
            ->setTemplate('Magento_Catalog::product/view/options/type/text.phtml');

        $productOptions->setChild('product.info.options.text', $textOption);

        $fileOption = $this->layout
            ->createBlock('Magento\Catalog\Block\Product\View\Options\Type\File')
            ->setTemplate('Magento_Catalog::product/view/options/type/file.phtml');

        $productOptions->setChild('product.info.options.file', $fileOption);

        $selectOption = $this->layout
            ->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Select')
            ->setTemplate('Magento_Catalog::product/view/options/type/select.phtml');

        $productOptions->setChild('product.info.options.select', $selectOption);

        $dateOption = $this->layout
            ->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Date')
            ->setTemplate('Magento_Catalog::product/view/options/type/date.phtml');

        $productOptions->setChild('product.info.options.date', $dateOption);

        $optionWrapper->setChild('product_options', $productOptions);

        $calendar = $this->layout
            ->createBlock('Magento\Framework\View\Element\Html\Calendar')
            ->setTemplate('Magento_Theme::js/calendar.phtml');

        $optionWrapper->setChild('html_calendar', $calendar);

        return $optionWrapper->toHtml();
    }

    /**
     * Return true if product has options
     *
     * @return bool
     */
    private function hasOptions($product)
    {
        if ($product->getTypeInstance()->hasOptions($product)) {
            return true;
        }
        return false;
    }

    /**
     * Get container name, where product options should be displayed
     *
     * @return string
     */
    private function getOptionsContainer($product)
    {
        return $product->getOptionsContainer() == 'container1' ? 'container1' : 'container2';
    }

    /**
     * @param $product
     * @return string
     */
    private function getPrice($product)
    {
        return $this->priceBlock->getProductPrice($product);
    }

    /**
     * @return string
     */
    private function getGallery()
    {
        $galleryJs = '
            <div class="gallery-placeholder _block-content-loading" data-gallery-role="gallery-placeholder">
                <div data-role="loader" class="loading-mask">
                    <div class="loader">
                        <img src="' . $this->galleryBlock->getViewFileUrl("images/loader-1.gif") . '"
                             alt="' . __("Loading...") . '">
                    </div>
                </div>
            </div>
            <script type="text/x-magento-init">
                {
                    "[data-gallery-role=gallery-placeholder]": {
                        "mage/gallery/gallery": {
                            "mixins":["magnifier/magnify"],
                            "magnifierOpts": ' . $this->galleryBlock->getMagnifier() . ',
                            "data": ' . $this->galleryBlock->getGalleryImagesJson() . ',
                            "options": {
                                "nav": "' . $this->galleryBlock->getVar("gallery/nav") . '",';
        if ($this->galleryBlock->getVar("gallery/loop")) {
            $galleryJs .=  '"loop": ' . $this->galleryBlock->getVar("gallery/loop") . ',';

        }
        if ($this->galleryBlock->getVar("gallery/keyboard")){
            $galleryJs .= '"keyboard": ' . $this->galleryBlock->getVar("gallery/keyboard") . ',';
        }
        if ($this->galleryBlock->getVar("gallery/arrows")) {
            $galleryJs .= '"arrows": ' . $this->galleryBlock->getVar("gallery/arrows") . ',';
        }
        if ($this->galleryBlock->getVar("gallery/allowfullscreen")) {
            $galleryJs .= '"allowfullscreen": ' . $this->galleryBlock->getVar("gallery/allowfullscreen") . ',';
        }
        if ($this->galleryBlock->getVar("gallery/caption")) {
            $galleryJs .= '"showCaption": ' . $this->galleryBlock->getVar("gallery/caption") .',';
        }
        $galleryJs .= '"width": "' . $this->galleryBlock->getImageAttribute("product_page_image_medium", "width") . '",
            "thumbwidth": "' . $this->galleryBlock->getImageAttribute("product_page_image_small", "width") . '",';
        if ($this->galleryBlock->getImageAttribute("product_page_image_small", "height")
            || $this->galleryBlock->getImageAttribute("product_page_image_small", "width")) {
            $galleryJs .= '"thumbheight": ' . $this->galleryBlock->getImageAttribute("product_page_image_small", "height")
                ?: $this->galleryBlock->getImageAttribute("product_page_image_small", "width");
            $galleryJs .= ',';
        }
        if ($this->galleryBlock->getImageAttribute("product_page_image_medium", "height")
            || $this->galleryBlock->getImageAttribute("product_page_image_medium", "width")) {
            $galleryJs .= '"height": ' . $this->galleryBlock->getImageAttribute("product_page_image_medium", "height")
                ?: $this->galleryBlock->getImageAttribute("product_page_image_medium", "width");
            $galleryJs .= ',';
        }
        if ($this->galleryBlock->getVar("gallery/transition/duration")) {
            $galleryJs .= '"transitionduration": ' . $this->galleryBlock->getVar("gallery/transition/duration") . ',';
        }
        $galleryJs .= '"transition": "' . $this->galleryBlock->getVar("gallery/transition/effect") . '",';
        if ($this->galleryBlock->getVar("gallery/navarrows")) {
            $galleryJs .= '"navarrows": ' . $this->galleryBlock->getVar("gallery/navarrows") . ',';
        }
        $galleryJs .= '"navtype": "' . $this->galleryBlock->getVar("gallery/navtype") . '",
            "navdir": "' . $this->galleryBlock->getVar("gallery/navdir") . '"
            },
            "fullscreen": {
                "nav": "' . $this->galleryBlock->getVar("gallery/fullscreen/nav") . '",';
        if ($this->galleryBlock->getVar("gallery/fullscreen/loop")) {
            $galleryJs .= '"loop": ' . $this->galleryBlock->getVar("gallery/fullscreen/loop") . ',';
        }
        $galleryJs .= '"navdir": "' . $this->galleryBlock->getVar("gallery/fullscreen/navdir") . '",';
        if ($this->galleryBlock->getVar("gallery/transition/navarrows")){
            $galleryJs .= '"navarrows": ' . $this->galleryBlock->getVar("gallery/fullscreen/navarrows") . ',';
        }
        $galleryJs .= '"navtype": "' . $this->galleryBlock->getVar("gallery/fullscreen/navtype") . '",';
        if ($this->galleryBlock->getVar("gallery/fullscreen/arrows")) {
            $galleryJs .= '"arrows": ' . $this->galleryBlock->getVar("gallery/fullscreen/arrows") . ',';
        }
        if ($this->galleryBlock->getVar("gallery/fullscreen/caption")) {
            $galleryJs .= '"showCaption": ' . $this->galleryBlock->getVar("gallery/fullscreen/caption") . ',';
        }
        if ($this->galleryBlock->getVar("gallery/fullscreen/transition/duration")) {
            $galleryJs .= '"transitionduration": ' . $this->galleryBlock->getVar("gallery/fullscreen/transition/duration") . ',';
        }
        $galleryJs .= '"transition": "' . $this->galleryBlock->getVar("gallery/fullscreen/transition/effect") . '"
            },
            "breakpoints": ' . $this->galleryBlock->getBreakpoints() . '
            }
        }}
        </script>';

        return $galleryJs;
    }
}
