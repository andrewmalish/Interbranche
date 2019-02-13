<?php


namespace Lucid\Booking\Model;


use Lucid\Booking\Model\LucidBookedWebsiteFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem\Io\File as IoFile;
use \Lucid\Booking\Model\S3Factory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;


class Ep1ToW3
{

    const DS = DIRECTORY_SEPARATOR;
    const FLAG_FILE_NAME = 'flag.txt';

    private $bookedWebsiteFactory;
    private $directoryList;
    private $ioFile;
    private $s3Factory;
    private $_directoryList;

    function __construct(
        LucidBookedWebsiteFactory $bookedWebsiteFactory,
        DirectoryList $directoryList,
        IoFile $ioFile,
        S3Factory $s3Factory,
        CollectionFactory $orderCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource

    )
    {

        $this->bookedWebsiteFactory = $bookedWebsiteFactory;
        $this->directoryList = $directoryList;
        $this->ioFile = $ioFile;
        $this->s3Factory = $s3Factory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_resource = $resource;
        $this->_directoryList = array();

    }


    public function getBookedWebsite($date = '')
    {


        if ($date == '') {
            $date = date('Y-m-d');
        }

        $second_table_name = $this->_resource->getTableName('sales_order');

        $collection = $this->bookedWebsiteFactory->create()
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('booked_date', $date);

         $collection->getSelect()
            ->join(array('second' => $second_table_name), 'main_table.order_id = second.increment_id')
            ->where("second.status = 'complete'");

        $booked = $collection->getData() ;

        /*** Remove old ep1 foders from s3 ***/
        $this->removeEp1Folder();


        /** Upload new ep1 to s3 */
        foreach ($booked as $reserv) {

            if ($this->createImagesDir($reserv['website'])) {
                $this->writeFile($reserv);
            }
        }

        /** Create root flag file */
        if(count($booked) > 0) {
            $this->createFlagFile();
        }
        return $booked;
    }

    private function writeFile($data)
    {
        $filename = 'production/ep1' . static::DS . $data['website'] . static::DS . $data['subpage'] . '_01.ep1';
        $file = $this->ioFile->write($this->directoryList->getPath('media') . static::DS . $filename, $data['modified_ep1_blob'], 0775);
        if ($file) {
            $this->uploadToS3($filename);
        }
    }

    private function createFlagFile(){

        $file = false;
        try {
            $text = (string)time();

            $filename = 'production/ep1' . static::DS . static::FLAG_FILE_NAME;
            $this->ioFile->checkAndCreateFolder($this->directoryList->getPath('media') . static::DS . 'production/ep1' , 0775);
            $file = $this->ioFile->write($this->directoryList->getPath('media') . static::DS . $filename, $text, 0775);
            if ($file) {
                $this->uploadToS3($filename);
            }

        }
        catch (\Exception $e){
            var_dump($e);
        }

    }


    private function createImagesDir($dir)
    {
        return $this->ioFile->checkAndCreateFolder($this->directoryList->getPath('media') . static::DS . 'production/ep1' . static::DS . $dir, 0775);
    }

    private function uploadToS3($file)
    {
        $this->s3Factory->create()->saveFile($file);
    }


    private function removeEp1Folder(){


            $path = 'production/ep1' ;
            try {
                $this->ioFile->rmdir($this->directoryList->getPath('media') . static::DS . $path, true);
                $this->s3Factory->create()->deleteDirectory($path);

            } catch (\Exception $e) {

            }

    }

    private function clearEp1DataFolders(){


        foreach($this->getRemoveDirectoryList() as $folder) {
            $path = 'production/ep1' . static::DS . $folder ;
            try {
                $this->s3Factory->create()->deleteDirectory($path);

            } catch (\Exception $e) {

            }
        }

    }

    private function getRemoveDirectoryList() {

        if (empty($this->_directoryList)) {

            $this->_directoryList = array('BB', 'k1de', 'k1doku', 'p7de', 'P7de', 'p7maxx', 'ProSieben', 's1de', 's1gold', 'SAT.1', 'sixx');
        }

        return $this->_directoryList;
    }

}