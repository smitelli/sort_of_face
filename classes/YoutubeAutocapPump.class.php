<?php

  class YouTubeAutocapPump extends AbstractPump {
    protected $itemNoun = 'autocap lines';

    public function __construct($videoId) {
      // Request a list of items
      $data = $this->loadData("http://www.youtube.com/watch?v={$videoId}");
      $this->parseData($data);
    }

    public function nextItem() {
      // Wrap the parent's nextItem() so we can automatically do lineCleanup()
      $line = parent::nextItem();
      return self::lineCleanup($line);
    }

    protected function parseData($data) {
      // Extract the autocap URL from the provided page URL, if one is present
      $tmp = array();
      preg_match('/\'TTS_URL\': "(.+?)"/', $data, $tmp);
      if (!isset($tmp[1]) || count($tmp[1]) < 1) {
        throw new PumpException("Could not locate an autocap URL.");
      }
      $autocapUrl = self::jsStringUnescape($tmp[1]) . '&kind=asr&lang=en';

      // Make another request to fetch the autocap XML from the URL found above
      $autocapData = $this->loadData($autocapUrl);
      $this->parseAutocap($autocapData);
    }

    private function parseAutocap($data) {
      // Split all the lines up
      $lines = preg_split('/<[^>]+>/', $data, NULL, PREG_SPLIT_NO_EMPTY);

      // Save the items and randomize their order
      $this->items = $lines;
      $this->shuffleItems();
    }

    private static function jsStringUnescape($str) {
      // Unescape forward slashes
      $str = str_replace('\/', '/', $str);

      // Find escaped \uXXXX sequences
      $str = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match) {
        $char = '';
        if (isset($match[1])) {
          // Convert each one into a UTF-8 character
          $char = mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }
        return $char;
      }, $str);
      return $str;
    }

    private static function lineCleanup($str) {
      $str = self::htmlToText($str);  //de-HTML-ify the line
      $str = str_replace('_', '.', $str);  //fix "u_s_a_" to be "u.s.a."
      return $str;
    }

    private static function htmlToText($str) {
      $str = html_entity_decode($str);  //decode entities
      $str = preg_replace('/&#x([0-9a-f]+);/ei', 'chr(hexdec("\\1"))', $str);  //decode &#xXXXX;
      $str = preg_replace('/&#([0-9]+);/e', 'chr("\\1")', $str);  //decode &#XXX;
      $str = preg_replace('/[\n\r\s\t]+/', ' ', $str);  //newlines/space runs become one space
      return trim($str);  //remove stray leading/trailing space
    }
  }

?>