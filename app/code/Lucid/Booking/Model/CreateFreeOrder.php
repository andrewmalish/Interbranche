<?php

namespace Lucid\Booking\Model;

class CreateFreeOrder
{
    protected $_customerSession;
    protected $ep1StepsSession;
    protected $checkoutSession;
    protected $responseFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Quote\Model\Quote\Address\Rate $shippingRate,
        \Magento\Customer\Model\Session $_customerSession,
        \Lucid\Booking\Api\Data\Ep1StepsSessionInterface $ep1StepsSession


    ) {
        $this->_storeManager = $storeManager;
        $this->_productFactory = $productFactory;
        $this->checkoutSession = $checkoutSession;
        $this->quoteManagement = $quoteManagement;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->_responseFactory = $responseFactory;
        $this->orderService = $orderService;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->shippingRate = $shippingRate;
        $this->ep1StepsSession = $ep1StepsSession;
        $this->_customerSession = $_customerSession;

    }
    /**
     * Create Order On Your Store
     *
     * @param array $orderData
     * @return int $orderId
     *
     */
    public function createFreeOrder($orderData = array()) {

        $orderData = $this->_prepareOrderData();
        $store = $this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        //init the customer
        $customer = $this->_customerSession->getCustomer();
        if(!$customer) {
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($orderData['email']);// load customet by email address
            //check the customer
            if (!$customer->getEntityId()) {
                //If not avilable then create this customer
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email'])
                    ->setPassword($orderData['email']);
                $customer->save();
            }
            //init the quote
        }
        if($customer->getDefaultBillingAddress()){
            $orderData['billing_address'] = $customer->getDefaultBillingAddress()->getData();
        }


//        $cart_id = $this->cartManagementInterface->createEmptyCart();
//        $cart = $this->cartRepositoryInterface->get($cart_id);
        $cart = $this->checkoutSession->getQuote();
        $cart->setStore($store);
        $cart->removeAllItems();
        // if you have already buyer id then you can load customer directly
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $cart->setCurrency();
        $cart->assignCustomer($customer); //Assign quote to customer
        //add items in quote
        foreach($orderData['items'] as $item){
            $product = $this->_productFactory->create()->load($item['product_id']);

            $price = 0.00;
            $product
                ->setPrice($price)
                ->setBasePrice($price)
                ->setCustomPrice($price);

            $cart->addProduct(
                $product,
                intval($item['qty'])
            );
        }

        //Set Address to quote @todo add section in order data for seperate billing and handle it
        $cart->getBillingAddress()->addData($orderData['billing_address']);
        $cart->getShippingAddress()->addData($orderData['billing_address']);
        // Collect Rates and Set Shipping & Payment Method
        $this->shippingRate
            ->setCode('freeshipping_freeshipping')
            ->getPrice(1);
        $shippingAddress = $cart->getShippingAddress();

        //@todo set in order data
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate'); //shipping method
        $cart->getShippingAddress()->addShippingRate($this->shippingRate);
        $cart->setPaymentMethod('checkmo'); //payment method
        //@todo insert a variable to affect the invetory
        $cart->setInventoryProcessed(false);
        // Set sales order payment
        $cart->setCouponCode($orderData['coupon'])->collectTotals()->save();
        $cart->getPayment()->importData(['method' => 'checkmo']);
//        $cart->->getQuote()->setCouponCode($orderData['coupon'])->collectTotals()->save();

//        $cart->coupon() setCouponCode($orderData['coupon']);
        // Collect total and saeve
        $cart->collectTotals();
        // Submit the quote and create the order
        $cart->save();

        $cart = $this->cartRepositoryInterface->get($cart->getId());
        $order_id = $this->cartManagementInterface->placeOrder($cart->getId());
        if($order_id) {
           $this->_redirectToSuccess();
        }
        return $order_id;
    }

    private function _redirectToSuccess(){
        $successUrl = '/checkout/onepage/success';
        $this->_responseFactory->create()->setRedirect($successUrl)->sendResponse();
        exit();
    }

    protected function _prepareOrderData(){

        $customer  = $this->ep1StepsSession->getCustomer7pathInfo();
        $ep1Data   = $this->ep1StepsSession->getCustomerEp1Session();

        $customerData = $customer['customer7pathInfo'];
        $email  = $customerData->email == '' ? 'default@interbranche.com' :  $customerData->email;
        $firstName = $customerData->first_name == '' ? 'Default' :  $customerData->first_name;
        $lastName = $customerData->last_name == '' ? 'Name' :  $customerData->first_name;
        $street = 'street';
        $city = 'city';
        $postcode = 'postcode';
        $telephone = '1235789';

        $orderData = [
            'currency_id'  => 'EUR',
            'email'        => $email, //buyer email id
            'coupon'       => $ep1Data['couponCode'],
            'billing_address' =>[
                'firstname'    => $firstName, //address Details
                'lastname'     => $lastName,
                'street' => $street,
                'city' => $city,
                'country_id' => 'DE',
                'postcode' => $postcode,
                'telephone' => $telephone,
                'fax' => $telephone,
                'save_in_address_book' => 0
            ],
            'items' => [ //array of product which order you want to create
                ['product_id'=>$ep1Data['product_id'],'qty'=>1],

            ]
        ];

        return $orderData;

    }

}