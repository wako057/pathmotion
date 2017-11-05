<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 15:48
 */
require_once "../require.php";



class exercice2 extends Controller {

    protected function buildResultRecursive($word, $idx) {
        
    }

    protected function buildResult($word = "") {
        $back = false;

        if ($word != '')
            $back = $this->buildResultRecursive($word, 0);

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