<?php
/**
 * Created by IntelliJ IDEA.
 * User: wako0
 * Date: 05/11/2017
 * Time: 14:45
 */

class HttpRequest {
    private static $infos = false;

    /**
     * @author Damian
     * <p>Set a new value for a url parameter
     * If the key doesn't exist we append the new Key/Value
     * If the key exist we replace the old value by the new value
     * If the new value is NULL we delete the key and the value</p>
     * @param String	$url		The url to feed
     * @param String	$key		The name of the parameter
     * @param String	$newValue	The new value of the parameter
     * @return String	$url		The new url
     */
    static function setUrlParam($url, $key, $newValue = null) {
        if (empty($url) || empty($key)) {
            return FALSE;
        }

        $qestionMarkIndex = strpos($url, '?');
        // no param on url, append new key/value
        if (FALSE === $qestionMarkIndex) {
            return $url . '?' . $key . '=' . $newValue;
        }

        $rootUrl = substr($url, 0, $qestionMarkIndex);
        // replace the &amp; senquence with amp;
        $clearUrl = str_replace('&amp;', '|amp;|', $url);
        $url = $rootUrl;
        $params = preg_split("/[&?](\w+)=([^&]*)/", $clearUrl, -1, PREG_SPLIT_DELIM_CAPTURE);
        $arrayLength = count($params);
        $found = false;
        for ($i = 1; $i < $arrayLength; $i += 3 ) {
            $currentKey = $params[$i];
            $currentValue = (isset($params[$i + 1])) ? str_replace('&', '', $params[$i + 1]) : '';
            if ($key == $currentKey) {
                $found = true;
                // continue, don't write this key
                if (NULL == $newValue) continue;
                // replace the value for this key
                $currentValue = $newValue;
            }
            $url .= (1 != $i) ? '&' : '?';
            $url .= $currentKey . '=' . $currentValue;
        }

        // replace the amp; senquence with &amp;
        $url =  str_replace('|amp;|', '&amp;', $url);

        // append the new key/value
        if (false == $found && NULL != $newValue) {
            $url .= '&' . $key . '=' . $newValue;
        }
        return $url;
    }


    /**
     * @author Damian
     * <p>Returns a url cleared of parameters.
     * Cuts the url at the first '?' occurence
     * If the url have no parameters, returns the original url.
     * Can be used to get the root url of a url with parameters</p>
     * @param  String $url
     * @return Boolean|String the clear url
     */
    static function flushUrlParams($url) {
        $back = false;

        $cut_index = strpos($url, '?');

        if (false === $cut_index)
            $back = $url;
        else
            $back = substr($url, 0, $cut_index);

        return $back;
    }

    /**
     * Renvoie la methode HTTP de la requete en cours
     * @return Ambigous <boolean, string> Si $_REQUEST_METHOD existe renvoie GET / POST / PUT - False si echec
     */
    public static function getHttpMethod() {
        $back = false;

        if (isset($_SERVER['REQUEST_METHOD']))
            $back = $_SERVER['REQUEST_METHOD'];

        return $back;
    }


    /**
     * Renvoie une entree particuliere des Header HTTP (par exemple: Accept-Language)
     * @param string $key
     * @return Ambigous <boolean, string> Renvoi la valeur du header demandé si trouvé
     */
    public static function getHeader($key) {
        $back = false;

        $headers = getallheaders();
        foreach ($headers as $index => $value) {
            if ($index == $key) {
                $back = $value;
                break;
            }
        }

        return $back;
    }


    /**
     * Renvoie le Request URI sinon le SCRIPT_URL (mod_rewrite)
     * @return Ambigous <boolean, string> REQUEST_URI ou SCRIPT_URL si existe - False sinon
     */
    public static function getRequestUri() {
        $back = false;

        if (!empty($_SERVER['REQUEST_URI'])) {
            $back = $_SERVER['REQUEST_URI'];
        }
        if ($back == false && $_SERVER['SCRIPT_URL'] != false) {
            $back = $_SERVER['SCRIPT_URL'];
        }

        return $back;
    }


