<?php

namespace Aca\Bundle\ShopBundle\Shop;

use Aca\Bundle\ShopBundle\Db\DBCommon;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class User handles all user functionality
 * @package Aca\Bundle\ShopBundle\Shop
 */
class User
{
    /**
     * @var DBCommon
     */
    protected $db;

    protected $session;

    public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
    }

    public function getName($username)
    {

    }

    public function getUserId()
    {

    }

    public function isLoggedIn() // This should be used within getName() and getUserId()?
    {

    }

    public function login($username, $password)
    {
        // Check username and password
        $query = 'SELECT * FROM aca_user WHERE username="' . $username . '" AND password="' . $password .'"';

        $this->db->setQuery($query);
        $user = $this->db->loadObject(); // fetches ONE row from the database!

        if(empty($user)) {

            $this->session->set('logged_in', 0);
            $this->session->set('error_message', 'Login failed, please try again');

        } else {

            $this->session->set('logged_in', 1);
            $this->session->set('name', $user->name); // $user->name doesn't feel like it's right
            $this->session->set('user_id', $user->user_id); // $user->user_id doesn't feel like it's right

        }

        return new RedirectResponse('/');
    }

    public function logout()
    {
        $this->session->clear();

        return new RedirectResponse('/');
    }

}



?>