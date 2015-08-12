<?php

namespace Aca\Bundle\ShopBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    public function indexAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        $name = $session->get('name');
        $loggedIn = $session->get('logged_in');
        $errorMessage = $session->get('error_message');

        return $this->render('AcaShopBundle:Home:index.html.twig',
            array(
                'loggedIn' => $loggedIn,
                'name' => $name,
                'errorMessage' => $errorMessage
            )
        );
    }

    /**
     * This logs the user in
     * @return RedirectResponse
     */
    public function loginAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        // Homework Aug 6 -- Create user class (within Shop folder) with getName(), isLoggedIn(), login(), logout(), getUserId() and everything else that happens within this controller.
        // Think about efficiency!
        // How are you going to answer "Is the user logged in?"
        // Does login() need $username & $password as inputs?
        // logout() should be easy
        // See github for all the correct completed files

        // Acquire user input
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check username and password
        $query = 'SELECT * FROM aca_user WHERE username="' . $username . '" AND password="' . $password .'"';

        /**
         * @var DBCommon $db
         */
        $db = $this->get('aca.db');
        $db->setQuery($query);
        $user = $db->loadObject(); // fetches ONE row from the database!

        if(empty($user)) {

            $session->set('logged_in', 0);
            $session->set('error_message', 'Login failed, please try again');

        } else {

            $session->set('logged_in', 1);
            $session->set('name', $user->name);
            $session->set('user_id', $user->user_id);

        }

        return new RedirectResponse('/');
    }

    public function logoutAction()
    {
        /** @var Session $session */
        $session = $this->get('session');

        $session->clear();

        return new RedirectResponse('/');
    }

    public function productsAction()
    {
        $session = $this->get('session');

        $query = 'SELECT * FROM aca_product';

        $db = $this->get('aca.db');
        $db->setQuery($query);
        $products = $db->loadObjectList();
    }

}
