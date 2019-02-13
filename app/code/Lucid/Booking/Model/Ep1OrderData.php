<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\Ep1OrderDataInterface;

class Ep1OrderData extends \Magento\Framework\Model\AbstractModel implements Ep1OrderDataInterface
{

    protected $_eventPrefix = 'lucid_booking_ep1orderdata';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lucid\Booking\Model\ResourceModel\Ep1OrderData');
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getOrderId(){
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId($id){
        return $this->setData(self::ORDER_ID, $id);
    }

    public function getCustomer7Path(){
        return $this->getData(self::CUSTOMER_7PATH);
    }

    public function setCustomer7Path($data){
        return $this->setData(self::CUSTOMER_7PATH, $data);
    }

    public function getStep1Json(){
        return $this->getData(self::STEP_1_JSON);
    }

    public function setStep1Json($data){
        return $this->setData(self::STEP_1_JSON, $data);
    }

    public function getStep2Json(){
        return $this->getData(self::STEP_2_JSON);
    }

    public function setStep2Json($data){
        return $this->setData(self::STEP_2_JSON, $data);
    }

    public function getStep3Json(){
        return $this->getData(self::STEP_3_JSON);
    }

    public function setStep3Json($data){
        return $this->setData(self::STEP_3_JSON, $data);
    }

    public function getStep4Json(){
        return $this->getData(self::STEP_4_JSON);
    }

    public function setStep4Json($data){
        return $this->setData(self::STEP_4_JSON, $data);
    }

    public function getBlobData(){
        return $this->getData(self::BLOB_DATA);
    }

    public function setBlobData($data){
        return $this->setData(self::BLOB_DATA, $data);
    }

    public function getVarbinaryData(){
        return $this->getData(self::VARBINARY_DATA);
    }

    public function setVarbinaryData($data){
        return $this->setData(self::VARBINARY_DATA, $data);
    }

    public function getEmailSmsTime(){
        return $this->getData(self::EMAIL_SMS_TIME);
    }

    public function setEmailSmsTime($data){
        return $this->setData(self::EMAIL_SMS_TIME, $data);
    }

}
