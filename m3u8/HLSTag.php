<?php

namespace pyngon\m3u8;

class HLSTag {
    const PREFIX = '#EXT';
    const START = '#EXTM3U';

    const VERSION = '#EXT-X-VERSION';
    const PLAYLIST_TYPE = '#EXT-X-PLAYLIST-TYPE';
    const TARGET_DURATION = '#EXT-X-TARGETDURATION';
    const MEDIA_SEQUENCE = '#EXT-X-MEDIA-SEQUENCE';
    const KEY = '#EXT-X-KEY';
    const PROGRAM_DATE_TIME = '#EXT-X-PROGRAM-DATE-TIME';

    const MEDIA = '#EXT-X-MEDIA';
    const STREAM = '#EXT-X-STREAM-INF';
    const SEGMENT = '#EXTINF';

    const END = '#EXT-X-ENDLIST';
}

?>