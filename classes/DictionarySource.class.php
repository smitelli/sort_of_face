<?php

  /**
   * This file provides "Haters gonna hate"-style source text. Along a similar
   * theme, it can also provide "Rectum? It damn near killed him!" lines.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class DictionarySource {
    const MODE_HATERS = 'haters';
    const MODE_PROCON = 'procon';
    const MODE_WRECKEDEM = 'wreckedem';

    private $dictionary_file;

    /**
     * Constructor function. Parses a config array for a 'dictionary_file' key.
     * @access public
     * @param array $config The configuration array
     * @param string $mode The operation mode, one of the MODE_* constants
     */
    public function __construct($config, $mode) {
      $this->dictionary_file = $config['dictionary_file'];
      $this->mode = $mode;

      // Read in the dictionary file
      ConsoleLogger::writeLn("Using dictionary file " . $this->dictionary_file);
      $this->dictionary = file_get_contents($this->dictionary_file);
    }

    /**
     * Dispatches the correct internal method based on the operation mode.
     * @access public
     * @return string A piece of gibberish
     */
    public function getLine() {
      switch ($this->mode) {
        case $this::MODE_HATERS:
          return $this->getHaters();
          break;

        case $this::MODE_PROCON:
          return $this->getProCon();
          break;

        case $this::MODE_WRECKEDEM:
          return $this->getWreckedEm();
          break;
      }
    }

    /**
     * Picks a random "-er" word and and builds an "xers gonna x" sentence
     * around it.
     * @access private
     * @return string A piece of gibberish
     */
    private function getHaters() {
      // Pick a word at random and use it in a sentence
      $base_word = $this->getDictEntry('/^(.+)er$/m');
      return "{$base_word}ers gonna {$base_word}";
    }

    /**
     * Picks a random pairing of two words and builds a "The opposite of
     * progress is congress." sentence around it.
     * @access private
     * @return string A piece of gibberish
     */
    private function getProCon() {
      $i = 0;

      while (++$i < 1000) {
        $pro = $this->getDictEntry('/^(pro.+)$/im');

        $con_test = preg_replace('/^pro/i', 'con', $pro);

        try {
          $con = $this->getDictEntry('/^(' . $con_test . ')$/im');
        } catch (SourceException $e) {
          continue;
        }

        return "The opposite of $pro is $con.";
      }

      throw new SourceException("Gave up trying to find pro/con matches.");
    }

    /**
     * Picks a random word and builds a "Rectum? It damn near killed him!"
     * sentence around it. This works rather poorly without a true rhyming
     * dictionary, and that's sort of the point.
     * @access private
     * @return string A piece of gibberish
     */
    private function getWreckedEm() {
      // Pluck a random template out of the pre-determined list
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
        array('gender' => 'm', 'template' => "$? I don't even know him!"),
        array('gender' => 'o', 'template' => "$? I just met you!"),
        array('gender' => 'o', 'template' => "$? I barely know you!"),
        array('gender' => 'o', 'template' => "$? I don't even know you!"),
        array('gender' => 'o', 'template' => "$? But I don't like you!")
      );
      $choice = $choices[array_rand($choices)];

      // Pick a word at random that fits within the chosen template's gender
      $matches = array();
      switch ($choice['gender']) {
        case 'm':  //words that sound like "him"
          $base_word = $this->getDictEntry('/^(.+em|im|um)$/m');
          break;
        case 'f':  //words that sound like "her"
          $base_word = $this->getDictEntry('/^(.+er|or|re)$/m');
          break;
        default:  //ideally, words that sound like "you"
          $base_word = $this->getDictEntry('/^(.+oo|ou|ue)$/m');
          break;
      }

      // Construct the final output sentence
      return str_replace('$', ucfirst($base_word), $choice['template']);
    }

    /**
     * Picks random words out of the system dictionary file that match the
     * specified regex pattern. The pattern must contain at least one capture
     * group; the first group encountered determines which part of the word (or
     * the whole word) should be returned.
     * @access private
     * @param array $pattern The regex pattern to match and capture on
     * @return string A single dictionary word matching the pattern and group
     */
    private function getDictEntry($pattern) {
      // Search the dictionary for all words that match the pattern
      $matches = array();
      preg_match_all($pattern, $this->dictionary, $matches);

      if (isset($matches[1]) && count($matches[1]) > 0) {
        // We found at least one word that can work. Save the captured portion,
        // that is, the front part of the word excluding the "er"
        $candidates = $matches[1];
      } else {
        // No suitable matches
        throw new SourceException("Could not find any candidates.");
      }

      // Pick a random word and return it
      return $candidates[array_rand($candidates)];
    }
  }

?>
