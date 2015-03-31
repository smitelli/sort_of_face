<?php

  /**
   * A lil' console logger.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class ConsoleLogger {
    public static function write($message, $with_datestamp = TRUE) {
      if ($with_datestamp) {
        echo date('r') . ": {$message}";
      } else {
        echo $message;
      }
    }

    public static function writeLn($message, $with_datestamp = TRUE) {
      self::write("{$message}\n", $with_datestamp);
    }
  }

?>
