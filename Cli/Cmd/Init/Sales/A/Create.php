<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Flancer32\LoginAs\Cli\Cmd\Init\Sales\A;

/**
 * Simple process that creates new sale order.
 */
class Create
{
    private const DEF_ADDR_CITY = 'Riga';
    private const DEF_ADDR_COUNTRY = 'LV';
    private const DEF_ADDR_FIRST = \Flancer32\LoginAs\Cli\Cmd\Init\Customers::DEF_CUST_01_FIRST;
    private const DEF_ADDR_LAST = \Flancer32\LoginAs\Cli\Cmd\Init\Customers::DEF_CUST_01_LAST;
    private const DEF_ADDR_PHONE = '+37129181801';
    private const DEF_ADDR_REGION = '362';
    private const DEF_ADDR_STREET = 'Street';
    private const DEF_ADDR_ZIP = '1010';
    private const DEF_CUST_EMAIL = \Flancer32\LoginAs\Cli\Cmd\Init\Customers::DEF_CUST_01_EMAIL;
    private const DEF_PROD_QTY = 2;
    private const DEF_PROD_SKU = \Flancer32\LoginAs\Cli\Cmd\Init\Catalog\A\Product::DEF_PROD_SKU;

    /** @var \Magento\Quote\Model\QuoteFactory */
    private $factQuote;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var \Magento\Quote\Model\QuoteManagement */
    private $manQuote;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $manStore;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCustomer;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProduct;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Store\Model\StoreManagerInterface $manStore,
        \Magento\Quote\Model\QuoteManagement\Proxy $manQuote,
        \Magento\Quote\Model\QuoteFactory $factQuote,
        \Magento\Customer\Api\CustomerRepositoryInterface\Proxy $repoCustomer,
        \Magento\Catalog\Api\ProductRepositoryInterface\Proxy\Proxy $repoProduct
    ) {
        $this->manObj = $manObj;
        $this->manStore = $manStore;
        $this->manQuote = $manQuote;
        $this->factQuote = $factQuote;
        $this->repoCustomer = $repoCustomer;
        $this->repoProduct = $repoProduct;
    }

    public function exec()
    {
        $store = $this->manStore->getStore();
        /* create empty quote */
        $quote = $this->factQuote->create();
        $quote->setStore($store);
        $quote->setCurrency();
        /* assign customer to quote */
        $customer = $this->repoCustomer->get(self::DEF_CUST_EMAIL);
        $quote->assignCustomer($customer);

        /* add product item to quote */
        $product = $this->repoProduct->get(self::DEF_PROD_SKU);
        $quote->addProduct($product, self::DEF_PROD_QTY);

        /* set addresses (billing, shipping) */
        $addrBilling = $quote->getBillingAddress();
        $addrBilling->setFirstname(self::DEF_ADDR_FIRST);
        $addrBilling->setLastname(self::DEF_ADDR_LAST);
        $addrBilling->setStreet(self::DEF_ADDR_STREET);
        $addrBilling->setCity(self::DEF_ADDR_CITY);
        $addrBilling->setCountryId(self::DEF_ADDR_COUNTRY);
        $addrBilling->setRegionId(self::DEF_ADDR_REGION);
        $addrBilling->setPostcode(self::DEF_ADDR_ZIP);
        $addrBilling->setTelephone(self::DEF_ADDR_PHONE);
        $addrBilling->setSaveInAddressBook(true);

        $addrShipping = $quote->getShippingAddress();
        $addrShipping->setFirstname(self::DEF_ADDR_FIRST);
        $addrShipping->setLastname(self::DEF_ADDR_LAST);
        $addrShipping->setStreet(self::DEF_ADDR_STREET);
        $addrShipping->setCity(self::DEF_ADDR_CITY);
        $addrShipping->setCountryId(self::DEF_ADDR_COUNTRY);
        $addrShipping->setRegionId(self::DEF_ADDR_REGION);
        $addrShipping->setPostcode(self::DEF_ADDR_ZIP);
        $addrShipping->setTelephone(self::DEF_ADDR_PHONE);
        $addrShipping->setSaveInAddressBook(true);

        /* set shipping method */
        $addrShipping->setCollectShippingRates(true);
        $addrShipping->collectShippingRates();
        $addrShipping->setShippingMethod('flatrate_flatrate');

        /* set billing method */
        $quote->setPaymentMethod(\Magento\OfflinePayments\Model\Checkmo::PAYMENT_METHOD_CHECKMO_CODE);


        /* collect totals then save quote */
        $quote->collectTotals();
        $quote->save();

        /* re-load quote to init items properly  */
        $id = $quote->getId();
        $quote = $this->manObj->create(\Magento\Quote\Model\Quote::class);
        $quote->load($id);

        /* convert quote to order (set payment method once more) */
        $quote->getPayment()->importData(['method' => \Magento\OfflinePayments\Model\Checkmo::PAYMENT_METHOD_CHECKMO_CODE]);
        $order = $this->manQuote->submit($quote);
        $order->setEmailSent(0);
        $orderId = $order->getId();
        return $orderId;
    }
}