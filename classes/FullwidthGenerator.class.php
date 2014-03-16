<?php

  /**
   * Unicode Fullwidth Generator Class. Converts most ASCII characters into
   * their Unicode fullwidth counterparts.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class FullwidthGenerator {
    /**
     * Converts a string of ASCII text into Unicode fullwidth. PLEASE NOTE that
     * strings which already contain multi-byte characters will likely be
     * mangled beyond recognition!
     * @access public
     * @param string $text The source string
     * @return string The same input string, uppercase, in Unicode fullwidth
     */
    public static function convert($text) {
      // Convert each letter in the source string
      $output = '';
      for ($i = 0; $i < strlen($text); $i++) {
        $output .= self::getTranslatedChar($text[$i]);
      }

      return $output;
    }

    /**
     * Transforms one character into Unicode fullwidth
     * @access private
     * @param string $char The input character
     * @return string The translated character (if one exists) or the original
     */
    private static function getTranslatedChar($char) {
      $ord = ord($char);

      if ($ord === 32) {
        // Space is a special case
        return "\xE3\x80\x80";

      } else if ($ord > 32 && $ord < 96) {
        // Most of these characters are in the FF00-FF3F range
        return "\xEF\xBC" . chr($ord + 96);

      } else if ($ord >= 96 && $ord < 127) {
        // The rest of these characters are in the FF40-FF5E range
        return "\xEF\xBD" . chr($ord + 32);
      }

      // Fall through for unfamiliar characters
      return $char;
    }
  }

?>
