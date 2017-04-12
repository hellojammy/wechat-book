<?php

/**
 *
 * create at 16/09/20
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */
class Wechatools{
    public static function buildHref($txt, $link, $blank = true){
        if($link == "")
            return $txt;

        return sprintf("<a href=\"%s\" %s >%s</a>", $link, ($blank ? "target=\"_blank\"" : ""), $txt);
    }

    public static function maxLen($allText, $text){
        $len = strlen($text);
        $total_bytes = strlen($allText);
        $total_bytes += $len;

        return $total_bytes < WECHAT_MAX_RESPONSE_LEN ? false : true;
    }
}