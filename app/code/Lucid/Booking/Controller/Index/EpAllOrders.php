<?php


namespace Lucid\Booking\Controller\Index;


use Lucid\Booking\Model\Ep1AdditionalOrderDataFactory;



class EpAllOrders extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;
    protected $ep1Data;
    protected $_ep1OrderDataFactory;
    protected $product_id;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory

     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Ep1AdditionalOrderDataFactory $ep1OrderDataFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory

    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        parent::__construct($context);
    }

    public function execute()
    {


        $result = $this->resultJsonFactory->create();

        $data = $this->sortEp1Array($this->getEp1Data());

        return $result->setData($data);

    }

    private function getEp1Data(){


        if(!$this->ep1Data) {
            try {
                $this->ep1Data = $this->_ep1OrderDataFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToSelect(array('order_id', 'blob_data'))
                    ->getItems();
            } catch (\Exception $e) {
                echo 'error ep1data block';
              //  var_dump($e);
            }

        }

        return $this->ep1Data;


    }

    private function sortEp1Array($data = array()){

        $result = array();

        foreach ($data as $item){
            $source2 = $item['blob_data'];
            $source = (string)$source2;
            $source = substr($source, 6, 1006);


            $result['order_'.$item['order_id']] = $source;
        }

        return $result;
    }

}
