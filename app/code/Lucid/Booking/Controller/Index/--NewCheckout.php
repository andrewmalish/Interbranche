<?php


namespace Lucid\Booking\Controller\Index;

use Lucid\Booking\Api\Data\Ep1StepsSessionInterface;


class NewCheckout extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $coupon;
    protected $saleRule;
    protected $_ep1StepsSession;
    protected $customerEp1Session;
    protected $freeOrder;
    protected $_protectionSalt;
    protected $_customerSession;
    protected $addressRepository;


    public function __construct(
        Ep1StepsSessionInterface $ep1StepsSession,
        \Magento\Framework\App\Action\Context $context,
        \Lucid\Booking\Model\CreateFreeOrder $freeOrder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Api\Data\AddressInterface $addressRepository,
        \Magento\Customer\Model\Session $_customerSession

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->_ep1StepsSession = $ep1StepsSession;
        $this->freeOrder = $freeOrder;
        $this->_customerSession = $_customerSession;
        $this->addressRepository = $addressRepository;
        $this->setcustomerEp1Session();
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

//        $this->customerEp1Session = $this->_ep1StepsSession->getCustomerEp1Session();


        if (is_array($this->customerEp1Session) && count($this->customerEp1Session) > 5) {
            $this->addingToCart();
            return $this->resultPageFactory->create();
        }

        return $this->_redirectToCategory();


    }


    private function addingToCart()
    {

        try {

            $coupon = $this->getRequest()->getParam('coupon');


            if ($this->getRequest()->getParam('confirmOrder')) {

                if ($this->checkFreeCondition()) {

                    try {
                        $orderId = $this->freeOrder->createFreeOrder();
                    } catch (\Exception $e) {

                        print_r(json_encode(array('error' => $e->getMessage())));
                        return false;

                    }
                    if ($orderId) {
//                        return false;
//                        exit;
                        return $this->_redirectToSuccess();
                    }
                }

            }


            if ($coupon) {
                $this->applyCoupon($coupon);
                exit;
            }
            if ($coupon === '') {
                exit;
            }

            $id = $this->getProductId();
            if ($id) {

                $qty = 1;

                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);
                $cart = $this->_objectManager->create('Magento\Checkout\Model\Cart');
                $quoteSession = $this->_objectManager->get('Magento\Checkout\Model\Session');
                $quote = $cart->getQuote();
                $quoteSession->getQuote();
                $quoteSession->clearSession();
                $cart->truncate();

                $price = $this->getCalendarPrice();

                $product
                    ->setPrice($price)
                    ->setBasePrice($price)
                    ->setCustomPrice($price);


                $quote
                    ->addProduct($product, $qty)
                    ->setPrice($price)
                    ->setBasePrice($price)
                    ->setCustomPrice($price)
                    ->setOriginalCustomPrice($price);


                if ($customer = $this->_customerSession->getCustomer()) {
                    if ($billingAddress = $customer->getDefaultBillingAddress()) {
                        $quote->getBillingAddress()->addData($billingAddress->getData());
                    }
                }

                $quote->reserveOrderId();
                $quote->save();
                $quote->collectTotals();
                $cart->save();
                $quoteSession->setQuoteId($cart->getQuote()->getId());
                //             $quoteSession->setData($quote->getData());

            }

        } catch (\Exception $e) {

            var_dump('error Found' . $e);
        }
    }


    private function checkFreeCondition()
    {

        return $this->customerEp1Session['salt'] === $this->getRequest()->getParam('confirmOrder');
    }

    private function applyCoupon($coupon)
    {

        $couponData = $this->getDiscountAmount($coupon);

        $checkoutSession = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $quote = $checkoutSession->getQuote();
        $discount = 0;
        if ($couponData) {
            $quote->setCouponCode($coupon)->collectTotals()->save();
            $discount = $this->getDiscount($quote);


            if (!empty($quote->getCouponCode())) {

                if ($couponData === '100.0000') {

                    $this->_ep1StepsSession->setCustomerEp1Session(array('couponCode' => $coupon));
                    if (is_array($items = $quote->getAllItems())) {
                        $item = array_shift($items);
                        $priceRow = $item->getPrice();
                    } else {
                        $priceRow = 0.00;
                    }

                    $salt = $this->_getSalt();

                    $couponResult = array(
                        'status' => 'ok',
                        'discount' => $this->formatPrice($priceRow),
                        'tax' => $this->formatPrice(0.00),
                        'total' => $this->formatPrice(0.00),
                        'successUrl' => '/booking/index/newcheckout/confirmOrder/' . $salt
                    );

                    print_r(json_encode($couponResult));
                    exit;
                }


                $tax = $quote->getTotals();
                $tax = $tax['tax']->getValue();
                $couponResult = array(
                    'status' => 'ok',
                    'discount' => $this->formatPrice($discount),
                    'tax' => $this->formatPrice($tax),
                    'total' => $this->formatPrice($quote->getBaseGrandTotal()),
                );

                print_r(json_encode($couponResult));
            } else {
                $tax = $quote->getTotals();
                $tax = $tax['tax']->getValue();
                $couponResult = array(
                    'status' => 'fail',
                    'discount' => $this->formatPrice($discount),
                    'tax' => $this->formatPrice($tax),
                    'total' => $this->formatPrice($quote->getBaseGrandTotal()),
                );

                print_r(json_encode($couponResult));
            }
        } else {
            $tax = $quote->getTotals();
            $tax = $tax['tax']->getValue();
            $couponResult = array(
                'status' => 'fail',
                'discount' => $this->formatPrice($discount),
                'tax' => $this->formatPrice($tax),
                'total' => $this->formatPrice($quote->getBaseGrandTotal()),
            );

            print_r(json_encode($couponResult));

        }
    }


    private function _getSalt()
    {

        if (!$this->_protectionSalt) {
            $this->_protectionSalt = $this->_randomSalt();
            $this->_ep1StepsSession->setCustomerEp1Session(array('salt' => $this->_protectionSalt));
        }

        return $this->_protectionSalt;

    }

    private function _randomSalt()
    {

        $randString = "";
        $charUniverse = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        for ($i = 0; $i < 7; $i++) {
            $randInt = rand(0, 61);
            $randChar = $charUniverse[$randInt];
            $randString .= $randChar;
        }
        return $randString;

    }

    private function getCalendarPrice()
    {
        return str_replace(',', '.', $this->customerEp1Session['calendar_price']);
    }

    private function getProductId()
    {
        $id = $this->getRequest()->getParam('product') ? $this->getRequest()->getParam('product') : $this->customerEp1Session['product_id'];
        return $id;
    }

    public function getDiscountAmount($couponCode)
    {
        $couponModel = $this->_objectManager->create('\Magento\SalesRule\Model\Coupon');
        $saleRuleModel = $this->_objectManager->create('\Magento\SalesRule\Model\Rule');

        $ruleId = $couponModel->loadByCode($couponCode)->getRuleId();
        $rule = $saleRuleModel->load($ruleId);
        return $rule->getDiscountAmount();
    }


    private function _redirectToCategory()
    {
        $redirectionUrl = $this->_url->getUrl('motive.html');
        return $this->_redirectCustom($redirectionUrl);
    }

    private function _redirectToSuccess()
    {

        $session = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);

        if ($session->getLastRealOrderId()) {
            return $this->_redirect('checkout/onepage/success');
        }
        return false;
    }

    private function _redirectCustom($url)
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($url);
        return $resultRedirect;
    }

    public function getDiscount($quote)
    {
        $discount = 0;
        $items = $quote->getItems();

        if (is_array($items)) {

            $item = array_shift($items);
            $discount = $item->getDiscountAmount();
        }

        return $discount;
    }

    public function formatPrice($value)
    {
        $price = sprintf('%.2F', $value);
        $price = str_replace('.', ',', $price);
        return $price;
    }

    protected function setcustomerEp1Session()
    {

        $ep1Data = $this->_ep1StepsSession->getCustomerEp1Session();
        $this->customerEp1Session = is_array($ep1Data) ? $ep1Data : false;

        return $this->customerEp1Session;
    }
}
