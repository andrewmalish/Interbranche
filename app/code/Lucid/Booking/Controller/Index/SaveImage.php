<?php


namespace Lucid\Booking\Controller\Index;


use Lucid\Booking\Model\SaveCanvasImage;

class SaveImage extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_assetRepo;
    protected $_filesystem;
    protected $_saveImage;


    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(

        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        SaveCanvasImage $saveImage

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->_saveImage = $saveImage;

    }

    public function execute()
    {
        $image = $this->getRequest()->getParam('canvas_image');
        if ($image) {
            $imgName = $this->getRequest()->getParam('order_id');

            $filename = $this->_saveImage->saveCanvasImage($image, $imgName);
            print_r($filename);
            exit;

        }
        return false;
    }

}
