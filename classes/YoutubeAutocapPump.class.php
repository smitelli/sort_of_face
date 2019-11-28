<?php

  /**
   * YouTube Autocap Pump Class. Given a video ID, requests all text lines
   * from the "autocap" closed-captioning track, and iterates over them.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class YouTubeAutocapPump extends AbstractPump {
    protected $itemNoun = 'autocap lines';

    /**
     * Constructor function. Automatically loads the video page for the given
     * video ID and parses the autocap data associated with it.
     * @access public
     * @param string $videoId The 11-character video ID to fetch
     */
    public function __construct($videoId) {
      // Request a list of items
      $data = $this->loadData("https://www.youtube.com/watch?v={$videoId}");
      $this->parseData($data);
    }

    /**
     * Removes one item from the front of this collection and returns it. This
     * method is overridden so lineCleanup() can auto-run on each item.
     * @access public
     * @return mixed An item from the front of the collection
     */
    public function nextItem() {
      // Wrap the parent's nextItem() so we can automatically do lineCleanup()
      $line = parent::nextItem();
      return self::lineCleanup($line);
    }

    /**
     * Scrapes the response data for the autocap URL, then makes a request for
     * that file. When the autocap data comes in, it is parsed separately.
     * @access protected
     * @param string $data The response data from the server
     */
    protected function parseData($data) {
      // Extract the autocap URL from the provided page URL, if one is present
      $tmp = array();
      preg_match('/\'TTS_URL\': "(.+?)"/', $data, $tmp);
      if (!isset($tmp[1]) || strlen($tmp[1]) < 1) {
        throw new PumpException("Could not locate an autocap URL.");
      }
      $autocapUrl = self::jsStringUnescape($tmp[1]) . '&kind=asr&lang=en';

      // Make another request to fetch the autocap XML from the URL found above
      $autocapData = $this->loadData($autocapUrl);
      $this->parseAutocap($autocapData);
    }

    /**
     * Scrapes the autocap data for a list of text lines, shuffles it, and
     * stores it in the collection.
     * @access private
     * @param string $data The response data from the server
     */
    private function parseAutocap($data) {
      // Split all the lines up
      $lines = preg_split('/<[^>]+>/', $data, NULL, PREG_SPLIT_NO_EMPTY);

      // Save the items and randomize their order
      $this->items = $lines;
      $this->shuffleItems();
    }

    /**
     * Parse a JavaScript string in object literal notation into a plain,
     * unescaped UTF-8 string.
     * @access private
     * @param string $str The raw JS string
     * @return string The same string in plain UTF-8
     */
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

    /**
     * Cleans up the peculiarities from YouTube's autocap format.
     * @access private
     * @param string $str The raw autocap string
     * @return string The same string, cleaned up for public presentation
     */
    private static function lineCleanup($str) {
      $str = self::htmlToText($str);  //de-HTML-ify the line

      // Fix "u_s_a_" to be "USA", and "r_ kelly" to be "R. kelly"
      $str = preg_replace_callback('/(._)+/', function($matches) {
        $dot = (substr_count($matches[0], '_') > 1) ? '' : '.';
        return str_replace('_', $dot, strtoupper($matches[0]));
      }, $str);

      return $str;
    }

    /**
     * Removes several layers of HTML escapes: named entities, hex entities,
     * decimal entities, and stray newlines and whitespace.
     * @param string $str The raw HTML string
     * @return string The same string, with HTML-specific entities removed
     */
    private static function htmlToText($str) {
      $str = html_entity_decode($str);  //decode entities
      $str = strip_tags($str);  //remove any HTML tags that may be present
      $str = preg_replace_callback('/&#x([0-9a-f]+);/i', 'self::hexToChar', $str);  //decode &#xXXXX;
      $str = preg_replace_callback('/&#([0-9]+);/', 'self::decToChar', $str);  //decode &#XXX;
      $str = preg_replace('/[\n\r\s\t]+/', ' ', $str);  //newlines/space runs become one space
      return trim($str);  //remove stray leading/trailing space
    }

    /**
     * Converts a hexadecimal string into the character it corresponds to.
     * @param array $matches The array of matches from preg_replace_callback()
     * @return string The new character
     */
    private static function hexToChar($matches) {
      return chr(hexdec($matches[0]));
    }

    /**
     * Converts an integer string into the character it corresponds to.
     * @param array $matches The array of matches from preg_replace_callback()
     * @return string The new character
     */
    private static function decToChar($matches) {
      return chr($matches[0]);
    }
  }

?>
