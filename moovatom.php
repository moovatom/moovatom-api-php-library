<?php

/**
 * Moovatom PHP Library
 *
 * The MoovEngine API provides the RESTful interface for encoding, canceling
 * and querying, your videos on MoovAtom's servers. This library defines the
 * methods and functionality necessary for your app to communicate with that
 * interface.
 *
 * See README file for installation details and specific usage information.
 *
 * @author Dominic Giglio <mailto:humanshell@gmail.com>
 * @copyright Copyright (c) 2012 Dominic Giglio - All Rights Reserved
 * @license MIT
 */


/**
 * The Moovatom Player Utility Class
 */
class Moovatom_Player {
  
  // this array holds all the player attributes
  public $data = array();

  // the constructor
  public function __construct( $pattrs = array() ) {
    foreach ( $pattrs as $key => $value )
      $this->__set( $key, $value );
  }

  // Magic __setter
  public function __set( $name, $value ) {
    $this->data[$name] = $value;
  }

  // Magic __getter
  public function __get( $name ) {
    if ( array_key_exists( $name, $this->data ) ) {
      return $this->data[$name];
    }
  }

  // Magic __toString() - for prettier debug output in a browser
  public function __toString() {
    return "Height:              $this->height<br />
            Width:               $this->width<br />
            Auto Play:           $this->auto_play<br />
            Sharing Enabled:     $this->sharing_enabled<br />
            Show Hold Image:     $this->show_hold_image<br />
            Watermark:           $this->watermark<br />
            Watermark URL:       $this->watermark_url<br />
            Show Watermark:      $this->show_watermark<br />
            Watermark Opacity:   $this->watermark_opacity<br />
            Background Color:    $this->background_color<br />
            Duration Color:      $this->duration_color<br />
            Buffer Color:        $this->buffer_color<br />
            Volume Color:        $this->volume_color<br />
            Volume Slider Color: $this->volume_slider_color<br />
            Button Color:        $this->button_color<br />
            Button Over Color:   $this->button_over_color<br />
            Time Color:          $this->time_color<br />";
  }

} // end Moovatom_Player class


/**
 * The Main Moovatom Class
 */
class Moovatom {

  // Moovatom's base api url
  const API_URL = 'https://www.moovatom.com/api/v2';

  // this array holds all the video attributes
  private $data = array();
  
  /**
   * The constructor populates the class' instance variables to hold all the
   * specifics about the video you're accessing or starting to encode, as well
   * as a player instance that holds all the player specific attributes.
   *
   * There are two ways to instantiate a new Moovatom object:
   *
   * 1. Create a blank object and set each variable using '->' notation
   * 2. Supply video and/or player attributes in associative arrays
   *
   * See the README for specific examples
   *
   * $this->player is an instance of the Moovatom_Player class that holds all
   * the attributes for your video player. $this->action gets set in each of
   * the request methods below to correctly correspond with the actions you're
   * asking MoovAtom to perform. $this->format allows you to get xml or json in
   * your responses, it's set to json by default. $this->content_type will
   * default to 'video'.
   */

  public function __construct( $vattrs = array(), $pattrs = array() ) {

    // parse passed in video attributes
    if ( $vattrs )
      foreach ( $vattrs as $key => $value )
        $this->__set( $key, $value );

    // set the default content_type if none given
    if ( ! $this->content_type )
      $this->content_type = 'video';

    // set the default format if none given
    if ( ! $this->format )
      $this->format = 'json';
    
    // instantiate a player object
    $this->player = new Moovatom_Player( $pattrs );

  }

  /**
   * the magic __setter
   */

  public function __set( $name, $value ) {
    $this->data[$name] = $value;
  }

  /**
   * the magic __getter
   */

  public function __get( $name ) {
    if ( array_key_exists( $name, $this->data ) ) {
      return $this->data[$name];
    }
  }

  /**
   * The fetch_details() method is responsible for communicating the details
   * about a video that has completed encoding on Moovatom's servers. You can
   * pass an array of attributes to update the internal state of the Moovatom
   * object prior to requesting the details of an existing video. This method
   * sets the variable $this->action to 'detail' for you. It uses the
   * send_request() method to assign the response from the Moovatom servers to
   * the $this->response instance variable.
   *
   * See README for specific examples
   */

