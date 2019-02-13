<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\CalendarInterface;

class Calendar extends \Magento\Framework\Model\AbstractModel implements CalendarInterface
{

    protected $_eventPrefix = 'lucid_booking_calendar';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lucid\Booking\Model\ResourceModel\Calendar');
    }

    /**
     * Get id
     * @return string
     */
    public function getCalendarId()
    {
        return $this->getData(self::id);
    }

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setCalendarId($id)
    {
        return $this->setData(self::id, $id);
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
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Price1
     * @return string
     */
    public function getPrice1()
    {
        return $this->getData(self::PRICE1);
    }

    /**
     * Set Price1
     * @param string $price1
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setPrice1($price1)
    {
        return $this->setData(self::PRICE1, $price1);
    }

    /**
     * Get Price2
     * @return string
     */
    public function getPrice2()
    {
        return $this->getData(self::PRICE2);
    }

    /**
     * Set Price2
     * @param string $price2
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setPrice2($price2)
    {
        return $this->setData(self::PRICE2, $price2);
    }

    /**
     * Get Level
     * @return string
     */
    public function getLevel()
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * Set Level
     * @param string $level
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setLevel($level)
    {
        return $this->setData(self::LEVEL, $level);
    }

    /**
     * Get Days
     * @return string
     */
    public function getDays()
    {
        return $this->getData(self::DAYS);
    }


    /**
     * Set Days
     * @param string $days
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setDays($days)
    {
        return $this->setData(self::DAYS, $days);
    }

    /**
     * Get Days
     * @return string
     */
    public function getDaysTo()
    {
        return $this->getData(self::DAYSTO);
    }


    /**
     * Set Days
     * @param string $days
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setDaysTo($days)
    {
        return $this->setData(self::DAYSTO, $days);
    }

    /**
     * Get getWebsiteJson
     * @return string
     */
    public function getWebsitesJson()
    {
        return $this->getData(self::WEBSITES_JSON);
    }

    /**
     * Get getWebsiteJson
     * @param string $websitesJson
     * @return string
     */
    public function setWebsitesJson($websitesJson)
    {
        return $this->setData(self::WEBSITES_JSON);
    }

    /**
     * Get getWebsiteJson
     * @return string
     */
    public function getDaysInAdvance()
    {
        return $this->getData(self::DAYS_IN_ADVANCE);
    }

    /**
     * Get getWebsiteJson
     * @param string $data
     * @return string
     */
    public function setDaysInAdvance($data)
    {
        return $this->setData(self::DAYS_IN_ADVANCE,$data);
    }

}
