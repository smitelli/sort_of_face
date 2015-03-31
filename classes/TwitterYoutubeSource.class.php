<?php

  /**
   * This file provides source data from Twitter trends and YouTube closed-
   * captioning data. It can be considered the "classic" sort_of_face source.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  class TwitterYoutubeSource {
    private $max_attempts;
    private $min_length;
    private $max_length;

    /**
     * Constructor function. Parses a config array for 'max_attempts',
     * 'min_length', and 'max_length' keys.
     * @access public
     * @param array $config The configuration array
     */
    public function __construct($config) {
      $this->max_attempts = $config['max_attempts'];
      $this->min_length   = $config['min_length'];
      $this->max_length   = $config['max_length'];
    }

    /**
     * Asks Twitter for a list of current trends. Picks one, then uses it as a
     * search term on YouTube to find matching videos. Picks a video, and loads
     * the autocap lines. Picks a line, and returns it. If any of that fails,
     * a SourceException is thrown.
     * @access public
     * @return string A piece of gibberish
     */
    public function getLine() {
      // Load the current trends from Twitter
      try {
        $trends = new TwitterTrendPump();
      } catch (PumpException $e) {
        // If we don't have trends, there's no way the script can work
        throw new SourceException($e->getMessage());
      }

      // Loop over every trend...
      $attempts = 0;
      while ($trends->hasItems()) {
        if (++$attempts > $this->max_attempts) {
          // Runaway script protection
          throw new SourceException("Too many failed attempts.");
        }

        try {
          // Use the current trend as a YT search term
          $searchTerm = $trends->nextItem();
          ConsoleLogger::writeLn("Considering Twitter trend [$searchTerm]...");
          $videos = new YoutubeSearchPump("{$searchTerm},cc");  //',cc' means we want closed-captioning

          // Loop over every video ID from the search results page
          while ($videos->hasItems()) {
            // Use this video ID to load the autocap data from YT
            $videoId = $videos->nextItem();
            ConsoleLogger::writeLn("  Trying YouTube video ID [$videoId]...");
            $captions = new YoutubeAutocapPump($videoId);

            // Loop over every line from the autocap file
            while ($captions->hasItems()) {
              $line = $captions->nextItem();

              if ($line) {
                // Trim the line to a random length
                $length = rand($this->min_length, $this->max_length);
                list($line) = explode("\n", wordwrap($line, $length));

                return $line;
              }
            }
          }
        } catch (PumpException $e) {
          // Error from one of the pumps
          ConsoleLogger::writeLn($e->getMessage());
          continue;  //jump to next trend
        }
      }

      // Shouldn't get here
      throw new SourceException("All available pumps have run dry.");
    }
  }

?>
