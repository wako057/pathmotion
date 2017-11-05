<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 15:48
 */
require_once "../require.php";

class exercice2 extends Controller {

    protected function buildResultRecursive($idx, $word, $input) {
        $len = strlen($word);
        $back  = array();

        if ($idx == $len) {
            return $back;
        } else
            array_merge($back, $this->buildResultRecursive($idx +1, $word, $back));
        return $back;
    }

    protected function buildResult($word = "") {
        $back = array();

        if ($word != '') {
            $back =  array($word);
            $back = $this->buildResultRecursive(0, $word, $back);
        }

        return $back;
    }

    public function index() {

        $result = $this->buildResult(HttpRequest::getPost('word'));

        if (HttpRequest::getPost('word') !== false) {
            var_dump($result);
        }
var_dump(HttpRequest::getPostTab());
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