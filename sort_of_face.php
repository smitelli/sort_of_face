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
    ConsoleLogger::writeLn("ERROR: The file config.ini is missing or malformed.");
    die();
  }

  // Set up the timezone
  date_default_timezone_set($config['timing']['timezone']);

  // Only run if the time is within `run_grace` minutes of `run_interval` past the hour
  $offset = intval(date('i')) % $config['timing']['run_interval'];
  if ($offset > $config['timing']['run_grace']) {
    ConsoleLogger::writeLn("Not time to run.");
    die();
  }

  if ($config['dictionary_source']['procon_probability'] >= rand(1, 100)) {
    // Possibly send a pro/con message instead
    $source = new DictionarySource($config['dictionary_source'], DictionarySource::MODE_PROCON);
  } else if ($config['dictionary_source']['haters_probability'] >= rand(1, 100)) {
    // Possibly send a "xers gonna x" message instead
    $source = new DictionarySource($config['dictionary_source'], DictionarySource::MODE_HATERS);
  } else if ($config['dictionary_source']['wreckedem_probability'] >= rand(1, 100)) {
    // Possibly send a "wrecked 'em" message instead
    $source = new DictionarySource($config['dictionary_source'], DictionarySource::MODE_WRECKEDEM);
  } else {
    // In all other cases, fall back on Twitter+YouTube
    $source = new TwitterYoutubeSource($config['twitter_youtube_source']);
  }

  try {
    // Read one line from our source -- that's all we should ever require
    $line = $source->getLine();
  } catch (SourceException $e) {
    // Error getting a source line
    ConsoleLogger::writeLn("SourceException: " . $e->getMessage());
    die();
  }

  if ($config['twitter']['claps_probability'] >= rand(1, 100)) {
    // Possibly add claps into the text
    $line = EmojiEmbellisher::convertClaps($line);

  } else if ($config['twitter']['fullwidth_probability'] >= rand(1, 100)) {
    // Possibly convert this line into upside-down text
    $line = FullwidthGenerator::convert($line);

  } else if ($config['twitter']['upside_down_probability'] >= rand(1, 100)) {
    // Possibly convert this line into upside-down text
    $line = UpsideDownTextGenerator::convert($line);

  } else if ($config['twitter']['cyrillic_probability'] >= rand(1, 100)) {
    // Possibly translate this line into fake Cyrillic
    $line = FakeCyrillicGenerator::convert($line);
  }

  if (date('n') == 10) {
    // Definitely do the Halloween thing if it's October
    $line = EmojiEmbellisher::convertHalloween($line);
  }

  // Make sure the line doesn't start with an SMS command sequence
  $line = TwitterWrapper::smsCommandEscape($line);

  try {
    // Send a tweet
    ConsoleLogger::writeLn("Posting [$line] to Twitter");
    $twitter = new TwitterWrapper($config['twitter']);
    $twitter->sendTweet($line);

  } catch (TwitterException $e) {
    // Error sending the tweet (TODO: Should it retry?)
    ConsoleLogger::writeLn("TwitterException: " . $e->getMessage());
    die();
  }

  // It's over!
  ConsoleLogger::writeLn("Done.");

?>
