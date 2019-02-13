<?php


namespace Lucid\Booking\Api\Data;

interface Ep1OrderDataInterface
{

    const ID = 'id';
    const ORDER_ID = 'order_id';
    const CUSTOMER_7PATH = 'customer_7path';
    const STEP_1_JSON = 'step_1_json';
    const STEP_2_JSON = 'step_2_json';
    const STEP_3_JSON = 'step_3_json';
    const STEP_4_JSON = 'step_4_json';
    const BLOB_DATA   = 'blob_data';
    const VARBINARY_DATA = 'varbinary_data';
    const EMAIL_SMS_TIME = 'email_sms_time';


    /**
     * Get Id
     * @return string|null
     */
    public function getId();

    /**
     * Set Id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setId($id);

    /**
     * Get Name
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set Name
     * @param string $id
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setOrderId($id);

    /**
     * Get Name
     * @return string|null
     */
    public function getCustomer7Path();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setCustomer7Path($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getStep1Json();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setStep1Json($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getStep2Json();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setStep2Json($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getStep3Json();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setStep3Json($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getStep4Json();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setStep4Json($data);
    /**
     * Get Name
     * @return string|null
     */

    public function getBlobData();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setBlobData($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getVarbinaryData();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setVarbinaryData($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getEmailSmsTime();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setEmailSmsTime($data);

}
