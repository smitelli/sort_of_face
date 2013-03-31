<?php

  /**
   * Fake Cyrillic Generator Class. Converts several ASCII letters into visually
   * similar (but linguistically meaningless) Cyrillic letters.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class FakeCyrillicGenerator {
    private static $translations = array(
      // These must all be in UTF-8!
      'A' => "\xD0\x94",
      'B' => array("\xD0\xAC", "\xD0\xAA", "\xD0\x91"),
      'E' => array("\xD0\x97", "\xD0\xAD"),
      'K' => "\xD0\x9A",
      'N' => array("\xD0\x98", "\xD0\x99"),
      'O' => "\xD0\xA4",
      'R' => "\xD0\xAF",
      'U' => "\xD0\xA6",
      'W' => array("\xD0\xA8", "\xD0\xA9"),
      'X' => "\xD0\x96",
      'Y' => array("\xD0\xA3", "\xD0\xA7")
    );

    /**
     * Converts a string of ASCII text into fake Cyrillic. The entire input
     * string will be transformed into uppercase, partially out of necessity and
     * partially because it is hilarious. PLEASE NOTE that strings which already
     * contain multi-byte characters will likely be mangled beyond recognition!
     * @access public
     * @param string $text The source string
     * @return string The same input string, uppercase, in fake Cyrillic
     */
    public static function convert($text) {
      // Uppercase conversion
      $text = strtoupper($text);

      // Convert each letter in the source string
      $output = '';
      for ($i = 0; $i < strlen($text); $i++) {
        $output .= self::getTranslatedChar($text[$i]);
      }

      return $output;
    }

    /**
     * Looks up a source character in the translation table and returns the
     * translated character. If multiple possible translations exist, a random
     * one will be returned each time. If a source character does not exist in
     * the table, it will not be modified in the output.
     * @access private
     * @param string $char The input character
     * @return string The translated character (if one exists) or the original
     */
    private static function getTranslatedChar($char) {
      // Look up this character's translation(s)
      $result = isset(self::$translations[$char]) ? self::$translations[$char] : NULL;

      if (is_string($result)) {
        // Translation is a string -- only one possible match
        return $result;

      } else if (is_array($result)) {
        // Translation is an array -- multiple possibilities, pick a random one
        return $result[array_rand($result)];
      }

      // This character wasn't in the table -- return the original
      return $char;
    }
  }

?>