    /**
     * Retourne la query string a partir QUERY_STRING sinon a partir de REQUEST_URI
     * @return Ambigous <boolean, string> la query string - False sinon
     */
    public static function getQueryString() {
        $back = false;

        if ($_SERVER['QUERY_STRING'] != false)
            $back = $_SERVER['QUERY_STRING'];

        if ($back == false && $_SERVER['REQUEST_URI'] != false && strpos($_SERVER['REQUEST_URI'], '?') && strlen($_SERVER['REQUEST_URI']) > (strpos($_SERVER['REQUEST_URI'], '?') +1)) {
            $back = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '?') +1);
        }

        return $back;
    }




    /**
     * Retourne l'url complete demandé, repose sur
     * SCRIPT_URI(/QUERY_STRING) si existe, sinon
     * http://HTTP_HOST(/REQUEST_URI) si existe, sinon
     * http://HTTP_HOST/$_SERVER['REDIRECT_URL'];
     * http://HTTP_HOST/REQUEST_URI sinon
     *
     * @return Ambigous <boolean, string>
     */
    public static function getUrlAcces() {
        $back = false;

        if (php_sapi_name() != 'cli') {

            if ( isset($_SERVER ['SCRIPT_URI']) ) {
                $back = $_SERVER ['SCRIPT_URI'] . '?';
                if ( isset( $_SERVER ['QUERY_STRING'] ) ) {
                    $back .= $_SERVER ['QUERY_STRING'] ;
                }
            }
            if ( $back == '' && isset($_SERVER ['REDIRECT_URL']) ) {
                $back = 'http://' . $_SERVER ['HTTP_HOST'];
                if ( isset($_SERVER["REQUEST_URI"]) ) {
                    $back .= $_SERVER["REQUEST_URI"];
                }
            }
            if ( $back == '' && isset($_SERVER ['REDIRECT_QUERY_STRING']) ) {
                $back = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REDIRECT_URL'] . '?' . $_SERVER["REDIRECT_QUERY_STRING"];
            }
            if ( $back == '' && isset( $_SERVER ['HTTP_HOST'] ) && isset( $_SERVER ['REQUEST_URI'] ) ) {
                $back = 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }
        } else {
            $back = WIML_SERVICE_ROOT . realpath($_SERVER["SCRIPT_NAME"]);
        }
        // pour les script on met le fil
        if ($back == '' && defined('WIML_FORCED_SCRIPT') && WIML_FORCED_SCRIPT == true) {
            $back = WIML_SERVICE_ROOT . realpath($_SERVER["SCRIPT_NAME"]);
        }

        return $back;
    }



    /**
     * Retourne un tableau des GET POST fusionné grace a array_walk_recursive
     * @return array Le tableau GET fusionne au tableau POST
     */
    public static function getParamTab() {

        if (self::$infos == false)
            self::init();

        return self::$infos['REQUEST'];
    }



    /**
     * Renvoi les infos comme $_REQUEST, en fouillant dans le GET si fail dans le POST (delta par rapport a $_REQUEST), mais ne va pas chercher dans le COOKIE
     * @param string $key
     * @return Ambigous <boolean, mixed> Renvoi la valeur demandé si trouvé, false sinon
     */
    public static function getParam($key) {
        $back = false;

        if (self::$infos == false)
            self::init();

        if (array_key_exists($key, self::$infos['REQUEST']))
            $back = self::$infos['REQUEST'][$key];


        return $back;
    }


    /**
     * Ecrase la valeur GET du tableau local sans toucher $_GET php
     * @param string $key
     * @param <mixed> $value
     * @return boolean
     */
    public static function setQuery($key, $value) {

        if (self::$infos == false)
            self::init();

        self::$infos['GET'][$key] = $value;

        return true;
    }



    /**
     * Ecrase la valeur GET du tableau local sans toucher $_GET php
     * @param string $key
     * @param <mixed> $value
     * @return boolean
     */
    public static function setPost($key, $value) {

        if (self::$infos == false)
            self::init();

        self::$infos['POST'][$key] = $value;

        return true;
    }



    /**
     * Ecrase la valeur GET du tableau local sans toucher $_GET php
     * @param string $key
     * @param <mixed> $value
     * @return boolean
     */
    public static function setParam($key, $value) {

        if (self::$infos == false)
            self::init();

        self::$infos['REQUEST'][$key] = $value;

        return true;
    }


    /**
     * Renvoi un cookie
     * @param string $key
     * @return Ambigous <boolean, mixed> Retourne le cookie si trouvé, false sinon
     */
    public static function getCookie($key) {
        $back = false;

        if (self::$infos == false)
            self::init();

        if (array_key_exists($key, self::$infos['COOKIE']))
            $back = self::$infos['COOKIE'][$key];

        return $back;
    }



    public static function getPostTab() {

        if (self::$infos == false)
            self::init();

        $back = self::$infos['POST'];

        return $back;
    }

    /**
     * Renvoi la valeur, si elle existe, dans le POST d'un clef passé en parametre
     * @param string $key
     * @return Ambigous <boolean, mixed> String ou Array si trouvé, false sinon
     */
    public static function getPost($key) {
        $back = false;

        if (self::$infos == false)
            self::init();

        if (array_key_exists($key, self::$infos['POST']))
            $back = self::$infos['POST'][$key];

        return $back;
    }



    /**
     * Retourne une copie du tableau $_GET
     * @return boolean
     */
    public static function getQueryTab() {

        if (self::$infos == false)
            self::init();

        $back = self::$infos['GET'];

        return $back;
    }

    /**
     * Renvoi la valeur, si elle existe, dans le GET d'un clef passé en parametre
     * @param string $key
     * @return Ambigous <boolean, mixed> String ou Array si trouvé, false sinon
     */
    public static function getQuery($key) {
        $back = false;

        if (self::$infos == false)
            self::init();

        if (array_key_exists($key, self::$infos['GET']))
            $back = self::$infos['GET'][$key];

        return $back;
    }


    /**
     * On attend des PUT formatter comme des POST si jamais on a des donnees autre il faudra
     * modifier le code en consequence
     * @param unknown $key
     * @return Ambigous <boolean, string> String du body si trouvé, false sinon
     */
    public static function getPut() {
        $back = false;

        if (self::$infos == false)
            self::init();

        if (self::$infos['PUT'] == null)
            self::initPut();

        $back = self::$infos['PUT'];

        return $back;
    }


    /**
     * Retroune le body de la requete Http dans la limite de 8192 octets
     * @throws Exception Si on a pas reussit a lire sur l'entree standard
     * @return string Le body de la requete Http
     */
    public static function getBodyRaw() {
        $back = false;

        try {
            if (!($putFd = fopen("php://input", "r")))
                throw new Exception("Can't open file descriptor on the standard php://input.");


            $data = fread($putFd, 8192);

            $back = $data;

            if (!fclose($putFd))
                throw new Exception("Can't close file Descriptor.");

        } catch (Exception $e) {
            Logger::getInstance()->logError($e->getMessage(), 'wap_http_error');
        }

        return $back;
    }


    /**
     * On lit les donnees PUT
     * @return boolean
     */
    private static function initPut() {
        $back = false;

        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

            $data = self::getBodyRaw();
            self::$infos['_raw_']['PUT'] = $data;

            self::$infos['PUT'] = $data;

        }
        return $back;
    }


    /**
     * Methode permettant de recuperer les infos GET POST COOKIES PUT (suppression magic_quote)
     * @return array Un tableau contenant GET POST COOKIE (PUT si present et deja demandé)
     */
    public static function debug() {

        if (self::$infos == false)
            self::init();

        return self::$infos;
    }


    /**
     * Methode recursive permettant de bufferiser les entrees
     * @param array $param Tableau a parser pour copie
     * @return multitype:string unknown multitype:string NULL unknown
     */
    private static function initRecur($param, $cpt) {
        $back = array();

        if ($cpt > 5) // limité a 5 recursions pour eviter tout probleme de Dos
            return $back;

        $cpt++;

        if (is_array($param) && count($param) > 0) {

            $flagGPC = get_magic_quotes_gpc();

            foreach ($param as $key => $value) {

                if (is_array($value)) {

                    $back[$key] = self::initRecur($value, $cpt);

                } else {
                    if ($flagGPC == false)
                        $back[$key] = $value;
                    else
                        $back[$key] = stripslashes($value);
                }
            }
        }

        return $back;
    }


    /**
     * Methode general d'initialisation de bufferisation des entrees
     */
    private static function init() {

        self::$infos = array();
        self::$infos['PUT'] = array();
        self::$infos['_raw_'] = array();

        $flagGPC = get_magic_quotes_gpc();


        self::$infos['_raw_']['GET'] = $_GET;
        self::$infos['_raw_']['POST'] = $_POST;
        self::$infos['_raw_']['REQUEST'] = $_REQUEST;
        self::$infos['_raw_']['COOKIE'] = $_COOKIE;
        self::$infos['GET'] = self::initRecur($_GET, 0);
        self::$infos['POST'] = self::initRecur($_POST, 0);
        self::$infos['REQUEST'] = array_merge(self::$infos['GET'], self::$infos['POST']);
        self::$infos['COOKIE'] = self::initRecur($_COOKIE, 0);

    }


    /**
     * Si on utilise la classe en object, on init la bufferisation
     */
    private function __construct() {
        if (self::$infos == false) {
            self::init();
        }

    }


    /**
     * On nettoie le tableau bufferisé
     */
    public function __destruct(){
        unset(self::$infos['GET']);
        unset(self::$infos['POST']);
        unset(self::$infos['REQUEST']);
        unset(self::$infos['COOKIE']);
        unset(self::$infos['PUT']);
        unset(self::$infos['_raw_']);
        self::$infos = false;
    }
}
