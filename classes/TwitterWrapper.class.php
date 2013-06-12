<?php

  /**
   * Twitter Wrapper Class. Performs a dead-simple interface to send out a
   * tweet given a set of OAuth keys.
   * @author Scott Smitelli
   * @package sort_of_face
   */

  // Requires Abraham Williams' Twitter OAuth library
  require_once APP_DIR . '/libraries/twitteroauth/twitteroauth.php';

  class TwitterWrapper {
    private $consumer_key;
    private $consumer_secret;
    private $access_token;
    private $access_token_secret;

    /**
     * Constructor function. Parses a config array for 'consumer_key',
     * 'consumer_secret', 'access_token', and 'access_token_secret' keys.
     * @access public
     * @param array $config The configuration array
     */
    public function __construct($config) {
      // Store the OAuth keys from the user's config
      $this->consumer_key        = $config['consumer_key'];
      $this->consumer_secret     = $config['consumer_secret'];
      $this->access_token        = $config['access_token'];
      $this->access_token_secret = $config['access_token_secret'];

      // Set up the OAuth library to talk to Twitter
      $this->twitter = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_token_secret);
    }

    /**
     * Sends one tweet to the account referred to by the OAuth credentials
     * stored in this object's instance.
     * @access public
     * @param string $status The text content of the tweet to send
     */
    public function sendTweet($status) {
      // Make the request and read the API response
      $response = $this->twitter->post('http://api.twitter.com/1.1/statuses/update.json', array('status' => $status));

      if (empty($response)) {
        // Response was blank
        throw new TwitterException("Could not contact Twitter API.");
      }

      if (isset($response->error)) {
        // Response had an error indication
        throw new TwitterException("Twitter says: {$response->error}");

      } else if (!isset($response->created_at)) {
        // Response lacked any indication that the tweet was created
        throw new TwitterException("Could not create tweet.");
      }
    }

    public function getData($url, $parameters = array()) {
      // Make the request and read the API response
      $response = $this->twitter->get($url, $parameters);

      return $response;
    }
    
    /**
     * Escapes certain string sequences that can be interpreted by Twitter as
     * being commands to perform undesired actions.
     * @access public
     * @param string $text The input text to sanitize
     * @return string The same text, with special SMS command sequences escaped
     */
    public static function smsCommandEscape($text) {
      // https://support.twitter.com/articles/14020-twitter-for-sms-basic-features
      if (preg_match('/^on$/i', $text)  //ON
       || preg_match('/^on\s+\w+$/i', $text)  //ON [name]
       || preg_match('/^off$/i', $text)  //OFF
       || preg_match('/^off\s+\w+$/i', $text)  //OFF [name]
       || preg_match('/^follow\s+\w+$/i', $text)  //FOLLOW [name]
       || preg_match('/^f\s+\w+$/i', $text)  //F [name]
       || preg_match('/^unfollow\s+\w+$/i', $text)  //UNFOLLOW [name]
       || preg_match('/^leave\s+\w+$/i', $text)  //LEAVE [name]
       || preg_match('/^l\s+\w+$/i', $text)  //L [name]
       || preg_match('/^block\s+\w+$/i', $text)  //BLOCK [name]
       || preg_match('/^blk\s+\w+$/i', $text)  //BLK [name]
       || preg_match('/^unblock\s+\w+$/i', $text)  //UNBLOCK [name]
       || preg_match('/^unblk\s+\w+$/i', $text)  //UNBLK [name]
       || preg_match('/^report\s+\w+$/i', $text)  //REPORT [name]
       || preg_match('/^rep\s+\w+$/i', $text)  //REP [name]
       || preg_match('/^stop$/i', $text)  //STOP
       || preg_match('/^quit$/i', $text)  //QUIT
       || preg_match('/^end$/i', $text)  //END
       || preg_match('/^cancel$/i', $text)  //CANCEL
       || preg_match('/^unsubscribe$/i', $text)  //UNSUBSCRIBE
       || preg_match('/^arret$/i', $text)  //ARRET
       || preg_match('/^d\s+/i', $text)  //D [...]
       || preg_match('/^m\s+/i', $text)  //M [...]
       || preg_match('/^retweet\s+\w+$/i', $text)  //RETWEET [name]
       || preg_match('/^rt\s+\w+$/i', $text)  //RT [name]
       || preg_match('/^set\s+/i', $text)  //SET [...]
       || preg_match('/^whois\s+\w+$/i', $text)  //WHOIS [name]
       || preg_match('/^w\s+\w+$/i', $text)  //W [name]
       || preg_match('/^get\s+\w+$/i', $text)  //GET [name]
       || preg_match('/^g\s+\w+$/i', $text)  //G [name]
       || preg_match('/^fav\s+\w+$/i', $text)  //FAV [name]
       || preg_match('/^fave\s+\w+$/i', $text)  //FAVE [name]
       || preg_match('/^favorite\s+\w+$/i', $text)  //FAVORITE [name]
       || preg_match('/^favourite\s+\w+$/i', $text)  //FAVOURITE [name]
       || preg_match('/^\*\w+$/i', $text)  //*[name]
       || preg_match('/^stats\s+\w+$/i', $text)  //STATS [name]
       || preg_match('/^suggest$/i', $text)  //SUGGEST
       || preg_match('/^sug$/i', $text)  //SUG
       || preg_match('/^s$/i', $text)  //S
       || preg_match('/^wtf$/i', $text)  //WTF
       || preg_match('/^help$/i', $text)  //HELP
       || preg_match('/^info$/i', $text)  //INFO
       || preg_match('/^aide$/i', $text)  //AIDE
      ) {
        $text = ". {$text}";
      }
      return $text;
    }
  }

?>