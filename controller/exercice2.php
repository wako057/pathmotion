<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 15:48
 */
require_once "../require.php";

class exercice2 extends Controller {


    protected function swapLetter($word, $from, $to) {
        $tmp  = $word{$from};
        $word{$from} = $word{$to};
        $word{$to} = $tmp;

        return $word;
    }

    protected function buildResultRecursive($word, $idx, $max, &$back) {

        if ($idx == $max) {
            array_push($back, $word);
        } else {
            for ($cpt = $idx; $cpt <= $max; $cpt++) {
                $newWord = $this->swapLetter($word, $idx, $cpt);
                $this->buildResultRecursive($newWord, $idx +1 , $max, $back);
            }
        }
        return $back;
    }

    protected function buildResult($word = "") {
        $back = array();

        if ($word != '' && strlen($word) > 1) {
            $len  = strlen($word) - 1;

            $back =  array();
            $this->buildResultRecursive($word, 0, $len, $back);
        }

        return $back;
    }

    public function index() {

        $result = $this->buildResult(HttpRequest::getPost('word'));

        self::render(array('result' => $result));
    }

    public static function render($args = array())
    {
        require '../views/header.phtml';
        require '../views/exercice2/exercice2.phtml';
        require '../views/footer.phtml';
    }
}

$contoller = new exercice2();
$contoller->index();