<?php

namespace pyngon\m3u8;

class HLSMedia {
    const ATTR_TYPE = 'TYPE';
    const ATTR_GROUPID = 'GROUP-ID';
    const ATTR_NAME = 'NAME';
    const ATTR_DEFAULT = 'DEFAULT';
    const ATTR_AUTOSELECT = 'AUTOSELECT';
    const ATTR_LANGUAGE = 'LANGUAGE';
    const ATTR_URI = 'URI';

    // private $type;
    // private $groupID;
    // private $name;
    // private $isDefault;
    // private $autoSelect;
    // private $language;
    // private $uri;

    private $stringTypeAttr;
    private $attr;
    //private $uri;
    private $playlist;

    public function __construct(){
        $this->attr = array();
        $this->stringTypeAttr = array();
    }

    public function isDefault(){
        return $this->attr[HLSMedia::ATTR_DEFAULT] = $value;
    }

    public function setAttr($key, $value) {
        if(strcasecmp($key, HLSMedia::ATTR_DEFAULT) == 0
            || strcasecmp($key, HLSMedia::ATTR_AUTOSELECT) == 0){
            $value = $this->toBoolean($value);
        } else {
            $matches = array();
            if(preg_match('/^"(.*)"$/', $value, $matches) == 1) {
                array_push($this->stringTypeAttr, $key);
                $value = $matches[1];
            }
        }

        $this->attr[$key] = $value;
    }

    public function getAttr($key) {
        if(isset($this->attr[$key])){
            return $this->attr[$key];
        }
        return null;
    }

    public function getAttrs(){
        return $this->attr;
    }

    public function isStringTypeAttr($key) {
        if(in_array($key, $this->stringTypeAttr)) {
            return true;
        }
        return false;
    }

    private function toBoolean($value){
        if(strcasecmp($value, 'YES') == 0
            || strcasecmp($value, 'TRUE') == 0){
            return true;
        }
        return false;
    }
    
    /* Getters and Setters */
    // public function setUri($uri) {
    //     $this->uri = $uri;
    // }

    // public function getUri() {
    //     return $this->uri;
    // }

    public function setPlaylist($playlist) {
        $this->playlist = $playlist;
    }

    public function getPlaylist() {
        return $this->playlist;
    }
}

?>