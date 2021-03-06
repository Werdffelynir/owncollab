<?php

namespace OCA\Owncollab;

use OC\Preview;
use OC\User\Session;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TemplateResponse;

class Helper
{

    /**
     * Check current the application is running.
     * If $name return bool if current application equivalent
     * If $name missing return current application name
     *
     * @param $name
     * @return array|null|bool
     */
    static public function isApp($name = null) {
        $uri = \OC::$server->getRequest()->getRequestUri();
        $start = strpos($uri, '/apps/') + 6;
        $app = substr($uri, $start);

        if (strpos($app, '/'))
            $app = substr($app, 0, strpos($app, '/'));

        if($name)
            return $app == $name;

        return $app;
    }

    /**
     * todo: delete
     * Checked is app now
     * @param $appName
     * @return bool
     */
    static public function isAppPage($appName)
    {
        $requestUri = \OC::$server->getRequest()->getRequestUri();
        $uriParts = explode('/',trim($requestUri,'/'));
        if(strtolower($appName) === strtolower($uriParts[array_search('apps',$uriParts)+1]))
            return true;
        else return false;
    }

    /**
     * Current URI address path
     * @param $appName
     * @return bool|string
     */
    static public function getCurrentUri($appName)
    {
        $requestUri = \OC::$server->getRequest()->getRequestUri();
        $subPath = 'apps/'.$appName;
        if(strpos($requestUri, $subPath) !== false){
            $ps =  substr($requestUri, strpos($requestUri, $subPath)+strlen($subPath));
            if($ps==='/'||$ps===false) return '/';
            else return trim($ps, '/');
        }else{
            return false;
        }
    }


    /**
     * Check URI address path
     * @param $appName
     * @param $uri
     * @return bool
     */
    static public function isUri($appName, $uri)
    {
        $requestUri = \OC::$server->getRequest()->getRequestUri();
        if ( strpos($requestUri, $appName."/".$uri) !== false)
            return true;
        else return false;
    }

    /**
     * Render views and transmit data to it
     * @param $appName
     * @param $view
     * @param array $data
     * @return string
     */
    static public function renderPartial($appName, $view, array $data = [])
    {
        $response = new TemplateResponse($appName, $view, $data, '');
        return $response->render();
    }


    /**
     * Session worker
     * @param null $key
     * @param null $value
     * @return mixed|Sessioner
     */
    static public function session($key=null, $value=null)
    {
        static $ses = null;
        if($ses === null) $ses = new Sessioner();
        if(func_num_args() == 0)
            return $ses;
        if(func_num_args() == 1)
            return $ses->get($key);
        else
            $ses->set($key,$value);
    }

    /**
     * @param null $key
     * @param bool|true $clear
     * @return bool|string|array
     */
    static public function post($key=null, $clear = true)
    {
        if(func_num_args() === 0)
            return $_POST;
        else{
            if(isset($_POST[$key])) {
                if($clear)
                    return trim(strip_tags($_POST[$key]));
                else return $_POST[$key];
            }
        }
        return false;
    }

    /**
     * Encode string with salt
     * @param $unencoded
     * @param $salt
     * @return string
     */
    static public function encodeBase64($unencoded, $salt)
    {
        $string = base64_encode($unencoded);
        $encodeStr = '';
        $arr = [];
        $x = 0;
        while ($x++< strlen($string)) {
            $arr[$x-1] = md5(md5($salt.$string[$x-1]).$salt);
            $encodeStr = $encodeStr.$arr[$x-1][3].$arr[$x-1][6].$arr[$x-1][1].$arr[$x-1][2];
        }
        return $encodeStr;
    }

    static public function decodeBase64($encoded, $salt){
        $symbols="qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM=";
        $x = 0;
        while ($x++<= strlen($symbols)-1) {
            $tmp = md5(md5($salt.$symbols[$x-1]).$salt);
            $encoded = str_replace($tmp[3].$tmp[6].$tmp[1].$tmp[2], $symbols[$x-1], $encoded);
        }
        return base64_decode($encoded);
    }

    static public function appName($name=false){
        static $_name = null;
        if($name) $_name = $name;
        return $_name;
    }


    static public function toTimeFormat($timeString){
        return date( "Y-m-d H:i:s", strtotime($timeString) );
    }

    /**
     * @return \OCP\IDBConnection
     */
    static public function getConnection(){
        return \OC::$server->getDatabaseConnection();
    }

    static public function randomString($length = 6, $symbols = ''){
        $abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789".$symbols;
        $rand = "";
        for($i=0; $i<$length; $i++) {
            $rand .= $abc[rand()%strlen($abc)];
        }
        return $rand;
    }

    /**
     * @param $filePath User/files/....
     * @return string
     */
    static public function prevImg($filePath)
    {
//        $filePath = 'files/Photos/Paris.jpg';
        //$preview = \OC::$server->getPreviewManager()->createPreview($filePath, 128, 128, true);
        $conf = explode('/files/', $filePath);
        $preview = new Preview($conf[0], '/', 'files/'.$conf[1], 128, 128, true);
        $resp = new DataDisplayResponse($preview->getPreview()->data(), Http::STATUS_OK, ['Content-Type' => 'image/png']);
        $src = 'data: '.$preview->getPreview()->mimeType().';base64,'.base64_encode($resp->render());
        return $src;
    }
}
