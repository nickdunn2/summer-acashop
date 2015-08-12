<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * Class ReceiptController shows a receipt for the order they just placed
 * @package Aca\Bundle\ShopBundle\Controller
 */
class ReceiptController extends Controller
{
    /**
     * Display a receipt for a completed order
     */
    public function showAction()
    {

        /** @var Session $session */
        $session = $this->get('session');

        $session->remove('cart');

        // Acquire the orderId (from session)
        $orderId = $session->get('completed_order_id');

        $order = $this->get('aca.order');
        $products = $order->getProducts();

        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        // Calculate the subtotal for each item and the grand total
        $grandTotal = number_format(0, 2);

        foreach($products as $product) {
            $product->total_price = $product->price * $product->quantity;
            $grandTotal += $product->total_price;
        }

        // Create a template for the receipt (make sure it extends base.html.twig)

        return $this->render('AcaShopBundle:Receipt:receipt.html.twig',
            array(
                'orderId' => $orderId,
                'products' => $products,
                'billing' => $billingAddress,
                'shipping' => $shippingAddress,
                'grandTotal' => $grandTotal
            )
        );

        // Hand off the order product data and the address data to the template

        // In the template, display the addresses, purchased products and order total
    }
}