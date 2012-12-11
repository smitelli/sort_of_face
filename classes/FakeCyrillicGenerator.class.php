<?php

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
  
    public static function convert($text) {
      $text = strtoupper($text);
    
      $output = '';
      for ($i = 0; $i < strlen($text); $i++) {
        $output .= self::getTranslatedChar($text[$i]);
      }
      
      return $output;
    }
    
    private static function getTranslatedChar($char) {
      $result = isset(self::$translations[$char]) ? self::$translations[$char] : NULL;
      
      if (is_string($result)) {
        return $result;
        
      } else if (is_array($result)) {
        return $result[array_rand($result)];
      }
      
      return $char;
    }
  }

?>