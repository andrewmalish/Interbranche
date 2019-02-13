<?php

namespace Lucid\Booking\Model\Observer;

use Magento\Framework\Event\ObserverInterface;
use Lucid\Booking\Model\Ep1OrderDataFactory;
use Lucid\Booking\Model\LucidBookedWebsiteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;




class Observer implements ObserverInterface
{


    private $connector;
    private $_ep1OrderDataFactory;
    private $_lucidBookedWebsiteFactory;
    protected $scopeConfig;
    protected $_ep1StepsSession;
    protected $_customerSession;




    /**
     * @var  \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $_transportBuilder;

    private $_storeManager;


    public function __construct(
        Ep1OrderDataFactory $ep1OrderDataFactory,
        Ep1StepsSessionInterface $ep1StepsSession,
        LucidBookedWebsiteFactory $lucidBookedWebsiteFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $_customerSession,
        ScopeConfigInterface $scopeConfig

    )
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_lucidBookedWebsiteFactory = $lucidBookedWebsiteFactory;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->_customerSession = $_customerSession;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager=$storeManager;
            $this->scopeConfig = $scopeConfig;



    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if ($order->getIncrementId()) {

          $this->saveStepsData($order);

        }

    }


    protected function saveStepsData($order)
    {


        $CustomerEp1Session = $this->_ep1StepsSession->getCustomerEp1Session();
        $Customer7PathSession = $this->_ep1StepsSession->getCustomer7pathInfo();


        /*****Prepare sms text ***/
        $smsText = $CustomerEp1Session['smscontent'];
        if(isset($CustomerEp1Session['smscontent'])) {
            $name = isset($Customer7PathSession['customer7pathInfo']->first_name) ? $Customer7PathSession['customer7pathInfo']->first_name : '';
            $needle = array('%NAME%', '%SENDER%', '%DATE%', '%SITE%');
            $replace = array($name ,$CustomerEp1Session['website'], $CustomerEp1Session['datum'], $CustomerEp1Session['subpage']);
            $smsText = str_replace($needle, $replace, $CustomerEp1Session['smscontent'] );
        }
        /*****Prepare sms text ***/


        /**** collect Steps ***/
        $step1Json = array(
            'productId' => isset($CustomerEp1Session['product_id']) ? $CustomerEp1Session['product_id']  : '',
            'text' => isset($CustomerEp1Session['text']) ? $CustomerEp1Session['text']  : '',
            'text2' => isset($CustomerEp1Session['text2']) ? $CustomerEp1Session['text2']  : '',
            'text3' => isset($CustomerEp1Session['text3']) ? $CustomerEp1Session['text3']  : ''

        );

        $step2Json = array(
            'price' => isset($CustomerEp1Session['price']) ? $CustomerEp1Session['price'] : '',
            'date' => isset($CustomerEp1Session['picked_date']) ? $CustomerEp1Session['picked_date'] : '',
            'website'=> isset($CustomerEp1Session['website']) ? $CustomerEp1Session['website'] : '',
            'websiteTitle'=> isset($CustomerEp1Session['websiteTitle']) ? $CustomerEp1Session['websiteTitle'] : '',
            'subpage' => isset($CustomerEp1Session['subpage']) ? $CustomerEp1Session['subpage'] : ''
        );

        $step3Json = array(
            'datum' => isset($CustomerEp1Session['datum']) ? $CustomerEp1Session['datum'] : '',
            'uhrzeit' => isset($CustomerEp1Session['uhrzeit']) ? $CustomerEp1Session['uhrzeit'] : '',
            'ruffnummer' => isset($CustomerEp1Session['ruffnummer']) ? $CustomerEp1Session['ruffnummer'] : '',
            'email_to' => isset($CustomerEp1Session['email_to']) ? $CustomerEp1Session['email_to'] : '',
            'sms_phone' => isset($CustomerEp1Session['smsphone']) ? $CustomerEp1Session['smsphone'] : '',
            'sms_text' => isset($smsText) ? $smsText : '',

        );
        $tmpImage =  isset($CustomerEp1Session['png_image']) ? $CustomerEp1Session['png_image'] : '';

