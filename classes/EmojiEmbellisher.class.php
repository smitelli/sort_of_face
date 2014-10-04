<?php

  /**
   * Emoji Embellisher Class. Appends or prepends various emoji characters at
   * random, depending on the desired message flavor.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class EmojiEmbellisher {
    private static $halloween_set = array(
      // These must all be in UTF-8!
      "\xF0\x9F\x98\xB1",  //FACE SCREAMING IN FEAR
      "\xF0\x9F\x98\xB5",  //DIZZY FACE
      "\xF0\x9F\x98\xB2",  //ASTONISHED FACE
      "\xF0\x9F\x99\x80",  //WEARY CAT FACE
      "\xF0\x9F\x92\x80",  //SKULL
      "\xF0\x9F\x8C\x95",  //FULL MOON SYMBOL
      "\xF0\x9F\x8E\x83",  //JACK-O-LANTERN
      "\xF0\x9F\x91\xBB",  //GHOST
      "\xF0\x9F\x94\xAA"   //HOCHO
    );

    /**
     * Add Halloween-themed embellishments to the input string and return it.
     * @access public
     * @param string $text The source string
     * @return string The same input string, with a Halloween embellishment
     */
    public static function convertHalloween($text) {
      // Hard-coding the randomness into this method, because the algorithm
      // isn't nailed down enough to define INI config variables yet.

      // Pick a random emoji to start with
      $set_count = count(self::$halloween_set) - 1;
      $index = rand(0, $set_count);

      $emoji = '';
      while (TRUE) {
        // Add one character
        $emoji .= self::$halloween_set[$index];

        $choice = rand(0, 100);
        if ($choice < 33) {
          // Sometimes, stop looping -- we have enough emoji
          break;
        } else if ($choice > 75) {
          // Sometimes, pick a new random emoji
          $index = rand(0, $set_count);
        }
      }

      // Prepend 20% of the time, append for the rest.
      if (rand(0, 100) < 20) {
        $output = $emoji . ' ' . $text;
      } else {
        $output = $text . ' ' . $emoji;
      }

      return $output;
    }
  }

?>
