<?php

  // Requires Abraham Williams' Twitter OAuth library
  require_once APP_DIR . '/libraries/twitteroauth/twitteroauth.php';

  class TwitterWrapper {
    private $consumer_key;
    private $consumer_secret;
    private $access_token;
    private $access_token_secret;

    public function __construct($config) {
      // Store the OAuth keys from the user's config
      $this->consumer_key        = $config['consumer_key'];
      $this->consumer_secret     = $config['consumer_secret'];
      $this->access_token        = $config['access_token'];
      $this->access_token_secret = $config['access_token_secret'];
    }

    public function sendTweet($status) {
      // Make the request and read the API response
      $twitter = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->access_token, $this->access_token_secret);
      $response = $twitter->oAuthRequest('http://api.twitter.com/1/statuses/update.json', 'POST', array('status' => $status));

      if (empty($response)) {
        // Response was blank
        throw new TwitterException("Could not contact Twitter API.");
      }

      $result = json_decode($response);
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