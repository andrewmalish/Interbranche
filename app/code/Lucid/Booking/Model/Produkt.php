<?php


namespace Lucid\Booking\Model;

use Lucid\Booking\Api\Data\ProduktInterface;

class Produkt extends \Magento\Framework\Model\AbstractModel implements ProduktInterface
{

    protected $_eventPrefix = 'lucid_booking_produkt';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lucid\Booking\Model\ResourceModel\Produkt');
    }

    /**
     * Get id
     * @return string
     */
    public function getProduktId()
    {
        return $this->getData(self::id);
    }

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setProduktId($id)
    {
        return $this->setData(self::id, $id);
    }

    /**
     * Get Id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::id);
    }

    /**
     * Set Id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setId($id)
    {
        return $this->setData(self::id, $id);
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
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get Picture
     * @return string
     */
    public function getPicture()
    {
        return $this->getData(self::PICTURE);
    }

    /**
     * Set Picture
     * @param string $picture
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setPicture($picture)
    {
        return $this->setData(self::PICTURE, $picture);
    }

    /**
     * Get Salutation
     * @return string
     */
    public function getSalutation()
    {
        return $this->getData(self::SALUTATION);
    }

    /**
     * Set Salutation
     * @param string $salutation
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setSalutation($salutation)
    {
        return $this->setData(self::SALUTATION, $salutation);
    }

    /**
     * Get Content
     * @return string
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * Set Content
     * @param string $content
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Get width
     * @return string
     */
    public function getWidth()
    {
        return $this->getData(self::WIDTH);
    }

    /**
     * Set width
     * @param string $width
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setWidth($width)
    {
        return $this->setData(self::WIDTH, $width);
    }

    /**
     * Get height
     * @return string
     */
    public function getHeight()
    {
        return $this->getData(self::HEIGHT);
    }

    /**
     * Set height
     * @param string $height
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setHeight($height)
    {
        return $this->setData(self::HEIGHT, $height);
    }

    /**
     * Get alignment
     * @return string
     */
    public function getAlignment()
    {
        return $this->getData(self::ALIGNMENT);
    }

    /**
     * Set alignment
     * @param string $alignment
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setAlignment($alignment)
    {
        return $this->setData(self::ALIGNMENT, $alignment);
    }

    /**
     * Get sender
     * @return string
     */
    public function getSender()
    {
        return $this->getData(self::SENDER);
    }

    /**
     * Set sender
     * @param string $sender
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setSender($sender)
    {
        return $this->setData(self::SENDER, $sender);
    }
}
