<?php


namespace Lucid\Booking\Api\Data;

interface Ep1StepsSessionInterface
{

    public function getCustomerEp1();

    public function setCustomerEp1($data);

    public function getStep1();

    public function setStep1($data);

    public function getStep2();

    public function setStep2($data);

    public function getStep3();

    public function setStep3($data);

    public function getStep4();

    public function setStep4($data);

    public function getCustomerEp1Session();

    public function setCustomerEp1Session($data);

    public function getCustomer7pathInfo();

    public function setCustomer7pathInfo($data);

    /**Destroy sessions **/
    public function destroyCustomerEp1();

    public function destroyStep1();

    public function destroyCustomer7pathInfo();

    public function destroyCustomerEp1Session();

}