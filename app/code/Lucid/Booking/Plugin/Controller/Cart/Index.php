<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lucid\Booking\Plugin\Controller\Cart;

class Index
{
    protected $_url;

    protected $request;

    protected $responseFactory;

    protected $messageManager;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager

    )
    {
        $this->_url = $url;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->storeManager = $storeManager;
    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\Index $subject, \Closure $proceed)
    {

        $returnValue = $proceed();

        $baseurl = $this->storeManager->getStore()->getBaseUrl();
        $newcheckout = $baseurl.'booking/index/newcheckout/';

            $redirectUrl = $this->_url->getUrl($newcheckout);
            $resultRedirect = $this->responseFactory->create();

            $this->messageManager->addError(__("Error paypal payment. Please try one more time, or contact administrator"));
            $resultRedirect->setRedirect($redirectUrl)->sendResponse('200');
//            exit;
            return $resultRedirect;
    }
}
