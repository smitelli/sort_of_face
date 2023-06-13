<?php

  /**
   * YouTube Subtitle Pump Class. Given a video ID, requests all text lines
   * from the "automatic" subtitle track(s), and iterates over them. This is a
   * fork of YouTubeAutocapPump (which stopped working a long time ago) and
   * directly copies some of the internal line cleanup methods used by that
   * older implementation.
   * Uses an external yt-dlp program to do the dirty work of scraping YouTube.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class YouTubeSubtitlePump extends AbstractPump {
    const SUB_LANGUAGE = 'en';
    const SUB_FORMAT = 'ttml';

    protected $itemNoun = 'subtitle lines';

    /**
     * Constructor function. Automatically scrapes the video page for the given
     * video ID and extracts the subtitle data associated with it.
     * @access public
     * @param string $yt_dlp_path Full path to a YouTube downloader binary
     * @param string $videoId The 11-character video ID to fetch
     */
    public function __construct($yt_dlp_path, $videoId) {
      // Set up to fetch a YT URL's subs into a temp file
      $url = "https://www.youtube.com/watch?v={$videoId}";
      $subs = tempnam(sys_get_temp_dir(), 'sort_of_face.');
      ConsoleLogger::writeLn("Loading YT URL {$url} into {$subs}");

      // Defer to yt-dlp to scrape a YT URL into a subtitle file on disk
      $cmd = "{$yt_dlp_path} --abort-on-error --skip-download --write-auto-subs " .
        "--sub-langs " . $this::SUB_LANGUAGE . " --sub-format " . $this::SUB_FORMAT . " " .
        "-o {$subs} {$url}";
      $output = NULL;
      $result_code = NULL;
      exec($cmd, $output, $result_code);

      if ($result_code !== 0) {
        $out_str = implode("\n", $output);
        ConsoleLogger::writeLn("Unexpected response from [$cmd]:\n{$out_str}");
        throw new PumpException("yt-dlp failed with result code {$result_code}.");
      }

      // Load the disk file contents into a variable, then remove that file
      $sub_file = $subs . '.' . $this::SUB_LANGUAGE . '.' . $this::SUB_FORMAT;
      $data = file_get_contents($sub_file);
      unlink($sub_file);

      if (!$data) {
        throw new PumpException("yt-dlp succeeded, but {$sub_file} is empty.");
      }

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
      // Split all the lines up
      $lines = preg_split('/\s*<[^>]+>\s*/', $data, NULL, PREG_SPLIT_NO_EMPTY);

      // Save the items and randomize their order
      $this->items = $lines;
      $this->shuffleItems();
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
      // TODO I'm not sure if either of these patterns still occur naturally
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
