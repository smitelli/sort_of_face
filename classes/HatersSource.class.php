<?php

  /**
   * This file provides "Haters gonna hate"-style source text. Along a similar
   * theme, it can also provide "Rectum? It damn near killed him!" lines.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class HatersSource {
    private $dictionary_file;

    /**
     * Constructor function. Parses a config array for a 'dictionary_file' key.
     * @access public
     * @param array $config The configuration array
     * @param string $mode The operation mode, either 'haters' or 'wreckedem'
     */
    public function __construct($config, $mode) {
      $this->dictionary_file = $config['dictionary_file'];
      $this->mode = $mode;
    }

    /**
     * Dispatches the correct internal method based on the operation mode.
     * @access public
     * @return string A piece of gibberish
     */
    public function getLine() {
      return ($this->mode == 'wreckedem') ?
        $this->getWreckedEm() : $this->getHaters();
    }

    /**
     * Picks random words out of the system dictionary file and builds an "xers
     * gonna x" sentence around them.
     * @access private
     * @return string A piece of gibberish
     */
    private function getHaters() {
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

    /**
     * Picks random words out of the system dictionary file and builds a
     * "Rectum? It damn near killed him!" sentence around them. This works quite
     * poorly without a true rhyming dictionary, and that's sort of the point.
     * @access private
     * @return string A piece of gibberish
     */
    private function getWreckedEm() {
      // Read in the dictionary file
      ConsoleLogger::writeLn("Using dictionary file " . $this->dictionary_file);
      $dictionary = file_get_contents($this->dictionary_file);

      $choices = array(
        array('gender' => 'f', 'template' => "$? It damn near killed her!"),
        array('gender' => 'f', 'template' => "$? It practically destroyed her!"),
        array('gender' => 'f', 'template' => "$? I just met her!"),
        array('gender' => 'f', 'template' => "$? I barely know her!"),
        array('gender' => 'f', 'template' => "$? I don't even know her!"),
        array('gender' => 'm', 'template' => "$? It damn near killed him!"),
        array('gender' => 'm', 'template' => "$? It practically destroyed him!"),
        array('gender' => 'm', 'template' => "$? I just met him!"),
        array('gender' => 'm', 'template' => "$? I barely know him!"),
        array('gender' => 'o', 'template' => "$? I just met you!"),
        array('gender' => 'o', 'template' => "$? I barely know you!"),
        array('gender' => 'o', 'template' => "$? I don't even know you!"),
      );
      $choice = $choices[array_rand($choices)];

      // Search the dictionary for all words that end in a usable suffix
      $matches = array();
      switch ($choice['gender']) {
        case 'm':
          preg_match_all('/^(.+(em|im|um))$/m', $dictionary, $matches);
          break;
        case 'f':
          preg_match_all('/^(.+(er|or|re))$/m', $dictionary, $matches);
          break;
        default:
          preg_match_all('/^(.+(oo|ou|ue))$/m', $dictionary, $matches);
          break;
      }

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
      return str_replace('$', ucfirst($base_word), $choice['template']);
    }
  }

?>
