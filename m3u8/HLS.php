<?php

namespace pyngon\m3u8;

require_once 'HLSMedia.php';
require_once 'HLSStream.php';
require_once 'HLSSegment.php';

class HLS {
    private $version;
    private $playlistType;
    private $targetDuration;
    private $allowCache;
    private $mediaSequence;

    /**
     * A 2-dimension array with 1st dimension is Group-ID and 
     * second dimension is a HLSMedia object.
     */
    private $medias;
    private $streams;
    private $segments;

    private $uri;
    private $parent;

    private $ended;

    public function __construct($uri) {
        $this->uri = $uri;
        $this->ended = false;
    }

    public function addMedia($media) {
        if($media == null){
            return;
        }

        if($this->medias == null){
            $this->medias = array();
        }

        $groupID = $media->getAttr(HLSMedia::ATTR_GROUPID);
        if(empty($groupID)){
            $groupID = "";
        }

        if(!array_key_exists($groupID, $this->medias)){
            $this->medias[$groupID] = array();
        }

        array_push($this->medias[$groupID], $media);
    }

    public function addStream($stream) {
        if($stream == null){
            return;
        }

        if($this->streams == null){
            $this->streams = array();
        }

        array_push($this->streams, $stream);
    }

    public function addSegment($segment) {
        if($segment == null) {
            return;
        }

        if($this->segments == null) {
            $this->segments = array();
        }

        array_push($this->segments, $segment);
    }

    public function getFullPath() {
        $dirpath = '';
        if($this->parent != null){
            $dirpath = $this->parent->getFullPath();
        }

        $info = pathinfo($this->uri);

        if (strcmp($info['dirname'], '.') == 0) {
            return $dirpath;
        } else if(strlen($dirpath) == 0) {
            return $info['dirname'];
        }

        return $dirpath.'/'.$info['dirname'];
    }

    public function getFullUri() {
        $path = '';
        if($this->parent != null){
            $path = $this->parent->getFullPath();
        }
        if(strlen($path) == 0) {
            return $this->uri;
        }
        return $path.'/'.$this->uri;
    }

    // Getters and Setters
    public function getMedias() {
        return $this->medias;
    }

    public function getStreams() {
        return $this->streams;
    }

    public function getSegments() {
        return $this->segments;
    }

    public function setUri($uri) {
        $this->uri = $uri;
    }

    public function getUri() {
        return $this->uri;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setPlaylistType($type) {
        $this->playlistType = $type;
    }

    public function getPlaylistType() {
        return $this->playlistType;
    }

    public function setTargetDuration($duration) {
        $this->targetDuration = $duration;
    }

    public function getTargetDuration() {
        return $this->targetDuration;
    }

    public function setAllowCache($allowCache) {
        $this->allowCache = $allowCache;
    }

    public function getAllowCache() {
        return $this->allowCache;
    }

    public function setMediaSequence($mediaSequence) {
        $this->mediaSequence = $mediaSequence;
    }

    public function getMediaSequence() {
        return $this->mediaSequence;
    }

    public function setEnded() {
        $this->ended = true;
    }

    public function isEnded() {
        return $this->ended;
    }

}
?>