<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\CalendarDaysInterface;

class CalendarDays extends \Magento\Framework\Model\AbstractModel implements CalendarDaysInterface
{

    protected $_eventPrefix = 'lucid_booking_calendar_days';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lucid\Booking\Model\ResourceModel\CalendarDays');
    }

       /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Price1
     * @return string
     */
    public function getDay()
    {
        return $this->getData(self::DAY);
    }

    /**
     * Set Price1
     * @param string $day
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setDay($day)
    {
        return $this->setData(self::DAY, $day);
    }

    /**
     * Get Price2
     * @return bool
     */
    public function getIsDisabled()
    {
        return $this->getData(self::IS_DISABLED);
    }

    /**
     * Set Price2
     * @param bool $disabled
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setIsDisabled($disabled)
    {
        return $this->setData(self::IS_DISABLED, $disabled);
    }

    /**
     * Get Level
     * @return string
     */
    public function getBasisPrice()
    {
        return $this->getData(self::BASIS_PRICE);
    }

    /**
     * Set Level
     * @param string $price
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setBasisPrice($price)
    {
        return $this->setData(self::BASIS_PRICE, $price);
    }

    /**
     * Get Days
     * @return string
     */
    public function getPremiumPrice()
    {
        return $this->getData(self::PREMIUM_PRICE);
    }


    /**
     * Set Days
     * @param string $price
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setPremiumPrice($price)
    {
        return $this->setData(self::PREMIUM_PRICE, $price);
    }

    /**
     * Get Days
     * @return string
     */
    public function getWebsiteDataJson()
    {
        return $this->getData(self::WEBSITE_JSON_DATA);
    }


    /**
     * Set Days
     * @param string $websitesJson
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setWebsiteDataJson($websitesJson)
    {
        return $this->setData(self::WEBSITE_JSON_DATA, $websitesJson);
    }

}
