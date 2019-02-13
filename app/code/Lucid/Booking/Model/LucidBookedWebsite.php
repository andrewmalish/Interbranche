<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\LucidBookedWebsiteInterface;

class LucidBookedWebsite extends \Magento\Framework\Model\AbstractModel implements LucidBookedWebsiteInterface
{

    protected $_eventPrefix = 'lucid_booked_websites';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lucid\Booking\Model\ResourceModel\LucidBookedWebsite');
    }


    /**
     * Get Id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Order Id
     * @return string
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set Order id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\LucidBookedWebsiteInterface
     */
    public function setOrderId($id)
    {
        return $this->setData(self::ORDER_ID, $id);
    }


    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    public function setWebsite($data)
    {
        return $this->setData(self::WEBSITE, $data);
    }

    public function getSubpage()
    {
        return $this->getData(self::SUBPAGE);
    }

    public function setSubpage($data)
    {
        return $this->setData(self::SUBPAGE, $data);
    }

    public function getBookedDate()
    {
        return $this->getData(self::BOOKED_DATE);
    }

    public function setBookedDate($data)
    {
        return $this->setData(self::BOOKED_DATE, $data);
    }

   public function getModifiedEp1Blob()
    {
        return $this->getData(self::MODIFIED_EP1_BLOB);
    }

    public function setModifiedEp1Blob($data)
    {
        return $this->setData(self::MODIFIED_EP1_BLOB, $data);
    }

   public function getTimestamp()
    {
        return $this->getData(self::TIMESTAMP);
    }

    public function setTimestamp($data)
    {
        return $this->setData(self::TIMESTAMP, $data);
    }

   public function getSessionIdentificator()
    {
        return $this->getData(self::SESSION_IDENTIFICATOR);
    }

    public function setSessionIdentificator($data)
    {
        return $this->setData(self::SESSION_IDENTIFICATOR, $data);
    }

}