  public function fetch_details( $attrs = array() ) {
    $this->action = 'detail';

    // parse passed in attributes
    if ( $attrs )
      foreach ( $attrs as $key => $value )
        $this->__set( $key, $value );

    $this->send_request();
  }

  /**
   * The fetch_status() method is almost identical to the fetch_details() method.
   * It also accepts the same type/combination of arguments and sets the
   * $this->action instance variable to 'status' for you. The main difference is
   * that you will receive either a success or error status response from
   * Moovatom's servers corresponding to the video of the uuid provided.
   *
   * See README for specific examples
   */
  
  public function fetch_status( $attrs = array() ) {
    $this->action = 'status';

    // parse passed in attributes
    if ( $attrs )
      foreach ( $attrs as $key => $value )
        $this->__set( $key, $value );

    $this->send_request();
  }

  /**
   * The encode() method allows you to start a new encoding on Moovatom's
   * servers. It is almost identical to the fetch_details() and fetch_status()
   * methods. You can pass the same type/combination of arguments and it sets
   * the $this->action instance variable to 'encode' for you. After a successful
   * response this method will set the $this->uuid instance variable to the
   * value returned from Moovatom.
   *
   * See README for specific examples
   */
  
  public function encode( $attrs = array() ) {
    $this->action = 'encode';

    // parse passed in attributes
    if ( $attrs )
      foreach ( $attrs as $key => $value )
        $this->__set( $key, $value );

    $this->send_request();

    // store the uuid of the newly encoded video
    $this->uuid = $this->response->uuid;
  }

  /**
   * The cancel() method allows you to cancel a video currently being encoded
   * by the Moovatom servers. It is almost identical to the fetch_details() and
   * fetch_status() methods. You can pass the same type/combination of arguments
   * and it sets the $this->action instance variable to 'cancel' for you.
   *
   * See README for specific examples
   */
  
  public function cancel( $attrs = array() ) {
    $this->action = 'cancel';

    // parse passed in attributes
    if ( $attrs )
      foreach ( $attrs as $key => $value )
        $this->__set( $key, $value );

    $this->send_request();
  }

  /**
   * The edit_player() method allows you to change the player attributes for
   * your videos on moovatom's servers. It accepts an array of player attributes
   * used to update the $this->player instance variable created during initialization.
   * It sets the $this->action instance variable to 'edit_player' for you.
   *
   * See README for specific examples
   */
  
  public function edit_player( $attrs = array() ) {
    $this->action = 'edit_player';

    // parse passed in attributes
    if ( $attrs )
      foreach ( $attrs as $key => $value )
        $this->__set( $key, $value );

    $this->send_request();
  }

  /**
   * The send_request() method is responsible for POSTing the values stored in
   * your object's instance variables to Moovatom. If the response was a success it
   * will be parsed according to the value of $this->format.
   */
  
  private function send_request() {

    // setup the url and data to be POST'd
    $url = self::API_URL . "/{$this->action}.{$this->format}";
    $data = http_build_query( ( $this->data + $this->player->data ) );

    // create the request
    $params = array( 'http' => array( 'method' => 'POST', 'content' => $data ) );
    $context = stream_context_create( $params );

    // parse the reponse according to the format specified
    if ( $this->format == 'json' )
      $this->response = json_decode( file_get_contents( $url, false, $context ) );
    else
      $this->response = new SimpleXMLElement( file_get_contents( $url, false, $context ) );

  }

  /**
   * Custom __toString() magic method for prettier debug output in a browser
   */
  public function __toString() {
    return "UUID:         $this->uuid<br />
            Username:     $this->username<br />
            Userkey:      $this->userkey<br />
            Content Type: $this->content_type<br />
            Title:        $this->title<br />
            Blurb:        $this->blurb<br />
            Source File:  $this->sourcefile<br />
            Callback URL: $this->callbackurl<br />
            Action:       $this->action<br />
            Format:       $this->format<br />";
  }

} // end Moovatom class

