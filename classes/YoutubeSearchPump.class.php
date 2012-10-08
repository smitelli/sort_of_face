<?php

  /**
   * YouTube Search Pump Class. Performs a search on youtube.com for a given
   * search query, scrapes all the video IDs from that page, and iterates over
   * those video IDs.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class YouTubeSearchPump extends AbstractPump {
    protected $itemNoun = 'search results';

    /**
     * Constructor function. Automatically loads search results from YouTube
     * that match the query, then parses them.
     * @access public
     * @param string $query The search term to send with the request
     */
    public function __construct($query) {
      // Request a list of items
      $q = urlencode($query);
      $data = $this->loadData("http://www.youtube.com/results?search_query={$q}&search_sort=video_date_uploaded");
      $this->parseData($data);
    }

    /**
     * Scrapes the response data for a list of video IDs, shuffles it, and
     * stores it in the collection.
     * @access protected
     * @param string $data The response data from the server
     */
    protected function parseData($data) {
      // Extract every instance of "/watch?v=XXXXXXXXXXX" from the page
      $tmp = array();
      preg_match_all('/watch\?v=([a-zA-Z0-9-_]{11})/', $data, $tmp);
      if (!isset($tmp[1]) || count($tmp[1]) < 1) {
        throw new PumpException("Could not parse the loaded {$this->itemNoun}.");
      }

      // Save the items and randomize their order
      $this->items = $tmp[1];
      $this->shuffleItems();
    }
  }

?>