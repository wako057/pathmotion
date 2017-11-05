<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 14:44
 */
require "../require.php";

class exercice1 extends Controller {

    public function index() {
        $price = JewelerFactory::createYesterdayPriceArray();

        var_dump($price);

        self::render();
    }

    public static function render()
    {
        require '../views/header.phtml';
        require '../views/exercice1/exercice1.phtml';
        require '../views/footer.phtml';
    }
}

$contoller = new exercice1();
$contoller->index();
