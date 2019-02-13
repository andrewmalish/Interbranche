<?php


namespace Lucid\Booking\Controller\Index;

use Lucid\Booking\Model\Ep1DataFactory;
use \Magento\Framework\Event\ObserverInterface;
use Lucid\Booking\Model\Ep1OrderDataFactory;
use Lucid\Booking\Model\LucidBookedWebsiteFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;




class CheckWebsiteBooked extends \Magento\Framework\App\Action\Action
{


    private $connector;
    private $_lucidBookedWebsiteFactory;
    protected $_ep1StepsSession;
    protected $bookedSubpages;
    protected $currentDay;




    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Ep1StepsSessionInterface $ep1StepsSession,
        LucidBookedWebsiteFactory $lucidBookedWebsiteFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory

    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_lucidBookedWebsiteFactory = $lucidBookedWebsiteFactory;
        $this->_ep1StepsSession = $ep1StepsSession;

    }


    public function execute()
    {
       if($this->checkWebsiteBooked()){


           $result = $this->resultJsonFactory->create();
           $data = array('error' => 'Data not found');

           if($this->getProductId()) {
               $ep1 = $this->getEp1Data();
               $data = array('data_text' => $this->prepareEp1Data($ep1['data_text_varbinary']));
           }


           return $result->setData($data);
       }

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