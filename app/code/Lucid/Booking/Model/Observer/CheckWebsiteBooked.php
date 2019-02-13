<?php

namespace Lucid\Booking\Model\Observer;

use \Magento\Framework\Event\ObserverInterface;
use Lucid\Booking\Model\Ep1OrderDataFactory;
use Lucid\Booking\Model\LucidBookedWebsiteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;




class CheckWebsiteBooked implements ObserverInterface
{


    private $connector;
    private $_ep1OrderDataFactory;
    private $_lucidBookedWebsiteFactory;
    protected $scopeConfig;
    protected $_ep1StepsSession;
    protected $_customerSession;
    protected $currentDay;
    protected $bookedSubpages;
    private $_storeManager;
    protected $_url;
    protected $responseFactory;
    protected $messageManager;


    public function __construct(
        Ep1OrderDataFactory $ep1OrderDataFactory,
        Ep1StepsSessionInterface $ep1StepsSession,
        \Magento\Framework\UrlInterface $url,
        LucidBookedWebsiteFactory $lucidBookedWebsiteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $_customerSession,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig

    )
    {

        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_lucidBookedWebsiteFactory = $lucidBookedWebsiteFactory;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->_customerSession = $_customerSession;
        $this->_storeManager=$storeManager;
        $this->_url = $url;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->responseFactory = $responseFactory;


    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       if($this->checkWebsiteBooked()){

           $baseurl = $this->_storeManager->getStore()->getBaseUrl();
           $newcheckout = $baseurl.'booking/index/newcheckout/';

           $redirectUrl = $this->_url->getUrl($newcheckout);
           $this->messageManager->addError(__("Failed booking website"));


    //       $observer->getResponse()->setRedirect($redirectUrl);
//
           return $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
           exit(0);
           die();
       }
        return $this;
    }





    protected function checkWebsiteBooked(){
        $CustomerEp1Session = $this->_ep1StepsSession->getCustomerEp1Session();

        if($CustomerEp1Session['picked_date'] && $CustomerEp1Session['picked_date']!=''){

            $this->getCurrentDay($CustomerEp1Session['picked_date']);

        }

        if($CustomerEp1Session['website']!='' && $CustomerEp1Session['subpage']!='') {
            $this->getSubpagesArray();
            $title = $CustomerEp1Session['website'] . '_' . $CustomerEp1Session['subpage'];

            return $this->checkSubpageBooked($title);
        }

        return false;
    }



    protected function checkSubpageBooked($title){

        return in_array(trim($title), $this->bookedSubpages);

    }


    protected function getSubpagesArray(){

        if (!empty($this->bookedSubpages)){
            return $this->bookedSubpages;
        }


             $data = $this->_lucidBookedWebsiteFactory->create()
            ->getCollection()
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('website')
            ->addFieldToSelect('subpage')
            ->addFieldToFilter('booked_date', $this->currentDay)
            ->getData();

        if (!empty($data)){
            foreach ($data as $value){
                if($value['order_id'] !== null) {
                    $this->bookedSubpages[] = $value['website'] . '_' . $value['subpage'];
                }
            }
        }

        return $this->bookedSubpages;
    }

    private function getCurrentDay($day) {

        if ($day){
            $this->currentDay = date('Y-m-d', strtotime($day));
        }
        else {
            $this->currentDay = date('Y-m-d');
        }

        return $this->currentDay;

    }

}