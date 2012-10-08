<?php

  /**
   * Pump Exception Class. A generic extension of the Exception class which is
   * thrown by several of the Pump classes.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class PumpException extends Exception {
    /**
     * Constructor function. Same as the constructor for Exception, except
     * $message is now a mandatory argument.
     * @param string $message The exception message
     * @param integer $code The exception code (optional)
     * @param Exception $previous The previous Exception (optional)
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
      parent::__construct($message, $code, $previous);
    }
  }

?>