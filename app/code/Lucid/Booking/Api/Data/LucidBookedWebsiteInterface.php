<?php


namespace Lucid\Booking\Api\Data;

interface LucidBookedWebsiteInterface
{

    const ID = 'id';
    const ORDER_ID = 'order_id';
    const WEBSITE = 'website';
    const SUBPAGE = 'subpage';
    const BOOKED_DATE = 'booked_date';
    const MODIFIED_EP1_BLOB = 'modified_ep1_blob';
    const TIMESTAMP = 'timestamp';
    const SESSION_IDENTIFICATOR = 'session_identificator';


    /**
     * Get Id
     * @return string|null
     */
    public function getId();

    /**
     * Set Id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
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
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setOrderId($id);

    /**
     * Get Name
     * @return string|null
     */
    public function getWebsite();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setWebsite($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getSubpage();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setSubpage($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getBookedDate();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setBookedDate($data);

    /**
     * Get Name
     * @return string|null
     */
    public function getModifiedEp1Blob();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setModifiedEp1Blob($data);
    /**
     * Get Name
     * @return string|null
     */

    public function getTimestamp();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */

    public function setTimestamp($data);
    /**
     * Get Name
     * @return string|null
     */

    public function getSessionIdentificator();

    /**
     * Set Name
     * @param string $data
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */

    public function setSessionIdentificator($data);


}
