<?php

  /**
   * Twitter Trend Pump Class. Queries the Twitter API for a list of trending
   * topics, and iterates over the returned terms.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class TwitterTrendPump extends AbstractPump {
    protected $itemNoun = 'trends';

    /**
     * Constructor function. Automatically loads the list of trends from
     * Twitter and parses them.
     * @access public
     * @todo Uses v1 API endpoint; will eventually break
     */
    public function __construct() {
      // TODO: This was a quick and dirty hack.
      global $config;

      // Request a list of items
      $twitter = new TwitterWrapper($config['twitter']);
      $data = $twitter->getData('https://api.twitter.com/1.1/trends/place.json', array('id' => 1, 'exclude' => 'hashtags'));
      $this->parseData($data);
    }


    /**
     * Parses the returned data, shuffles it, and stores it in the collection.
     * @access protected
     * @param string $data The response data from the server
     */
    protected function parseData($data) {
      // Parse the loaded data, make sure it contains what we need
      if (!isset($data->trends)) {
        throw new PumpException("Could not parse the loaded {$this->itemNoun}.");
      }

      // Result is grouped by hours; flatten it to a single array and remove URL
      // encoding.
      $tmp = array();
      foreach ($data[0]->trends as $item) {
        $tmp[] = urldecode($item->query);
      }

      // Save the items and randomize their order
      $this->items = $tmp;
      $this->shuffleItems();
    }
  }

?>
