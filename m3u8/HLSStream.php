<?php

namespace pyngon\m3u8;

class HLSStream {
    const ATTR_PROGRAMID = 'PROGRAM-ID';
    const ATTR_BANDWIDTH = 'BANDWIDTH';
    const ATTR_RESOLUTION = 'RESOLUTION';
    const ATTR_CODEC = 'CODECS';
    const ATTR_AUDIO = 'AUDIO';
    const ATTR_VIDEO = 'VIDEO';

    // private $bandwidth;
    // private $programID;
    // private $codecs;
    // private $resolution;
    // private $audio;
    // private $video;

    private $stringTypeAttr;
    private $attr;
    private $uri;
    private $playlist;

    public function __construct(){
        $this->attr = array();
        $this->stringTypeAttr = array();
    }

    public function setAttr($key, $value) {
        $matches = array();
        if(preg_match('/^"(.*)"$/', $value, $matches) == 1) {
            array_push($this->stringTypeAttr, $key);
            $value = $matches[1];
        }
        $this->attr[$key] = $value;
    }

    public function getAttr($key) {
        return $this->attr[$key];
    }

    public function getAttrs() {
        return $this->attr;
    }

    public function isStringTypeAttr($key) {
        if(in_array($key, $this->stringTypeAttr)) {
            return true;
        }
        return false;
    }

    /* Getter and Setter */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    public function getUri() {
        return $this->uri;
    }

    public function setPlaylist($playlist) {
        $this->playlist = $playlist;
    }

    public function getPlaylist() {
        return $this->playlist;
    }
}

?>