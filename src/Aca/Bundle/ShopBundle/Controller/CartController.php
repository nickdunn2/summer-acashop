<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller
{
    /**
     * Show all products on ACA Shop
     */
    public function addAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        // Get the cart from session, it may be empty the first time around.
        $cart = $session->get('cart');

        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // First time someone tries to add something to your cart
        if(empty($cart)) {

            $cart[] = array(
                'product_id' => $productId,
                'quantity' => $quantity
            );

        } else { // Something is already in your cart

            $existingItem = false;

            foreach ($cart as &$cartItem) {

                // If a product is existing in the shopping cart
                if ($cartItem['product_id'] == $productId) {

                    $existingItem = true;

                    // Add to the existing quantity
                    $cartItem['quantity'] += $quantity;
                }
            }

            // Brand new item
            if ($existingItem == false){

                $cart[] = array(
                    'product_id' => $productId,
                    'quantity' => $quantity
                );
            }
        }

        $session->set('cart', $cart);

        return new RedirectResponse('/cart');

    }

    /**
     * Show the contents of the user's shopping cart
     */
    public function showAction()
    {
        try {

            $cart = $this->get('aca.cart');
            $userSelectedProducts = $cart->getProducts();
            $grandTotal = number_format($cart->getGrandTotal(), 2);

            return $this->render('AcaShopBundle:Cart:items.html.twig',
                array(
                    'products' => $userSelectedProducts,
                    'grandTotal' => $grandTotal
                )
            );

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();

            return $this->render('AcaShopBundle:Cart:items.html.twig',
                array(
                    'errorMessage' => $errorMessage
                )
            );
        }


    }

    /**
     * Delete one item from your shopping cart
     * @return RedirectResponse
     */
    public function deleteAction()
    {
        $productId = $_POST['product_id'];
        $cart = $this->get('aca.cart');
        $cart->delete($productId);

        return new RedirectResponse('/cart');
    }

    /**
     * Update the quantity for one particular product in the cart
     * @return RedirectResponse
     */
    public function updateAction()
    {
        $productId = $_POST['product_id'];
        $updatedQuantity = $_POST['quantity'];
        $cart = $this->get('aca.cart');
        $cart->updateQuantity($productId, $updatedQuantity);

        return new RedirectResponse('/cart');
    }

    /**
     * Show shipping address form
     */
    public function shippingAddressAction()
    {
        // Figure out who the user is?

        /** @var Session $session */
        $session = $this->get('session');

        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        /** @var int $userId  Logged in user identifier */
        $userId = $session->get('user_id');

        if(empty($userId)) {
            $session->set('error_message', 'You must log in to proceed further with your purchase');
            return new RedirectResponse('/');
        }

        // Get the shipping_address_id and billing_address_id from the user table

        $query = 'SELECT shipping_address_id, billing_address_id FROM aca_user WHERE user_id = ' . $userId;

        $db->setQuery($query);
        $shippingIds = $db->loadObject();

        $shippingAddressId = $shippingIds->shipping_address_id;
        $billingAddressId = $shippingIds->billing_address_id;

        // Query the address table with these two ids

        $shippingQuery = 'SELECT * FROM aca_address WHERE address_id = ' . $shippingAddressId;
        $db->setQuery($shippingQuery);
        $shippingAddress = $db->loadObject();

        $billingQuery = 'SELECT * FROM aca_address WHERE address_id = ' . $billingAddressId;
        $db->setQuery($billingQuery);
        $billingAddress = $db->loadObject();

        // Hand that off to the template

        return $this->render('AcaShopBundle:Shipping:address.html.twig',
            array(
                'shipping' => $shippingAddress,
                'billing' => $billingAddress
            )
        );
    }

}
