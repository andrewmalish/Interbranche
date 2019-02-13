<?php
/**
 * @package     Interbranche\QuickView
 * @version     1.0.0
 */

namespace Interbranche\QuickView\Block;

use Magento\Framework\Json\EncoderInterface;

/**
 * Quickview Initialize block
 */
class Initialize extends \Magento\Catalog\Block\Product\View\Gallery
{
    /**
     * Return base url.
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
