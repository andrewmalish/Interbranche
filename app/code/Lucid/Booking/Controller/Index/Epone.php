<?php


namespace Lucid\Booking\Controller\Index;

use Lucid\Booking\Model\Ep1DataFactory;


class Epone extends \Magento\Framework\App\Action\Action
{

    protected $resultJsonFactory;
    protected $_ep1DataFactory;
    protected $product_id;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory

     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Ep1DataFactory $ep1DataFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory

    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_ep1DataFactory = $ep1DataFactory;
        parent::__construct($context);
    }

    public function execute()
    {


        $result = $this->resultJsonFactory->create();
        $data = array('error' => 'Data not found');

        if($this->getProductId()) {
            $ep1 = $this->getEp1Data();
            $data = array('data_text' => $this->prepareEp1Data($ep1['data_text_varbinary']));
        }


        return $result->setData($data);

    }

    public function getEp1Data(){
        return $this->_ep1DataFactory->create()
            ->getCollection()
            ->addFieldToSelect('data_text_varbinary')
            ->addFieldToFilter('product_id',$this->product_id)
            ->getLastItem()
            ->getData();
    }

    protected function getProductId(){
        return $this->product_id = $this->getRequest()->getParam('id');
    }

    protected function prepareEp1Data($source){
        $source = (string)$source;
        $source = substr($source, 6, 1006);
        return $source;
    }

}
