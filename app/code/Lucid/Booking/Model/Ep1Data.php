<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\Ep1DataInterface;

class Ep1Data extends \Magento\Framework\Model\AbstractModel implements Ep1DataInterface
{

    protected $_eventPrefix = 'lucid_booking_ep1data';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lucid\Booking\Model\ResourceModel\Ep1Data');
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
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Name
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set Name
     * @param string $name
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getDataText()
    {
        return $this->getData(self::DATA_TEXT);
    }

    public function setDataText($text)
    {
        return $this->setData(self::DATA_TEXT, $text);
    }

    public function getDataTextBlob()
    {
        return $this->getData(self::DATA_TEXT_BLOB);
    }

    public function setDataTextBlob($text)
    {
        return $this->setData(self::DATA_TEXT_BLOB, $text);
    }

    public function getDataTextVarbinary()
    {
        return $this->getData(self::DATA_TEXT_VARBINARY);
    }

    public function setDataTextVarbinary($text)
    {
        return $this->setData(self::DATA_TEXT_VARBINARY, $text);
    }

    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId($id)
    {
        return $this->setData(self::PRODUCT_ID, $id);
    }

    public function getDate()
    {
        return $this->getData(self::DATE);
    }

    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }
}
