<?php

  abstract class AbstractPump {
    protected $itemNoun = 'items';
    protected $items = array();

    abstract protected function parseData($data);

    public function nextItem() {
      // Make sure there are still items available
      if (!$this->hasItems()) {
        throw new PumpException("No more {$this->itemNoun} remain in the set.");
      }

      // Remove an item and return it
      return array_shift($this->items);
    }

    public function hasItems() {
      // Does this object hold at least 1 item?
      return (count($this->items) > 0);
    }

    protected function loadData($url) {
      // Make an HTTP request for the necessary data
      $response = @file_get_contents($url);
      if (empty($response)) {
        throw new PumpException("Could not load {$this->itemNoun}.");
      }

      return $response;
    }

    protected function shuffleItems() {
      // Remove duplicates; randomize the remaining items
      $this->items = array_unique($this->items);
      shuffle($this->items);
    }
  }

?>