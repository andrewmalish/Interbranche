<?php
namespace Lucid\Booking\Block\Sales\Email;

use Lucid\Booking\Model\Ep1AdditionalOrderDataFactory;

class Ep1Data extends \Magento\Framework\View\Element\Template
{

    protected $_ep1OrderDataFactory;
    protected $_orderFactory;
    protected $_coreRegistry = null;
    protected $ep1Data;
    private $_objectManager;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        \Magento\Framework\Registry $registry,
        Ep1AdditionalOrderDataFactory $ep1OrderDataFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    )
    {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_orderFactory = $orderFactory;
        $this->_objectManager = $objectmanager;
        $this->getEp1Data();
    }

    public function getEp1Data() {

        if(!$this->ep1Data) {
            try {

                $order_id = $this->getRequest()->getParam('order_id') ? $this->getRequest()->getParam('order_id') : $this->getOrderId();

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

}