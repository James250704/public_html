<?php
class PageController
{
    public function brand()
    {
        require_once 'views/brand.php';
    }

    public function shoppingGuide()
    {
        require_once 'views/shopping-guide.php';
    }

    public function warranty()
    {
        require_once 'views/warranty.php';
    }

    public function about()
    {
        require_once 'views/about.php';
    }
}
?>