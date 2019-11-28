<?php

  /**
   * Abstract Pump Class. The purpose of this class is to serve as a base for
   * other classes to extend. At its core, provides methods to load data and
   * manipulate/iterate over result sets.
   * @abstract
   * @author Scott Smitelli
   * @package sort_of_face
   */

  abstract class AbstractPump {
    protected $itemNoun = 'items';
    protected $items = array();

    /**
     * Abstract function declaration for a method which parses the response
     * data and loads it into the collection.
     * @abstract
     * @access protected
     * @param string $data The response data from the server
     */
    abstract protected function parseData($data);

    /**
     * Removes one item from the front of this collection and returns it.
     * @access public
     * @return mixed An item from the front of the collection
     */
    public function nextItem() {
      // Make sure there are still items available
      if (!$this->hasItems()) {
        throw new PumpException("No more {$this->itemNoun} remain in the set.");
      }

      // Remove an item and return it
      return array_shift($this->items);
    }

    /**
     * Returns TRUE if there is at least one element in the collection.
     * @access public
     * @return boolean TRUE if any elements remain, FALSE otherwise.
     */
    public function hasItems() {
      // Does this object hold at least 1 item?
      return (count($this->items) > 0);
    }

    /**
     * Performs an HTTP GET request and returns the response as a string.
     * @access protected
     * @param string $url The URL to load
     * @return string The response body from the server
     */
    protected function loadData($url) {
      // Make an HTTP request for the necessary data
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);
      // Force IPv4 to avoid frequent "solve the CAPTCHA" responses from YT
      curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_URL, $url);
      $response = curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($http_code < 200 || $http_code > 299 || empty($response)) {
        throw new PumpException("Could not load {$this->itemNoun}.");
      }

      return $response;
    }

    /**
     * Shuffles the current collection.
     * @access protected
     */
    protected function shuffleItems() {
      // Remove duplicates; randomize the remaining items
      $this->items = array_unique($this->items);
      shuffle($this->items);
    }
  }

?>
