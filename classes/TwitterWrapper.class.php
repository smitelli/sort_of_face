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
    }

    /**
     * Sends one tweet to the account referred to by the OAuth credentials
     * stored in this object's instance.
     * @access public
     * @param string $status The text content of the tweet to send
     */
    public function sendTweet($status) {
      // Make the request and read the API response
      $twitter = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_token_secret);
      $response = $twitter->post('http://api.twitter.com/1/statuses/update.json', array('status' => $status));

      if (empty($response)) {
        // Response was blank
        throw new TwitterException("Could not contact Twitter API.");
      }

      if (isset($result->error)) {
        // Response had an error indication
        throw new TwitterException("Twitter says: {$result->error}");

      } else if (!isset($result->created_at)) {
        // Response lacked any indication that the tweet was created
        throw new TwitterException("Could not create tweet.");
      }
    }
  }

?>