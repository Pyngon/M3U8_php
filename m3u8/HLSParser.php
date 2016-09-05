<?php

namespace pyngon\m3u8;

require_once 'HLSTag.php';
require_once 'HLS.php';

class HLSParser {

    //private $tagFuncMap;

    public function __construct(){
        //$this->tagFuncMap = array(HLSTag::VERSION => 'handleVersion');
    }

    /** 
     * @return HLS object
     */
    public function parseUri($uri){
        $content = file_get_contents($uri);

        $hls = new HLS($uri);
        $this->parseContent($content, $hls);
        return $hls;
    }

    public function parseUriWithParent($rawUri, $parent){
        $parsedUrl = parse_url($rawUri);
        if($parsedUrl == false){
            echo "parse url fail, $rawUri\n";
        } else {
            $playlistUri = $rawUri;
            if(!isset($parsedUrl['scheme'])){
                /* Relative path, have to append from parent */
                $playlistUri = $parent->getFullPath().$rawUri;
            }

            $content = file_get_contents($playlistUri);
            $child = new HLS($rawUri);
            $child->setParent($parent);
            $playlist = $this->parseContent($content, $child);
            return $playlist;
        }
        return null;
    }

    private function parseContent($content, $hlsObj){
        if($content) {
            $lines = array_map("trim", explode("\n", $content));
            $numLines = count($lines);
            //echo "numlines=$numLines\n";
            if($numLines > 0){
                //echo "lines[0]=$lines[0]\n";
                if(strcmp($lines[0], HLSTag::START) == 0){
                    //$hlsObj = new HLS();
                    $lastContent = null;
                    $lastProgramDateTime = null;
                    $lastKey = null;
                    for ($i=1; $i<$numLines; $i++) {
                        //echo "lines[$i]=$lines[$i]\n";
                        $line = $lines[$i];
                        if(count($line) > 0){
                            if(strcmp(substr($line, 0, 1), '#') == 0 
                                && strcmp(substr($line, 0, 4), HLSTag::PREFIX) != 0) {
                                // comment, ignore
                                //echo "comment\n";
                                continue;
                            }

                            $matches = array();

                            if(preg_match('/^('.HLSTag::PREFIX.'[^:|$]+)[:|$]?(.*)$/', $line, $matches) == 1){
                                //echo 'm1='.$matches[1].' m2='.$matches[2]."\n";

                                if(strcmp($matches[1], HLSTag::VERSION) == 0) {

                                    $hlsObj->setVersion($matches[2]);
                                    
                                } else if(strcmp($matches[1], HLSTag::PLAYLIST_TYPE) == 0) {

                                    $hlsObj->setPlaylistType($matches[2]);

                                } else if(strcmp($matches[1], HLSTag::TARGET_DURATION) == 0) {

                                    $hlsObj->setTargetDuration($matches[2]);

                                } else if(strcmp($matches[1], HLSTag::KEY) == 0) {

                                    //$hlsObj->setKey($matches[2]);
                                    $lastKey = $matches[2];

                                } else if(strcmp($matches[1], HLSTag::MEDIA_SEQUENCE) == 0) {

                                    $hlsObj->setMediaSequence($matches[2]);

                                } else if(strcmp($matches[1], HLSTag::END) == 0){
                                    
                                    $hlsObj->setEnded();

                                } else if(strcmp($matches[1], HLSTag::PROGRAM_DATE_TIME) == 0){

                                    $lastProgramDateTime = $matches[2];

                                } else if (strcmp($matches[1], HLSTag::MEDIA) == 0) {

                                    $attrList = array();
                                    //if(preg_match_all('/([^=,]*)=["]?([^,="]*)["]?/', $matches[2], $attrList, PREG_SET_ORDER) > 0) {
                                    if(preg_match_all('/([^=,]*)=("[^"]*"|[^,"]*)/', $matches[2], $attrList, PREG_SET_ORDER) > 0) {
                                        $media = new HLSMedia();
                                        for ($j=0;$j<count($attrList);$j++) {
                                            //echo 'media '.$attrList[$j][1].'='.$attrList[$j][2]."\n";
                                            $media->setAttr($attrList[$j][1], $attrList[$j][2]);
                                        }
                                        $hlsObj->addMedia($media);
                                        $mediaUri = $media->getAttr(HLSMedia::ATTR_URI);
                                        if($mediaUri != null){
                                            $playlist = $this->parseUriWithParent($mediaUri, $hlsObj);
                                            $media->setPlaylist($playlist);
                                        }
                                    }

                                } else if(strcmp($matches[1], HLSTag::STREAM) == 0) {

                                    $attrList = array();
                                    //if(preg_match_all('/([^=,]*)=["]?([^,="]*)["]?/', $matches[2], $attrList, PREG_SET_ORDER) > 0) {
                                    if(preg_match_all('/([^=,]*)=("[^"]*"|[^,"]*)/', $matches[2], $attrList, PREG_SET_ORDER) > 0) {
                                        $stream = new HLSStream();
                                        for ($j=0;$j<count($attrList);$j++) {
                                            $stream->setAttr($attrList[$j][1], $attrList[$j][2]);
                                        }

                                        $hlsObj->addStream($stream);
                                        $lastContent = $stream;
                                    }

                                } else if(strcmp($matches[1], HLSTag::SEGMENT) == 0) {

                                    $array = explode(',', $matches[2]);
                                    $segment = new HLSSegment($array[0], $array[1]);

                                    $hlsObj->addSegment($segment);
                                    $lastContent = $segment;
                                }

                            } else {
                                /* Not a comment, not a tag either. Should be a playlist or segment path. */
                                if($lastContent != null){
                                    if($lastContent instanceof HLSStream){

                                        $lastContent->setUri($line);
                                        $playlist = $this->parseUriWithParent($line, $hlsObj);
                                        $lastContent->setPlaylist($playlist);
                                        
                                    } else if($lastContent instanceof HLSSegment) {
                                        
                                        $lastContent->setUri($line);
                                        if($lastProgramDateTime != null){
                                            $lastContent->setProgramDateTime($lastProgramDateTime);
                                            $lastProgramDateTime = null;
                                        }
                                        if($lastKey != null) {
                                            $lastContent->setKey($lastKey);
                                            $lastKey = null;
                                        }

                                    }
                                    $lastContent = null;
                                }
                            }
                        }

                    }

                    return $hlsObj;
                } else {
                    echo "Not an m3u8 file\n";
                }
                
            }
        } else {
            echo "content is null\n";
        }

        return null;
    }

}

?>