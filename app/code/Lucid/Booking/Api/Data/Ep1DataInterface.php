<?php


namespace Lucid\Booking\Api\Data;

interface Ep1DataInterface
{

    const ID = 'id';
    const NAME = 'name';
    const DATA_TEXT = 'data_text';
    const DATA_TEXT_BLOB = 'data_text_blob';
    const DATA_TEXT_VARBINARY = 'data_text_varbinary';
    const PRODUCT_ID = 'product_id';
    const DATE = 'date';




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
    public function getName();

    /**
     * Set Name
     * @param string $name
     * @return \Lucid\Booking\Api\Data\Ep1DataInterface
     */
    public function setName($name);


    public function getDataText();

    public function setDataText($text);

    public function getDataTextBlob();

    public function setDataTextBlob($text);

    public function getDataTextVarbinary();

    public function setDataTextVarbinary($text);

    public function getProductId();

    public function setProductId($id);

    public function getDate();

    public function setDate($id);
}
