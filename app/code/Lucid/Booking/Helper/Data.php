<?php

namespace Lucid\Booking\Helper;

use Lucid\Booking\Model\Ep1DataFactory;
use Magento\CheckoutAgreements\Model\AgreementFactory;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $calendarRepository;
    protected $_ep1DataFactory;
    protected $checkoutSession;
    protected $agreementFactory;
    private $_ep1StepsSession;
    protected $_urlBuilder;
    protected $scopeConfig;
    protected $_registry;



    public function __construct(

        Ep1DataFactory $ep1DataFactory,
        AgreementFactory $agreementFactory,
        Ep1StepsSessionInterface $ep1StepsSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Context $context,
        \Lucid\Booking\Model\CalendarRepository $calendarRepository
    )
    {
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->agreementFactory = $agreementFactory;
        $this->_ep1DataFactory = $ep1DataFactory;
        $this->checkoutSession = $checkoutSession;
        $this->calendarRepository = $calendarRepository;
        $this->_registry = $registry;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->scopeConfig = $context->getScopeConfig();

    }

    public function getLastCalendar()
    {
        return $this->calendarRepository->getLastCalendar();
    }


    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');//get current category
    }

    public function getCustomerSessionInfo()
    {
        $customer7Path = is_array($this->_ep1StepsSession->getCustomer7pathInfo()) ? $this->_ep1StepsSession->getCustomer7pathInfo() : false;
        return isset($customer7Path['customer7pathInfo']) ? $customer7Path['customer7pathInfo'] : false;
    }

    public function getCustomerEp1Data()
    {
        $data = $this->_ep1StepsSession->getCustomerEp1Session();
        return is_array($data) ? $data : false;
    }


    public function getEp1ProductData($productId)
    {
        return $this->_ep1DataFactory->create()->getCollection()->addFieldToFilter('product_id', $productId)->getLastItem();
    }


    public function getCurrentQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    public function formatPrice($value)
    {
        $price = sprintf('%.2F', $value);
        $price = str_replace('.', ',', $price);
        return $price;
    }

    public function formatBlockDate($date)
    {
        return date('d.m.Y', strtotime($date));
    }

    public function getTerms()
    {
        $agreements = $this->agreementFactory->create()
            ->getCollection()
            ->addFieldToSelect('*')
//            ->addFieldToFilter('is_active', true)
            ->getItems();

        return (count($agreements) > 0) ? $agreements : false;
    }

    public function getEp1Url()
    {

        return $this->getMediaUrl() . $this->getS3ImageOrderFolder() . '/ep1_mail_images/tmp/';
    }

    private function getS3ImageOrderFolder()
    {
        return $this->scopeConfig->getValue('thai_s3/general/ep1_order_images_folder');
    }

    private function getMediaUrl()
    {

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

    }

}