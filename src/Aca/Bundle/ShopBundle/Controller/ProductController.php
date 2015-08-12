<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Aca\Bundle\ShopBundle\Shop\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class ProductController
 * @package Aca\Bundle\ShopBundle\Controller
 */
class ProductController extends Controller
{
    /**
     * Show all products on ACA Shop
     */
    public function showAction()
    {
        /**
         * @var Product $product
         */
        $product = $this->get('aca.product');

        $products = $product->getAllProducts();

        return $this->render('AcaShopBundle:Products:index.html.twig',
            array(
                'products' => $products
            )
        );
    }
}
