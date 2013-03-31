<?php

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
  
    public static function convert($text) {
      $text = strrev($text);
    
      $output = '';
      for ($i = 0; $i < strlen($text); $i++) {
        $output .= self::getTranslatedChar($text[$i]);
      }
      
      return $output;
    }
    
    private static function getTranslatedChar($char) {
      $result = isset(self::$translations[$char]) ? self::$translations[$char] : NULL;
      
      if ($result) {
        return $result;
      }
      
      return $char;
    }
  }

?>