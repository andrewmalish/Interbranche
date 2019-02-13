<?php



namespace Lucid\Booking\Controller\Order;

use Lucid\Booking\Model\Ep1OrderDataFactory;
use \Magento\Sales\Model\Order;
use Lucid\Booking\Model\SaveCanvasImage;



class Save extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_filesystem;
    private $_ep1OrderDataFactory;
    private $_salesOrder;
    protected $_saveImage;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Ep1OrderDataFactory $ep1OrderDataFactory,
        SaveCanvasImage $saveImage,
        Order $salesOrder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_ep1OrderDataFactory = $ep1OrderDataFactory;
        $this->_salesOrder = $salesOrder;
        $this->_saveImage = $saveImage;
        parent::__construct($context);

    }


    public function execute()
    {


        $parced = $this->getRequest()->getParams();

        $data = array();
        parse_str($parced['formData'], $data);

        if (!empty($parced['ep1modified'])){

            $ep1dta = $parced['ep1modified'];

            /**** collect Steps ***/
            $step1Json = array(
                'productId' => isset($data['product_id']) ? $data['product_id']  : '',
                'text' => isset($data['text']) ? $data['text']  : '',
                'text2' => isset($data['text2']) ? $data['text2']  : '',
                'text3' => isset($data['text3']) ? $data['text3']  : ''

            );





            $ep1DataModel = $this->_ep1OrderDataFactory->create();

            $item = $ep1DataModel->getCollection()->addFieldToSelect('id')->addFieldToFilter('order_id',$data['orderid'])->getLastItem();

            if($id = $item->getId()){
                $ep1DataModel->load($id);

            }

            $step4 = json_decode($ep1DataModel->getStep4Json());

            $tmpImage = $this->saveImage($parced['canvas_image'],$step4->tmpImage);

            $step1Json = json_encode($step1Json);
            $step4Json = json_encode(array('ep1_modified' => $ep1dta, 'tmpImage'=> $tmpImage));
            $ep1_before = '......';
            $ep1dta = (string)$ep1_before.$ep1dta;

            $ep1DataModel
                ->setStep1Json($step1Json)
                ->setStep4Json($step4Json)

                ->setBlobData($ep1dta)
                ->setVarbinaryData($ep1dta)
                ->save();

                $orderId = $this->changeOrderStatus($data['orderid']);

                print_r(json_encode(array('epchanged'=> true,'orderId' => $orderId)));
                exit;
        }

        return $this->resultPageFactory->create();

    }

    private function saveImage($canvas,$filename){


        if($canvas) {

         return $this->_saveImage->saveCanvasImage($canvas,$filename);
        }

    }

    private function changeOrderStatus($incrementOrderId) {


        try {
            $order = $this->_salesOrder->loadByIncrementId($incrementOrderId);
            $order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING)->save();
            return $order->getId();
        }
        catch (\Exception $e) {
            die('error' .$e);
        }

    }


    private function checkBadWords($words)
    {

        $path = $this->_filesystem->getDirectoryRead(
            'media'
        )->getAbsolutePath(
            'badwords/'
        );

        $path = $path . 'badwords.json';

        $handle = fopen($path, "r");
        $contents = fread($handle, filesize($path));
        fclose($handle);
        $badwordsList = json_decode($contents);

        foreach ($badwordsList as $badwords) {

            foreach ($badwords as $badword) {

                $allWords = preg_replace("/\r\n/", " ", $words['text']);
                $allWords = explode(' ', $allWords);

                foreach ($allWords as $word) {
                    $bad = strtolower($badword);
                    $needle = strtolower(trim($word));
                    if ($bad === $needle) {
                        return $badword;
                    }
                }

            }

        }

        return false;
    }



}
