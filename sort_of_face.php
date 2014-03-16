<?php

  /**
   * sort_of_face: A Twitter Gibberish Bot
   *
   * @author Scott Smitelli
   * @package sort_of_face
   */

  // Support autoloading classes as they are needed
  define('APP_DIR', realpath(dirname(__FILE__)));
  spl_autoload_register(function($class_name) {
    $class_file = sprintf(APP_DIR . "/classes/{$class_name}.class.php");
    if (is_readable($class_file)) {
      // File exists; load it
      require_once $class_file;
    }
  });

  // Load and parse the configuration file
  $config = @parse_ini_file(APP_DIR . '/config.ini', TRUE);
  if (empty($config)) {
    die("The file config.ini is missing or malformed.\n\n");
  }

  // Set up the timezone
  date_default_timezone_set($config['timing']['timezone']);

  // Only run if the time is within `run_grace` minutes of `run_interval` past the hour
  echo "========== Currently " . date('r') . " ==========";
  $offset = intval(date('i')) % $config['timing']['run_interval'];
  if ($offset > $config['timing']['run_grace']) {
    die("\nNot time to run.\n\n");
  }

  // Load the current trends from Twitter
  try {
    $trends = new TwitterTrendPump();
  } catch (PumpException $e) {
    // If we don't have trends, there's no way the script can work
    die($e->getMessage() . "\n\n");
  }

  // Loop over every trend...
  $attempts = 0;
  while ($trends->hasItems()) {
    if (++$attempts > $config['timing']['max_attempts']) {
      // Runaway script protection
      die("\nToo many failed attempts.\n\n");
    }

    try {
      // Use the current trend as a YT search term
      $searchTerm = $trends->nextItem();
      echo "\nConsidering Twitter trend [$searchTerm]... ";
      $videos = new YoutubeSearchPump("{$searchTerm},cc");  //',cc' means we want closed-captioning

      // Loop over every video ID from the search results page
      while ($videos->hasItems()) {
        // Use this video ID to load the autocap data from YT
        $videoId = $videos->nextItem();
        echo "\n  Trying YouTube video ID [$videoId]... ";
        $captions = new YoutubeAutocapPump($videoId);

        // Loop over every line from the autocap file
        while ($captions->hasItems()) {
          $line = $captions->nextItem();

          // Make sure the line doesn't start with an SMS command sequence
          $line = TwitterWrapper::smsCommandEscape($line);

          // Trim the line to a random length
          $length = rand($config['twitter']['min_length'], $config['twitter']['max_length']);
          list($line) = explode("\n", wordwrap($line, $length));

          if ($config['twitter']['fullwidth_probability'] >= rand(1, 100)) {
            // Possibly convert this line into upside-down text
            $line = FullwidthGenerator::convert($line);

          } else if ($config['twitter']['upside_down_probability'] >= rand(1, 100)) {
            // Possibly convert this line into upside-down text
            $line = UpsideDownTextGenerator::convert($line);

          } else if ($config['twitter']['cyrillic_probability'] >= rand(1, 100)) {
            // Possibly translate this line into fake Cyrillic
            $line = FakeCyrillicGenerator::convert($line);
          }

          try {
            // Send a tweet
            echo "\n    Sending [$line]... ";
            $twitter = new TwitterWrapper($config['twitter']);
            $twitter->sendTweet($line);

          } catch (TwitterException $e) {
            // Error sending the tweet
            echo $e->getMessage();
            continue 3;  //jump to next trend
          }

          break 3;  //bail out of all loops
        }
      }

    } catch (PumpException $e) {
      // Error from one of the pumps
      echo $e->getMessage();
      continue;  //jump to next trend
    }
  }

  // It worked!
  echo "\nDone.\n\n";

?>
