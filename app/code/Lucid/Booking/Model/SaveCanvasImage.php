<?php


namespace Lucid\Booking\Model;


use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Filesystem\Io\File as IoFile;
use \Thai\S3\Model\MediaStorage\File\Storage\S3 as S3Files;

class SaveCanvasImage
{

    const DS = DIRECTORY_SEPARATOR;

    private $bookedWebsiteFactory;
    private $directoryList;
    private $ioFile;
    private $s3Files;
    private $_ep1StepsSession;


    function __construct(
        LucidBookedWebsiteFactory $bookedWebsiteFactory,
        DirectoryList $directoryList,
        IoFile $ioFile,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Lucid\Booking\Api\Data\Ep1StepsSessionInterface $ep1StepsSession,
        S3Files $s3Files
    )
    {

        $this->bookedWebsiteFactory = $bookedWebsiteFactory;
        $this->directoryList = $directoryList;
        $this->ioFile = $ioFile;
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->scopeConfig = $scopeConfig;
        $this->s3Files = $s3Files;

    }


    public function saveCanvasImage($signature, $fileName ='') {

        $signature = str_replace('data:image/png;base64,', '', $signature);
        $signature = str_replace(' ', '+', $signature);
        $image = base64_decode($signature);


        $fileName = ($fileName == '') ? time() : $fileName;

        $this->_ep1StepsSession->setCustomerEp1Session(array('png_image' => $fileName));

        $this->_ep1StepsSession->setStep1(array(
            'temporaryFileName' => $fileName
        ));

        $this->createImagesDir('tmp');

         $this->writeFile($fileName, $image);
        return $fileName;


    }

    public function removeCanvasFolder() {
        return $this->ioFile->rmdir($this->directoryList->getPath('media').static::DS.'ep1_mail_images'.static::DS.'tmp', 0775);
    }

    private function writeFile($fileName, $image){
        $filename = 'ep1_mail_images' . static::DS . 'tmp' . static::DS . $fileName . '.png';
        $this->_ep1StepsSession->setCustomerEp1Session(array('png_image_full' => $filename));
        try{
            /** saving file locally */
            $this->ioFile->write($this->directoryList->getPath('media').static::DS.$filename, $image,0775);
            $this->ioFile->write($this->directoryList->getPath('media').static::DS.$this->getS3ImageOrderFolder() . static::DS.$filename, $image,0775);

            /** trying to save file to s3 */
            $this->s3Files->saveFile($this->getS3ImageOrderFolder() . static::DS . $filename);
        }
        catch (\Exception $e){
            print_r('errors occured: ' . $e);
        }
        return true;

    }

    private function getS3ImageOrderFolder()
    {
        return $this->scopeConfig->getValue('thai_s3/general/ep1_order_images_folder');
    }

    private function createImagesDir($dir)
    {
        $this->ioFile->checkAndCreateFolder($this->directoryList->getPath('media').static::DS. $this->getS3ImageOrderFolder() .static::DS.'ep1_mail_images'.static::DS.$dir, 0775);
        return $this->ioFile->checkAndCreateFolder($this->directoryList->getPath('media').static::DS.'ep1_mail_images'.static::DS.$dir, 0775);

    }

}