<?php
class PageController
{
    public function brand()
    {
        $pageTitle = '歐印精品-品牌介绍';
        require_once 'views/brand.php';
    }

    public function shoppingGuide()
    {
        $pageTitle = '歐印精品-購物說明';
        require_once 'views/shopping-guide.php';
    }

    public function warranty()
    {
        $pageTitle = '歐印精品-維修保固';
        require_once 'views/warranty.php';
    }

    public function about()
    {
        $pageTitle = '歐印精品-關於我們';
        require_once 'views/about.php';
    }
}
?>