<?php

  class YouTubeSearchPump extends AbstractPump {
    protected $itemNoun = 'search results';

    public function __construct($query) {
      // Request a list of items
      $q = urlencode($query);
      $data = $this->loadData("http://www.youtube.com/results?search_query={$q}&search_sort=video_date_uploaded");
      $this->parseData($data);
    }

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