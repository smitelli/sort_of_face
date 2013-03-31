<?php

  /**
   * Upside-Down Text Generator Class. Converts most lower ASCII characters into
   * higher Unicode symbols that appear rotated 180 degrees. Reverses the entire
   * input string to yield text that is readable when flipped upside-down.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class UpsideDownTextGenerator {
    private static $translations = array(
      // These must all be in UTF-8!
      '!' => "\xC2\xA1",
      '"' => "\xE2\x80\x9E",
      '&' => "\xE2\x85\x8B",
      "'" => ',',
      '(' => ')',
      ')' => '(',
      ',' => "'",
      '.' => "\xCB\x99",
      '1' => "\xE2\x87\x82",
      '2' => "\xE1\x84\x85",
      '3' => "\xC6\x90",
      '4' => "\xE3\x84\xA3",
      '5' => "\xDE\x8E",
      '6' => '9',
      '7' => "\xE3\x84\xA5",
      '9' => '6',
      ';' => "\xD8\x9B",
      '<' => '>',
      '>' => '<',
      '?' => "\xC2\xBF",
      'A' => "\xE2\x88\x80",
      'B' => "\xF0\x90\x90\x92",
      'C' => "\xC6\x86",
      'D' => "\xE2\x97\x96",
      'E' => "\xC6\x8E",
      'F' => "\xE2\x84\xB2",
      'G' => "\xE2\x85\x81",
      'J' => "\xC5\xBF",
      'K' => "\xE2\x8B\x8A",
      'L' => "\xCB\xA5",
      'M' => 'W',
      'P' => "\xD4\x80",
      'Q' => "\xCE\x8C",
      'R' => "\xE1\xB4\x9A",
      'T' => "\xE2\x8A\xA5",
      'U' => "\xE2\x88\xA9",
      'V' => "\xCE\x9B",
      'W' => 'M',
      'Y' => "\xE2\x85\x84",
      '[' => ']',
      ']' => '[',
      '_' => "\xE2\x80\xBE",
      'a' => "\xC9\x90",
      'b' => 'q',
      'c' => "\xC9\x94",
      'd' => 'p',
      'e' => "\xC7\x9D",
      'f' => "\xC9\x9F",
      'g' => "\xC6\x83",
      'h' => "\xC9\xA5",
      'i' => "\xC4\xB1",
      'j' => "\xC9\xBE",
      'k' => "\xCA\x9E",
      'l' => "\xCA\x83",
      'm' => "\xC9\xAF",
      'n' => 'u',
      'p' => 'd',
      'q' => 'b',
      'r' => "\xC9\xB9",
      't' => "\xCA\x87",
      'u' => 'n',
      'v' => "\xCA\x8C",
      'w' => "\xCA\x8D",
      'y' => "\xCA\x8E",
      '{' => '}',
      '}' => '{'
    );

    /**
     * Converts a string of ASCII text into upside-down text. This is
     * accomplished by reversing the entire input string and then replacing each
     * character with a symbol from the lookup table. PLEASE NOTE that strings
     * which already contain multi-byte characters will likely be mangled beyond
     * recognition! Additionally, the lookup table does NOT currently allow the
     * process to be reversed by convert()ing the text a second time.
     * @access public
     * @param string $text The source string
     * @return string The same input string, reversed, upside-down
     */  
    public static function convert($text) {
      // Reverse the input
      $text = strrev($text);
    
      // Convert each letter in the source string
      $output = '';
      for ($i = 0; $i < strlen($text); $i++) {
        $output .= self::getTranslatedChar($text[$i]);
      }
      
      return $output;
    }

    /**
     * Looks up a source character in the translation table and returns the
     * translated character. If a source character does not exist in the table,
     * it will not be modified in the output.
     * @access private
     * @param string $char The input character
     * @return string The translated character (if one exists) or the original
     */    
    private static function getTranslatedChar($char) {
      // Look up this character's translation
      $result = isset(self::$translations[$char]) ? self::$translations[$char] : NULL;
      
      if ($result) {
        // Found one; return it
        return $result;
      }
      
      // This character wasn't in the table -- return the original
      return $char;
    }
  }

?>
