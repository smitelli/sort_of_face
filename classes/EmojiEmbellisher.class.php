<?php

  /**
   * Emoji Embellisher Class. Appends, prepends, or intersperses various emoji
   * characters at random, depending on the desired message flavor.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class EmojiEmbellisher {
    // These must all be in UTF-8!
    private static $halloween_set = array(
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

    private static $clap = "\xF0\x9F\x91\x8F";  //CLAPPING HANDS SIGN
    private static $clap_colors = array(
      '',  //absence of color modifier is a valid option
      "\xF0\x9F\x8F\xBB",  //EMOJI MODIFIER FITZPATRICK TYPE-1-2
      "\xF0\x9F\x8F\xBC",  //EMOJI MODIFIER FITZPATRICK TYPE-3
      "\xF0\x9F\x8F\xBD",  //EMOJI MODIFIER FITZPATRICK TYPE-4
      "\xF0\x9F\x8F\xBE",  //EMOJI MODIFIER FITZPATRICK TYPE-5
      "\xF0\x9F\x8F\xBF"   //EMOJI MODIFIER FITZPATRICK TYPE-6
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

    /**
     * Insert an emoji clap into every space in the input string and return it.
     * @access public
     * @param string $text The source string
     * @return string The same input string, with claps added
     */
    public static function convertClaps($text) {
      // Half the time, capitalize the whole string
      if (rand(0, 100) < 50) {
        $text = strtoupper($text);
      }

      // Replace each space with a clap emoji with a random color modifier
      $color = self::$clap_colors[array_rand(self::$clap_colors)];
      $new_space = ' ' . self::$clap . $color . ' ';
      return preg_replace('/\s+/', $new_space, $text);
    }
  }

?>
