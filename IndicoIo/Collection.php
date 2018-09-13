<?php
/**
 * User: gpriday
 * Date: 2018/09/13
 * Time: 10:09
 */

namespace IndicoIo;

use IndicoIo\Utils\Image;
use IndicoIo\Utils\ImageException;

class Collection
{
    var $name;
    var $domain;

    function __construct($name, $domain=NULL, $shared=NULL) {
        $this->keywords = array(
            "domain" => $domain,
            "collection" => $name,
            "shared" => $shared
        );
    }

    function _callService($data, $service, $method, $params = array()) {
        $params = array_merge($this->keywords, $params);
        return IndicoIo::_callService($data, $service, $method, $params);
    }

    function addData($data, $params=array()) {
        if (gettype($data[0]) != 'array') {
            $params['batch'] = False;
            try {
                $data[0] = Image::processImage($data[0], 512, true);
            } catch (ImageException $e) {}
        } else {
            $params['batch'] = True;
            try {
                $x = array();
                $y = array();
                foreach ($data as $pair) {
                    array_push($x, $pair[0]);
                    array_push($y, $pair[1]);
                }
                $x = Image::processImage($x, 512, true);
                // equivalent to python's zip(x, y)
                $data = array_map(NULL, $x, $y);
            } catch (ImageException $e) {}
        }
        return $this->_callService($data, 'custom', 'add_data', $params);
    }

    function predict($data, $params=array()) {
        try {
            $data = Image::processImage($data, 512, true);
        } catch (ImageException $e) {}
        return $this->_callService($data, 'custom', 'predict', $params);
    }

    function removeExample($data, $params=array()) {
        try {
            $data = Image::processImage($data, 512, true);
        } catch (ImageException $e) {}
        return $this->_callService($data, 'custom', 'remove_example', $params);
    }

    function train($params=array()) {
        return $this->_callService(NULL, 'custom', 'train', $params);
    }

    function info($params=array()) {
        return $this->_callService(NULL, 'custom', 'info', $params);
    }

    function wait($interval=1, $params=array()) {
        while (TRUE) {
            $status = $this->info()['status'];
            if ($status == "ready") {
                break;
            } else if ($status != "training") {
                trigger_error(
                    "The `collection` training has ended with the failure: " + $status,
                    E_USER_WARNING
                );
                break;
            }
            sleep($interval);
        }
    }

    function clear($params=array()) {
        return $this->_callService(NULL, 'custom', 'clear_collection', $params);
    }

    function rename($name, $params=array()) {
        $params['name'] = $name;
        $result = $this->_callService(NULL, 'custom', 'rename', $params);
        $this->keywords['collection'] = $name;
        return $result;
    }

    function register($params=array()) {
        return $this->_callService(NULL, 'custom', 'register', $params);
    }

    function deregister($params=array()) {
        return $this->_callService(NULL, 'custom', 'deregister', $params);
    }

    function authorize($email, $params=array()) {
        $params['email'] = $email;
        if (!array_key_exists('permission_type', $params)) {
            $params['permission_type'] = 'read';
        }
        return $this->_callService(NULL, 'custom', 'authorize', $params);
    }

    function deauthorize($email, $params=array()) {
        $params['email'] = $email;
        return $this->_callService(NULL, 'custom', 'deauthorize', $params);
    }
}