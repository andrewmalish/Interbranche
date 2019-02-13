<?php

/**
 * @Author: Ngo Quang Cuong <bestearnmoney87@gmail.com>
 * @Creation time: 2017-06-12 10:02:19
 * @Last modified time: 2017-06-12 10:16:56
 * @link: http://www.giaphugroup.com
 *
 */

namespace Lucid\Booking\Plugin\Controller\Cart;

class Add
{
    protected $_url;

    protected $request;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_url = $url;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    public function aroundExecute(\Magento\Checkout\Controller\Cart\Add $subject, \Closure $proceed)
    {
        $returnValue = $proceed();
        // We need to check, does the request send from Ajax?
        if (!$this->request->isAjax()) {
            // get the url of the checkout page


            $baseurl = $this->storeManager->getStore()->getBaseUrl();
            $paypalexpress = $baseurl.'paypal/express/start/button/1/';

            $checkoutUrl = $this->_url->getUrl($paypalexpress);
            // set the url for redirecting
            $returnValue->setUrl($checkoutUrl);
        }
        return $returnValue;
    }
}
