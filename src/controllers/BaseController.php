<?php
namespace App\controllers;

class BaseController
{
    protected $container;
    public function __construct($container)
    {
        $this->container = $container;
    }
}
?>