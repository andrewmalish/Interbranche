<?php
/**
 * @package     Interbranche\QuickView
 * @version     1.0.0
 */

namespace Interbranche\Quickview\Controller\Catalog\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Catalog\Controller\Product
{
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var
     */
    protected $template;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Interbranche\QuickView\Helper\Data
     */
    private $helper;

    /**
     * View constructor.
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Interbranche\QuickView\Helper\Data $helper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory

    ) {
        $this->viewHelper = $viewHelper;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');
        $params = new \Magento\Framework\DataObject();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);
        $product = $this->_initProduct();
        if ($product->getId()) {
            try {
                $result = $this->helper->getProductDataAsJson($product);
                $this->getResponse()->representJson($result);
                /*$page->addDefaultHandle();
                $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
                return $page;*/
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->getResponse()->representJson($this->jsonHelper->jsonEncode(['error_message' => __("Product doesn't exist")]));
                //return $this->noProductRedirect();
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $resultForward = $this->resultForwardFactory->create();
                $resultForward->forward('noroute');
                return $resultForward;
            }
        } else {
            $this->getResponse()->representJson($this->jsonHelper->jsonEncode(['error_message' => __("Product doesn't exist")]));
        }

    }
}
