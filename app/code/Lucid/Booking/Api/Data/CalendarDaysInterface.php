<?php


namespace Lucid\Booking\Api\Data;

interface CalendarDaysInterface
{


    const ID = 'id';
    const DAY = 'day';
    const IS_DISABLED = 'is_disabled';
    const BASIS_PRICE = 'basis_price';
    const PREMIUM_PRICE = 'premium_price';
    const WEBSITE_JSON_DATA = 'website_json_data';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setId($id);

    /**
     * Get Price1
     * @return string|null
     */
    public function getDay();

    /**
     * Set Price1
     * @param string $day
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setDay($day);

    /**
     * Get Price2
     * @return bool|null
     */
    public function getIsDisabled();

    /**
     * Set Price2
     * @param bool $disabled
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setIsDisabled($disabled);

    /**
     * Get Level
     * @return string|null
     */
    public function getBasisPrice();

    /**
     * Set Level
     * @param string $price
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setBasisPrice($price);

    /**
     * Get Days
     * @return string|null
     */
    public function getPremiumPrice();

    /**
     * Set Days
     * @param string $price
     * @return \Lucid\Booking\Api\Data\CalendarDaysInterface
     */
    public function setPremiumPrice($price);


    /**
     * Get Website Json
     * @return string|null
     */
    public function getWebsiteDataJson();

    /**
     * Set Website Json
     * @param  string $websitesJson
     * @return string|null
     */
    public function setWebsiteDataJson($websitesJson);

}
