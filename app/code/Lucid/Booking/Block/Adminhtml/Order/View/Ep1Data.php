<?php
namespace Lucid\Booking\Block\Adminhtml\Order\View;

use Lucid\Booking\Model\Ep1AdditionalOrderDataFactory;

class Ep1Data extends \Magento\Backend\Block\Template
{

    protected $_ep1OrderDataFactory;
    protected $_orderFactory;
    protected $scopeConfig;
    protected $ep1Data;
    private $_objectManager;
    protected $_dir;



    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        \Magento\Framework\Filesystem\DirectoryList $dir,
        Ep1AdditionalOrderDataFactory $ep1OrderDataFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        parent::__construct($context, $data);
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_orderFactory = $orderFactory;
        $this->_objectManager = $objectmanager;
        $this->scopeConfig = $scopeConfig;
        $this->_dir = $dir;
        $this->getEp1Data();
    }

    public function getEp1Data() {



        if(!$this->ep1Data) {
            try {
                $order_id = $this->getRequest()->getParam('order_id');

                $order = $this->_orderFactory->create()->load($order_id);

                $this->ep1Data = $this->_ep1OrderDataFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('order_id', $order->getIncrementId())
                    ->getLastItem();
            }
            catch(\Exception $e){
                echo 'error ep1data block';
                var_dump($e);
            }
        }

        return $this->ep1Data;

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

        return $this->getMediaUrl() . $this->getS3ImageOrderFolder() . '/ep1_mail_images/tmp/';
    }

    private function getMediaUrl(){

         return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);

    }


    private function getS3ImageOrderFolder()
    {
        return $this->scopeConfig->getValue('thai_s3/general/ep1_order_images_folder');
    }

    public function getSaveImageUrl(){


        $url = $this->_urlBuilder->getUrl('lucid_booking/order/saveImage', $paramsHere = array());

        return  $url;
    }
    public function checkImageIsset($imageName){
        $path = $this->_dir->getPath('media').'/'.$imageName;
        return file_exists((string)$path);
    }


    public function formatBlockDate($date)
    {
        return date('d.m.Y', strtotime($date) );
    }


}