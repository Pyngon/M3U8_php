<?php

namespace pyngon\m3u8;

class HLSSegment {
    private $duration;
    private $title;
    private $uri;
    private $programDateTime;
    private $key;

    public function __construct($duration, $title){
        $this->duration = $duration;
        $this->title = $title;
    }

    public function setUri($uri) {
        $this->uri = $uri;
    }

    public function getUri() {
        return $this->uri;
    }

    public function setDuration($duration) {
        $this->duration = $duration;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setProgramDateTime($dateTime) {
        $this->programDateTime = $dateTime;
    }

    public function getProgramDateTime() {
        return $this->programDateTime;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function getKey() {
        return $this->key;
    }
}

?>