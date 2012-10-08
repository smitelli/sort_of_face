<?php

  class TwitterTrendPump extends AbstractPump {
    protected $itemNoun = 'trends';

    public function __construct() {
      // Request a list of items. This is going to break hard at the APIv1 sunset
      $data = $this->loadData('http://api.twitter.com/1/trends/daily.json');
      $this->parseData($data);
    }

    protected function parseData($data) {
      // Parse the loaded data, make sure it contains what we need
      $dataObj = json_decode($data);
      if (!isset($dataObj->trends)) {
        throw new PumpException("Could not parse the loaded {$this->itemNoun}.");
      }

      // Result is grouped by hours; flatten it to a single array
      $tmp = array();
      foreach ($dataObj->trends as $hour) {
        foreach ($hour as $item) {
          $tmp[] = $item->query;
        }
      }

      // Save the items and randomize their order
      $this->items = $tmp;
      $this->shuffleItems();
    }
  }

?>