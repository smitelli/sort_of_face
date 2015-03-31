<?php

  /**
   * A lil' console logger. Pretty much one step above peppering `echo` all over
   * the place.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class ConsoleLogger {
    /**
     * Writes a line of log material to standard output, keeping the cursor
     * at the end of the line. A timestamp can optionally be prepended.
     * @access public
     * @param string $message The message to write
     * @param boolean $with_datestamp If TRUE, prepends the current datestamp
     */
    public static function write($message, $with_datestamp = TRUE) {
      if ($with_datestamp) {
        echo date('r') . ": {$message}";
      } else {
        echo $message;
      }
    }

    /**
     * Writes a line of log material to standard output, followed by one single
     * newline character. A timestamp can optionally be prepended.
     * @access public
     * @param string $message The message to write
     * @param boolean $with_datestamp If TRUE, prepends the current datestamp
     */
    public static function writeLn($message, $with_datestamp = TRUE) {
      self::write("{$message}\n", $with_datestamp);
    }
  }

?>