        $customerInfo = isset($Customer7PathSession['customer7pathInfo']) ? $Customer7PathSession['customer7pathInfo'] : '';

        $ep1dta =  isset($CustomerEp1Session['ep1modified']) ? $CustomerEp1Session['ep1modified'] : '';

        date_default_timezone_set("UTC");

        $emailSmsTime = strtotime($step3Json['datum'] . ' ' . $step3Json['uhrzeit']) ;

        $ep1DataModel = $this->_ep1OrderDataFactory->create();
        /**** encode Data ***/

        $customer7path = json_encode($customerInfo);
        $step1Json = json_encode($step1Json);
        $step2Json = json_encode($step2Json);
        $step3Json = json_encode($step3Json);
        $step4Json = json_encode(array('ep1_modified' => $ep1dta, 'tmpImage'=> $tmpImage));
        $ep1_before = '......';
        $ep1dta = (string)$ep1_before.$ep1dta;

//        $ep1dta = \Sodium\bin2hex($ep1dta);
//        $ep1dta = bin2hex($ep1dta);



        $ep1DataModel
            ->setOrderId($order->getIncrementId())
            ->setCustomer7Path($customer7path)
            ->setStep1Json($step1Json)
            ->setStep2Json($step2Json)
            ->setStep3Json($step3Json)
            ->setStep4Json($step4Json)
            ->setEmailSmsTime($emailSmsTime)
            ->setBlobData($ep1dta)
            ->setVarbinaryData($ep1dta)
            ->save();

        $this->saveBookedWebsite($step2Json,$order->getIncrementId(),$ep1dta);


        if($customer = $this->_customerSession->getCustomer()) {
            if($billingAddress = $customer->getDefaultBillingAddress()) {

                $order->getBillingAddress()->setFirstname($billingAddress->getFirstname());
                $order->getBillingAddress()->setLastname($billingAddress->getLastname());

//                $order->getBillingAddress()->addData($billingAddress->getData());
                $order->save();

            }
        }

//        $this->sendEmailNew(1,1, $order);
        $this->clearCustomerSession();

    }

    protected function saveBookedWebsite($calendarData,$orderId,$ep1dta) {
        $calendarData = json_decode($calendarData);
        if(!empty($calendarData->date)) {
            try {
                $subpageDataModel = $this->_lucidBookedWebsiteFactory->create();
                $subpageDataModel
                    ->setOrderId($orderId)
                    ->setWebsite($calendarData->website)
                    ->setSubpage($calendarData->subpage)
                    ->setBookedDate($calendarData->date)
                    ->setModifiedEp1Blob($ep1dta)
                    ->save();

            }
            catch(\Exception $e){
                echo 'wrong data found ' .$e ;
            }

        }
        else {
            return false;
        }

        return true;
    }

    private function clearCustomerSession(){

        $this->_ep1StepsSession->destroyStep1();
//        $this->_ep1StepsSession->destroyCustomer7pathInfo();
        $this->_ep1StepsSession->destroyCustomerEp1Session();
        $this->_ep1StepsSession->destroyCustomerEp1();

        return;
    }


    public function sendEmailNew($templateId =1, $storeId =1,$order)
    {
        $emailTempVariables = array();

        $ep1Data = $this->_ep1StepsSession->getCustomerEp1Session();

        $emailTempVariables['my_variable'] = $this->_storeManager->getStore()->getUrl('pub/media') . $ep1Data['png_image_full'];


        $orderObject = new \Magento\Framework\DataObject();
        $orderObject->setData($order->getData());

        $emailTempVariables['order'] = $orderObject;


        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName  = $this->scopeConfig->getValue('trans_email/ident_general/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $email = $order->getCustomerEmail();
        $email2 = $this->scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);



        $sender = ['name' => $senderName,'email' => $senderEmail];

       $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)->setTemplateOptions(['area' =>\Magento\Framework\App\Area::AREA_FRONTEND,'store'=> \Magento\Store\Model\Store::DEFAULT_STORE_ID])
        ->setTemplateVars($emailTempVariables)
        ->setFrom($sender)
        ->addTo($email)
        ->addCc($email2)
        ->setReplyTo($senderEmail)
        ->getTransport();
                $transport->sendMessage();

    }

}