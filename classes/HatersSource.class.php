<?php

  /**
   * This file provides "Haters gonna hate"-style source text.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class HatersSource {
    private $dictionary_file;

    /**
     * Constructor function. Parses a config array for a 'dictionary_file' key.
     * @access public
     * @param array $config The configuration array
     */
    public function __construct($config) {
      $this->dictionary_file = $config['dictionary_file'];
    }

    /**
     * Picks random words out of the system dictionary file and builds an "xers
     * gonna x" sentence around them.
     * TODO: This is probably inefficient as hell.
     * @access public
     * @return string A piece of gibberish
     */
    public function getLine() {
      $dictionary = file_get_contents($this->dictionary_file);

      $matches = array();
      preg_match_all('/(^.+)er$/m', $dictionary, $matches);
      shuffle($matches[1]);

      $base_word = array_pop($matches[1]);
      return "{$base_word}ers gonna {$base_word}";
    }
  }

?>
