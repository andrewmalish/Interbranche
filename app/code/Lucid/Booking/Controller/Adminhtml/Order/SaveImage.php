<?php


namespace Lucid\Booking\Controller\Adminhtml\Order;

use Lucid\Booking\Model\Ep1OrderDataFactory;
use Lucid\Booking\Model\SaveCanvasImage;
use Magento\Framework\Exception\LocalizedException;

class SaveImage extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    private $_ep1OrderDataFactory;
    protected $_saveImage;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Ep1OrderDataFactory $ep1OrderDataFactory,
        SaveCanvasImage $saveImage,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_saveImage = $saveImage;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();




        if (!empty($data['canvas_image'])){

            $tmpImage = $this->saveImage();

            if($tmpImage) {
                $ep1DataModel = $this->_ep1OrderDataFactory->create();

                $item = $ep1DataModel->getCollection()->addFieldToSelect('id')->addFieldToFilter('order_id', $data['order_id'])->getLastItem();

                if ($id = $item->getId()) {
                    $ep1DataModel->load($id);

                }

                $step4 = json_decode($ep1DataModel->getStep4Json());

                $step4Json = json_encode(array('ep1_modified' => $step4->ep1_modified, 'tmpImage' => $tmpImage));

                $ep1DataModel
                    ->setStep4Json($step4Json)
                    ->save();

                print_r(json_encode(array('epchanged' => true)));
                exit;
            }
        }

        print_r(json_encode(array('error'=> true)));
        die();

    }

    private function saveImage(){

        $image = $this->getRequest()->getParam('canvas_image');
        if($image) {
            return $this->_saveImage->saveCanvasImage($image);
        }

        return false;
    }
}
