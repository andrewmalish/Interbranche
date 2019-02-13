<?php
namespace Lucid\Booking\Helper;

use Lucid\Booking\Model\Ep1AdditionalOrderDataFactory;

class EmailEp1 extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_ep1OrderDataFactory;
    protected $_orderFactory;
    protected $_coreRegistry = null;
    protected $ep1Data;
    protected $_urlBuilder;
    protected $scopeConfig;




    public function __construct(

        \Magento\Framework\Registry $registry,
        Ep1AdditionalOrderDataFactory $ep1OrderDataFactory,
        \Magento\Framework\View\Element\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
          $this->_urlBuilder = $context->getUrlBuilder();
          $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
          $this->scopeConfig = $context->getScopeConfig();

    }

    public function getEp1Data($orderId) {

        if(!$this->ep1Data) {
            try {

                $this->ep1Data = $this->_ep1OrderDataFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('order_id', $orderId)
                    ->getLastItem();
            }
            catch(\Exception $e){
                echo 'error ep1data block';
                var_dump($e);
            }
        }

        return $this;

    }

    public function getCustomerData(){
        if(!empty($this->ep1Data)){
            $data = $this->ep1Data->getData('customer_7path');
            $data = json_decode($data);
            return $data;
        }
        return '';
    }


    public function getStep1(){
        if(!empty($this->ep1Data)){

            $data = $this->ep1Data->getData('step_1_json');
            $data = json_decode($data);
            return $data;

        }
        return '';
    }

    public function formatBlockDate($date)
    {
        return date('d.m.Y', strtotime($date) );
    }

    public function formatPrice($value)
    {
        $price = sprintf('%.2F', $value);
        $price = str_replace('.',',',$price);
        return $price;
    }

    public function getStep2(){
        if(!empty($this->ep1Data)){

            $data = $this->ep1Data->getData('step_2_json');
            $data = json_decode($data);
            return $data;

        }
        return '';
    }

    public function getStep3(){
        if(!empty($this->ep1Data)){

            $data = $this->ep1Data->getData('step_3_json');
            $data = json_decode($data);
            return $data;

        }
        return '';
    }

    public function getStep4(){
        if(!empty($this->ep1Data)){

            $data = $this->ep1Data->getData('step_4_json');
            $data = json_decode($data);
            return $data;
        }
        return '';
    }

    public function getEp1Modified(){
        if(!empty($this->ep1Data)){

            $data = $this->ep1Data->getBlobData();
            return $data;

        }
        return false;
    }

    public function getEp1Url(){

        return $this->getMediaUrl() .  $this->getS3ImageOrderFolder() .'/ep1_mail_images/tmp/';
    }

    private function getS3ImageOrderFolder()
    {
        return $this->scopeConfig->getValue('thai_s3/general/ep1_order_images_folder');
    }

    private function getMediaUrl(){

        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

    }

}