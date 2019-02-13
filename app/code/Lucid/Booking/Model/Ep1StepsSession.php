<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;

class Ep1StepsSession implements Ep1StepsSessionInterface
{

    /** @var \Magento\Framework\Session\SessionManagerInterface  */
    protected $_coreSession;

    public function __construct(

        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    )
    {
//        $this->_coreSession = $coreSession;
        $this->_coreSession = $customerSession;

    }


    public function setCustomerEp1($data)
    {
        $this->_coreSession->start();
        $this->_coreSession->setCustomerEp1($data);
    }

    public function getCustomerEp1()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getCustomerEp1();
    }


    public function getStep1(){
        $this->_coreSession->start();
        return  $this->_coreSession->getStep1();
    }

    public function setStep1($data){
        $this->_coreSession->start();
        $this->_coreSession->setStep1($data);
    }

    public function getStep2(){
        $this->_coreSession->start();
        return $this->_coreSession->getStep2();
    }

    public function setStep2($data){
        $this->_coreSession->start();
        $this->_coreSession->setStep2($data);
    }

    public function getStep3(){
        $this->_coreSession->start();
        return $this->_coreSession->getStep3();
    }

    public function setStep3($data){
        $this->_coreSession->start();
        $this->_coreSession->setStep3($data);
    }

    public function getStep4(){
        $this->_coreSession->start();
        return $this->_coreSession->getStep4();
    }

    public function setStep4($data){
        $this->_coreSession->start();
        $this->_coreSession->setStep4($data);
    }

    public function getCustomerEp1Session(){
        $this->_coreSession->start();
        return $this->_coreSession->getCustomerEp1Session();
    }

    public function setCustomerEp1Session($data){
        $this->_coreSession->start();
        $currentVal = array();
        if (is_array($data)){
            $currentVal = $this->getCustomerEp1Session();
            foreach ($data as $key => $val){
                $currentVal[$key] = $val;
            }
            $data = $currentVal;
        }
        $this->_coreSession->setCustomerEp1Session($data);
    }

    public function getCustomer7pathInfo(){
        $this->_coreSession->start();
        return $this->_coreSession->getCustomer7pathInfo();
    }


    public function setCustomer7pathInfo($data){
        $this->_coreSession->start();
        $currentVal = array();
        if (is_array($data)){
            $currentVal = $this->getCustomer7pathInfo();
            foreach ($data as $key => $val){
                $currentVal[$key] = $val;
            }
            $data = $currentVal;
        }
        $this->_coreSession->setCustomer7pathInfo($data);
    }



    /***** Destroy sessions ***/
    public function destroyCustomerEp1 (){
        $this->_coreSession->start();
        $this->_coreSession->unsCustomerEp1();
    }

    public function destroyStep1 (){
        $this->_coreSession->start();
        $this->_coreSession->unsStep1();
    }

    public function destroyCustomer7pathInfo (){
        $this->_coreSession->start();
        $this->_coreSession->unsCustomer7pathInfo();
    }
    public function destroyCustomerEp1Session (){
        $this->_coreSession->start();
        $this->_coreSession->unsCustomerEp1Session();
    }


}