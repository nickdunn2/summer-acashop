<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrderController extends Controller
{
    /**
     * Place the order
     */
    public function placeAction()
    {
        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        /** @var Session $session */
        $session = $this->get('session');
        $userId = $session->get('user_id');

        // Create an order record
        $query = 'INSERT INTO aca_order(user_id) VALUES(' . $userId . ')';
        $db->setQuery($query);
        $db->query();

        // Get back an orderId
        $orderId = $db->getLastInsertId();

        // Collect the billing and shipping address from the user
        $this->createOrderAddresses($orderId);

        // Where do we get the products from?
        // Write the products to the aca_order_product table
        $this->createOrderProducts($orderId);

        // Save the orderId in session
        $session->set('completed_order_id', $orderId);

        // Redirect them to the /receipt route
        return new RedirectResponse('/receipt');

        // Be sure to enter in the quantity and the total price in the price field in the order_product table (this was accomplished by copying the code for createOrderProducts() method)
    }

    /**
     * Save the user entered addresses in the DB
     * @param int $orderId
     * @throws Exception
     */

    protected function createOrderAddresses($orderId)
    {
        /** @var DBCommon $db */
        $db = $this->get('aca.db');

        $billingStreet = $_POST['billing_street'];
        $billingCity = $_POST['billing_city'];
        $billingState = $_POST['billing_state'];
        $billingZip = $_POST['billing_zip'];

        $shippingStreet = $_POST['shipping_street'];
        $shippingCity = $_POST['shipping_city'];
        $shippingState = $_POST['shipping_state'];
        $shippingZip = $_POST['shipping_zip'];

        // Write the billing and shipping address to the order_address table
        // Command + Option + L to shortcut for auto-spacing
        $billingQuery = 'INSERT INTO aca_order_address
                          (
                            order_id,
                            type,
                            street,
                            city,
                            state,
                            zip
                          )
                          VALUES
                          (
                            "' . $orderId . '",
                            "billing",
                            "' . $billingStreet . '",
                            "' . $billingCity . '",
                            "' . $billingState . '",
                            "' . $billingZip . '"
                          )';

        $db->setQuery($billingQuery);
        $db->query();

        $shippingQuery = 'INSERT INTO aca_order_address
                          (
                            order_id,
                            type,
                            street,
                            city,
                            state,
                            zip
                          )
                          VALUES
                          (
                            "' . $orderId . '",
                            "shipping",
                            "' . $shippingStreet . '",
                            "' . $shippingCity . '",
                            "' . $shippingState . '",
                            "' . $shippingZip . '"
                          )';


        $db->setQuery($shippingQuery);
        $db->query();

    }

    protected function createOrderProducts($orderId)
    {
        $db = $this->get('aca.db');
        $session = $this->get('session');
        $cartItems = $session->get('cart');
        $cartProductIds = [];

        foreach ($cartItems as $cartItem) {
            $cartProductIds[] = $cartItem['product_id'];
        }

        $query = 'SELECT * FROM aca_product WHERE product_id IN(' . implode(',', $cartProductIds) . ')';

        $db->setQuery($query);
        $dbProducts = $db->loadObjectList();

        /**
         * @var array $userSelectedProducts contains the merge of products/cart items
         */

        foreach ($cartItems as $cartItem) {

            foreach ($dbProducts as $dbProduct) {

                if ($dbProduct->product_id == $cartItem['product_id']) {

                    $productId = $dbProduct->product_id;
                    $quantity = $cartItem['quantity'];
                    $productPrice = $dbProduct->price * $cartItem['quantity'];

                    $query = '
                    INSERT INTO aca_order_product
                      (order_id, product_id, quantity, price)
                    VALUES
                      ("' . $orderId . '", "' . $productId . '", "' . $quantity . '", "' . $productPrice . '")';

                    $db->setQuery($query);
                    $db->query();
                }
            }
        }
    }
}

// route thank_you
// controller for thank you



?>