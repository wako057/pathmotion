<?php
require "require.php";

class Main extends Controller {

    public static function render()
    {
        require 'views/header.phtml';
        require 'views/index.phtml';
        require 'views/header.phtml';
    }
}



Main::render();