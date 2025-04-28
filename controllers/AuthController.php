<?php
class AuthController
{
    public function login()
    {
        // Load login view
        require_once 'views/login.php';
    }

    public function register()
    {
        // Load register view
        require_once 'views/register.php';
    }
}
?>