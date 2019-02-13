<?php


namespace Lucid\Booking\Controller\Index;

use Lucid\Booking\Model\Ep1DataFactory;


class EpAll extends \Magento\Framework\App\Action\Action
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

        $data = $this->sortEp1Array($this->getEp1Data());

        return $result->setData($data);

    }

    private function getEp1Data(){
        return $this->_ep1DataFactory->create()
            ->getCollection()
            ->addFieldToSelect(array('data_text_varbinary','product_id'))
            ->getData();
    }

    private function sortEp1Array($data = array()){

        $result = array();

        foreach ($data as $item){
            $source2 = $item['data_text_varbinary'];
            $source = (string)$source2;
            $source = substr($source, 6, 1006);


            $result['product'.$item['product_id']] = $source;
        }

        return $result;
    }

}
