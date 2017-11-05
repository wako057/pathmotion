<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 14:44
 */
require_once "../require.php";

class exercice1 extends Controller {

    public function findBestProfit($diamondGrammePrices) {
        $back = 0;

        $nbElement = count($diamondGrammePrices);
        for ($cpt = 0; $cpt < $nbElement; $cpt++) {
            for ($cpt2 = $cpt; $cpt2 < $nbElement; $cpt2++) {
                if ($diamondGrammePrices[$cpt2] - $diamondGrammePrices[$cpt] > $back) {
                    $back = $diamondGrammePrices[$cpt2] - $diamondGrammePrices[$cpt];
                }

            }
        }

        return $back;
    }

    public function index() {
        $diamondGrammePrices = JewelerFactory::createYesterdayPriceArray();

        $bestprofit = $this->findBestProfit($diamondGrammePrices);

        self::render(array('bestprofit' => $bestprofit, 'price' => $diamondGrammePrices));
    }

    public static function render($args = array())
    {
        require '../views/header.phtml';
        require '../views/exercice1/exercice1.phtml';
        require '../views/footer.phtml';
    }
}

$contoller = new exercice1();
$contoller->index();
