<?php
namespace App\Controllers;

class HomeController extends BaseController
{
    public function index() : void
    {
         $this->render(
            'home/index',
            [
                'title' => 'ProSaúde'
            ],
            'public'
        );
    }

    public function sobre() : void
    {
         $this->render(
            'home/sobre',
            [
                'title' => 'Sobre o ProSaúde'
            ],
            'public'
        );
    }
}