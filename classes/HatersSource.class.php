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
     * @access public
     * @return string A piece of gibberish
     */
    public function getLine() {
      // Read in the dictionary file
      ConsoleLogger::writeLn("Using dictionary file " . $this->dictionary_file);
      $dictionary = file_get_contents($this->dictionary_file);

      // Search the dictionary for all words that end in "er"
      $matches = array();
      preg_match_all('/^(.+)er$/m', $dictionary, $matches);

      if (isset($matches[1]) && count($matches[1]) > 0) {
        // We found at least one word that can work. Save the captured portion,
        // that is, the front part of the word excluding the "er"
        $candidates = $matches[1];
      } else {
        // No suitable matches
        throw new SourceException("Could not find any candidates.");
      }

      // Pick a word at random and use it in a sentence
      $base_word = $candidates[array_rand($candidates)];
      return "{$base_word}ers gonna {$base_word}";
    }
  }

?>
