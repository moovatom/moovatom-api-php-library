<html>
<head>
  <meta charset="utf-8">
  <title>Moovatom | PHP API Demo Page</title>
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/main.css">
</head>
<body>

<?php
  
  include '../moovatom.php';

  // these attributes are used to control your videos on Moovatom's servers
  $vattrs = array(
    'uuid'         => '', // Uniquely identifies a video on Moovatom. Used by fetch_details(), fetch_status(), edit_player() and cancel()
    'username'     => '', // Your Moovatom username. Used by all action methods
    'userkey'      => '', // Your Moovatom API key. Used by all action methods
    'content_type' => '', // The mime-type of the video file. Defaults to 'video'
    'title'        => '', // The title of a new video encoding. Used by encode()
    'blurb'        => '', // The description of a new video encoding. Used by encode()
    'sourcefile'   => '', // The publicly accessible location of the video file used in a new video encoding. Used by encode()
    'callbackurl'  => '', // The URL on your server that records the details of a new video encoding once it's finished. Used by encode()
    'format'       => ''  // The format (JSON or XML) of the response from Moovatom. Defaults to JSON, used by all action methods
  );

  // these attributes are used to control the player displayed on the front end of your site
  // all values below are the defaults implemented by Moovatom
  $pattrs = array(
    'height'              => '480',
    'width'               => '720',
    'auto_play'           => 'False',
    'sharing_enabled'     => 'True',
    'show_hold_image'     => 'True',
    'watermark'           => 'http://www.example.com/path/to/watermark.png',
    'watermark_url'       => 'http://www.example.com',
    'show_watermark'      => 'True',
    'watermark_opacity'   => '0.8',
    'background_color'    => '#000000',
    'duration_color'      => '#FFFFFF',
    'buffer_color'        => '#6C9CBC',
    'volume_color'        => '#000000',
    'volume_slider_color' => '#000000',
    'button_color'        => '#889AA4',
    'button_over_color'   => '#92B2BD',
    'time_color'          => '#01DAFF'
  );

  $moovatom = new Moovatom( $vattrs, $pattrs );

  // to get the details about a video already on Moovatom and display it in the HTML below:
  // $moovatom->fetch_details();

  // to check the status of a video:
  // $moovatom->fetch_status();

  // to start a new encoding on Moovatom:
  // $moovatom->encode();

  // to cancel a new encoding job that hasn't finished yet:
  // $moovatom->cancel();

  // to edit the attributes of your video player:
  // $moovatom->edit_player();

?>

<header class="wrap">
  <h1>Moovatom PHP API Demo Page</h1>
</header>

<div class="wrap clearfix">
  <div id="main_left">
    <h2>Moovatom Object Attributes:</h2>
    <p><?php echo $moovatom; ?></p>
  </div>
  <div id="main_right">
    <h2>Moovatom Player Attributes:</h2>
    <p><?php echo $moovatom->player; ?></p>
  </div>
  <div id="main_bottom">
    <h2>Video Content:</h2>
    <?php echo $moovatom->response->embed_code ?>
  </div>
</div>

<footer class="wrap">
  <h1>Moovatom PHP API Demo Page</h1>
</footer>

</body>
</html>