<?php

namespace pyngon\m3u8;

require_once 'HLSTag.php';
require_once 'HLS.php';

class HLSGenerator {
    private $outputFolder;
    public function __construct($outputFolder) {
        $this->outputFolder = $outputFolder;
    }

    public function generatePlaylist($hls, $filename){
        echo 'generating '.$filename."\n";

        $filePathInfo = pathinfo($filename);
        $completePath = $this->outputFolder.'/'.$filePathInfo['dirname'].'/';

        if(!file_exists($completePath)){
            $success = mkdir($completePath, 0777, true);
            if(!$success){
                return false;
            }
        }

        $completeFileName = $this->outputFolder.'/'.$filename;
        $file = fopen($completeFileName, 'w');

        fwrite($file, HLSTag::START."\n");
        
        if($hls->getVersion() != null){
            fwrite($file, HLSTag::VERSION.":".$hls->getVersion()."\n");
        }
        if($hls->getPlaylistType() != null){
            fwrite($file, HLSTag::PLAYLIST_TYPE.":".$hls->getPlaylistType()."\n");
        }
        if($hls->getTargetDuration() != null){
            fwrite($file, HLSTag::TARGET_DURATION.":".$hls->getTargetDuration()."\n");
        }
        if($hls->getMediaSequence() != null){
            fwrite($file, HLSTag::MEDIA_SEQUENCE.":".$hls->getMediaSequence()."\n");
        }

        if($hls->getMedias() != null){
            foreach ($hls->getMedias() as $groupIDs) {
                foreach($groupIDs as $media){
                    $writeValue = HLSTag::MEDIA.':';

                    $keys = array_keys($media->getAttrs());
                    $numKeys = count($keys);
                    for($i=0; $i < $numKeys; $i++) {
                        $value = $media->getAttr($keys[$i]);

                        if(is_bool($value)){
                            if($value == true){
                                $value = 'YES';
                            } else {
                                $value = 'NO';
                            }
                        } else if($media->isStringTypeAttr($keys[$i])) {
                            $value = '"'.$value.'"';
                        }

                        if($i > 0){
                            $writeValue .= ',';
                        }
                        $writeValue .= "$keys[$i]=$value";
                    }
                    $writeValue .= "\n";
                    fwrite($file, $writeValue);

                    $mediaUri = $media->getAttr(HLSMedia::ATTR_URI);
                    $this->generateChildPlaylist($mediaUri, $media->getPlaylist());
                }
            }
        }

        if($hls->getStreams() != null){
            foreach($hls->getStreams() as $stream) {
                $writeValue = HLSTag::STREAM.':';
                $keys = array_keys($stream->getAttrs());
                $numKeys = count($keys);
                for($i=0; $i < $numKeys; $i++) {
                    $value = $stream->getAttr($keys[$i]);

                    if($stream->isStringTypeAttr($keys[$i])){
                        $value = '"'.$value.'"';
                    }

                    if($i > 0){
                        $writeValue .= ',';
                    }
                    $writeValue .= "$keys[$i]=$value";
                }

                $writeValue .= "\n";
                fwrite($file, $writeValue);
                fwrite($file, $stream->getUri()."\n");

                $this->generateChildPlaylist($stream->getUri(), $stream->getPlaylist());
            }
        } else if($hls->getSegments() != null) {
            foreach($hls->getSegments() as $segment) {
                fwrite($file, HLSTag::SEGMENT.':'.$segment->getDuration().','.$segment->getTitle()."\n");
                if($segment->getKey() != null){
                    fwrite($file, HLSTag::KEY.':'.$segment->getKey()."\n");
                }
                if($segment->getProgramDateTime() != null){
                    fwrite($file, HLSTag::PROGRAM_DATE_TIME.':'.$segment->getProgramDateTime()."\n");
                }
                fwrite($file, $segment->getUri()."\n");
            }
        }

        if($hls->isEnded()){
            fwrite($file, HLSTag::END."\n");
        }

        fclose($file);
    }

    private function isRelativePath($uri){
        $parsedUrl = parse_url($rawUri);
        if($parsedUrl == false){
            echo "parse url fail, $rawUri\n";
            throw new Exception('Not a path.');
        } else if(!isset($parsedUrl['scheme'])){
            return true;
        }
        return false;
    }

    private function generateChildPlaylist($uri, $playlist) {
        $filename = $uri;
        if(!$this->isRelativePath($uri)){
            $streamUriInfo = pathinfo($uri);
            $filename = $streamUriInfo['filename'];
        }
        $generator = new HLSGenerator($this->outputFolder);
        $generator->generatePlaylist($playlist,$filename);
    }

}

?>