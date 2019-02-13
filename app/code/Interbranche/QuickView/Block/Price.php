<?php
/**
 * @package     Interbranche\QuickView
 * @version     1.0.0

 */

namespace Interbranche\QuickView\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Unirgy\RapidFlow\Exception;

/**
 * Quickview Price block
 */
class Price extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Price constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = []
    ) {
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);

        try {
            $this->getLayout()->getUpdate()->addHandle('default');
        } catch (Exception $e) {
            // silently ignore
        }
    }
}
