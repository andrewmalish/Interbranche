<?php


namespace Lucid\Booking\Api\Data;

interface CalendarInterface
{

    const PRICE2 = 'Price2';
    const id = 'id';
    const ID = 'id';
    const LEVEL = 'Level';
    const DAYS = 'Days';
    const DAYSTO = 'DaysTo';
    const PRICE1 = 'Price1';
    const WEBSITES_JSON = 'websites_json';
    const DAYS_IN_ADVANCE = 'days_in_advance';

    /**
     * Get id
     * @return string|null
     */
    public function getCalendarId();

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setCalendarId($id);

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setId($id);

    /**
     * Get Price1
     * @return string|null
     */
    public function getPrice1();

    /**
     * Set Price1
     * @param string $price1
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setPrice1($price1);

    /**
     * Get Price2
     * @return string|null
     */
    public function getPrice2();

    /**
     * Set Price2
     * @param string $price2
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setPrice2($price2);

    /**
     * Get Level
     * @return string|null
     */
    public function getLevel();

    /**
     * Set Level
     * @param string $level
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setLevel($level);

    /**
     * Get Days
     * @return string|null
     */
    public function getDays();

    /**
     * Set Days
     * @param string $days
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setDays($days);

    /**
     * Get Days
     * @return string|null
     */
    public function getDaysTo();

    /**
     * Set Days
     * @param string $days
     * @return \Lucid\Booking\Api\Data\CalendarInterface
     */
    public function setDaysTo($days);

    /**
     * Get Website Json
     * @return string|null
     */
    public function getWebsitesJson();

    /**
     * Set Website Json
     * @param  string $websitesJson
     * @return string|null
     */
    public function setWebsitesJson($websitesJson);

    /**
     * Get Website Json
     * @return string|null
     */
    public function getDaysInAdvance();

    /**
     * Set Website Json
     * @param  string $data
     * @return string|null
     */
    public function setDaysInAdvance($data);

}
