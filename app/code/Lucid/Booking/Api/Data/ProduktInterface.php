<?php


namespace Lucid\Booking\Api\Data;

interface ProduktInterface
{

    const id = 'id';
    const HEIGHT = 'height';
    const NAME = 'Name';
    const WIDTH = 'width';
    const ALIGNMENT = 'alignment';
    const SALUTATION = 'Salutation';
    const PICTURE = 'Picture';
    const CONTENT = 'Content';
    const SENDER = 'sender';
    const ID = 'Id';


    /**
     * Get id
     * @return string|null
     */
    public function getProduktId();

    /**
     * Set id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setProduktId($id);

    /**
     * Get Id
     * @return string|null
     */
    public function getId();

    /**
     * Set Id
     * @param string $id
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setId($id);

    /**
     * Get Name
     * @return string|null
     */
    public function getName();

    /**
     * Set Name
     * @param string $name
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setName($name);

    /**
     * Get Picture
     * @return string|null
     */
    public function getPicture();

    /**
     * Set Picture
     * @param string $picture
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setPicture($picture);

    /**
     * Get Salutation
     * @return string|null
     */
    public function getSalutation();

    /**
     * Set Salutation
     * @param string $salutation
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setSalutation($salutation);

    /**
     * Get Content
     * @return string|null
     */
    public function getContent();

    /**
     * Set Content
     * @param string $content
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setContent($content);

    /**
     * Get width
     * @return string|null
     */
    public function getWidth();

    /**
     * Set width
     * @param string $width
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setWidth($width);

    /**
     * Get height
     * @return string|null
     */
    public function getHeight();

    /**
     * Set height
     * @param string $height
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setHeight($height);

    /**
     * Get alignment
     * @return string|null
     */
    public function getAlignment();

    /**
     * Set alignment
     * @param string $alignment
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setAlignment($alignment);

    /**
     * Get sender
     * @return string|null
     */
    public function getSender();

    /**
     * Set sender
     * @param string $sender
     * @return \Lucid\Booking\Api\Data\ProduktInterface
     */
    public function setSender($sender);
}
