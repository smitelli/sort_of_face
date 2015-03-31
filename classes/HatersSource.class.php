<?php

  /**
   * This file provides "Haters gonna hate"-style source text.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class HatersSource {
    /**
     * Constructor function. Parses a config array for TODO
     * @access public
     * @param array $config The configuration array
     */
    public function __construct($config) {

    }

    /**
     * Picks random words out of the system dictionary file and builds an "xers
     * gonna x" sentence around them.
     * @access public
     * @return string A piece of gibberish
     */
    public function getLine() {
      return "Oh, hello.";
    }
  }

?>
