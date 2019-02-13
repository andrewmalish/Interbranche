<?php

namespace Lucid\Booking\Block\Order;

use Lucid\Booking\Model\Ep1AdditionalOrderDataFactory;
use Magento\Framework\View\Element\Template;



class ImageConvert extends Template
{

    protected $_ep1OrderDataFactory;
    protected $ep1Data;


    public function __construct(
        Template\Context $context,
//        Registry $registry,
        Ep1AdditionalOrderDataFactory $ep1OrderDataFactory,
        array $data = []
    )
    {

//        $this->registry = $registry;
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        parent::__construct($context, $data);
        $this->getOrdersEp1Data();

    }


    public function prepareEp1Data($source)
    {
        $source = (string)$source;
        $source = substr($source, 6, 1006);
        $source = htmlentities($source);

        return json_encode(array('ep1' => $source));
    }





    private function getOrderEp1Collection(){
        $page = 1;
        $size = 30;
        if($this->getRequest()->getParam('page')){
            $page = $this->getRequest()->getParam('page');
        }
        if($this->getRequest()->getParam('size')){
            $size = $this->getRequest()->getParam('size');
        }


        if(!$this->ep1Data) {
            try {
                $this->ep1Data = $this->_ep1OrderDataFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToSelect('order_id')
                    ->setPageSize($size)
                    ->setCurPage($page);
            } catch (\Exception $e) {
                echo 'error ep1data block';
                var_dump($e);
            }

        }

        return $this->ep1Data;
    }


    public function getOrdersEp1Data() {

        if(!$this->ep1Data) {

             $this->getOrderEp1Collection();
        }

        return $this->ep1Data->getItems();

    }

    public function getLastPage (){

        if(!$this->ep1Data) {

            $this->getOrderEp1Collection();

        }

        return $this->ep1Data->getLastPageNumber();


    }

    public function getEp1Modified(){
        if(!empty($this->ep1Data)){

            $data = $this->ep1Data->getBlobData();
            return $data;

        }
        return false;
    }


}
