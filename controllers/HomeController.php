<?php
class HomeController
{
    public function index()
    {
        $pageTitle = '歐印精品-首頁';
        require_once 'views/home.php';
    }
}
?>