![Moovatom Logo](http://www.moovatom.com/static/img/site_images/moovatom_logo.png)

# Overview

This library provides access to the Moovatom online video processing and streaming service. It provides all the necessary attributes and methods for:

1. Starting a new video encoding process
2. Getting the status of a current encoding
3. Getting the details of a completed encoding
4. Canceling an encoding job
5. Deleting an encoding job
6. Editing the attributes of your video player
7. Searching for videos you've already encoded

The entire library is contained in a single file. So installation is as simple as adding the file to your app and then requiring it wherever it's needed.

The library consists of two classes: `Moovatom_Player` and `Moovatom`. The first is responsible for storing all the attributes related to the video player that will be embedded on the front end of your site. The second defines one constant and five action methods that interact with Moovatom's RESTful API. The constant `API_URL` defines the URL to which the JSON or XML requests must be POST'd. There are 13 instance variables used to communicate with Moovatom's servers:

1. `uuid`
2. `username`
3. `userkey`
4. `content_type`
5. `search_term`
6. `title`
7. `blurb`
8. `sourcefile`
9. `callbackurl`
10. `format`
11. `player`
12. `action`
13. `response`

`response` will always contain the last response received from the Moovatom servers and `action` will be set by each of the action methods explained below. `player` is a instance of the `Moovatom_Player` class that provides access to the player attributes for your video. The remaining ten variables correspond to the attributes of the video you want to control as well as your specific Moovatom account credentials. These attributes can be set in a number of ways depending upon the needs of your specific application.

Instantiating a new (empty) object to communicate with the MoovAtom API is as simple as:

```php
require '/path/to/moovatom.php';
$me = new Moovatom();
```

The object created in the code above isn't very useful though. A Moovatom object created without any arguments will, however, receive a few default values. `content_type` will be initialized with a value of 'video', `format` will be set to 'json' and `player` will be initialized as an empty `Moovatom_Player` instance when no arguments are provided. The remaining ten variables need to be set with the credentials for your Moovatom account and the specifics about the video you wish to control. Aside from creating an empty object, as we did above, I've tried to include as much flexibility as I could when it comes to creating a new Moovatom object. You can pass one or two associative arrays to the constructor containing the values you wish to be set for either player or video attributes. The first array will be used to setup video attributes and your Moovatom account credentials. The second is used to initialize the player attributes.

```php
You can pass literal arrays:
$me = new Moovatom( array( 'uuid' => 'j9i8h7g6f5e4d3c2b1a', 'username' => 'USERNAME' ) );

But it may be more readable to create the arrays first and then pass them:
$vattrs = array( 'uuid' => 'j9i8h7g6f5e4d3c2b1a', 'username' => 'USERNAME' );
$pattrs = array( 'width' => '720', 'height' => '480' );
$me = new Moovatom( $vattrs, $pattrs );
```

The library has been designed to be highly customizable. You are free to create a single instance and reuse it throughout your app, changing the attributes each time you need to work with a different video, or multiple instances representing individual videos if that's what your application requires, it's completely up to you.

# Action Methods

The `Moovatom` class has seven methods that have been designed to interact directly with the RESTful API implemented by Moovatom's servers:

1. `fetch_details()` will return details about an encoded video
2. `fetch_status()` will return the status of a video (e.g. - whether or not encoding has completed)
3. `encode()` will start a new encoding job
4. `cancel()` will cancel an __unfinished__ encoding job
5. `delete()` will delete a __finished__ encoding job
6. `edit_player()` changes the attributes of your video's online player
7. `media_search()` returns videos based on the search terms you've provided

Each of these methods are almost identical. They all accept an associative array argument similar to the constructor. The main difference is that the action methods will accept only one array. This allows you to easily reuse a `Moovatom` object to request information about different videos. The seven action methods are able to be used and reused because they share a method that handles the heavy lifting when building and sending the request to Moovatom: `send_request()`. The `send_request()` method takes every variable (including player attributes) and creates a single array containing the key/value attributes for your video. It then uses the `format` and `action` variables to build and POST the appropriate request to the Moovatom servers. If the response is successful it will parse the JSON or XML into a Standard Object and store it in the `response` variable.

For more specific information about the Moovatom API please see the [documentation](http://moovatom.com/support/v2/api.html).

## Details

Getting the details of a video you've uploaded to your Moovatom account is as simple as creating a `Moovatom` object and populating it with your credentials and the specifics of the video you'd like to access:

```php
$me = new Moovatom( array(
  'uuid'     => 'j9i8h7g6f5e4d3c2b1a',
  'username' => 'USERNAME',
  'userkey'  => 'a1b2c3d4e5f6g7h8i9j'
  )
);

$me->fetch_details();
```

A details request will POST the __uuid__, __username__ and __userkey__ variables from your `Moovatom` object. If successful `response` will contain a Standard Object ready to be queried and used.

*Successful fetch_details() JSON Response:*

```
{
    "uuid": "UUID",
    "media_type": "video",
    "embed_code": "EMBED CODE SMART SWITCHING FOR AUTOMATIC MOBILE AND WEB SUPPORT.",
    "iframe_target": "http://www.moovatom.com/media/embed/ID",
    "original_download": "http://www.moovatom.com/media/download/orig/UUID",
    "versions": [
        {
            "name": "mobile",
            "type": "video/mp4",
            "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
            "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
            "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
            "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
            "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
            "download": "http://www.moovatom.com/PATH_TO_FILE"
        },
        {
            "name": "mobile_large",
            "type": "video/mp4",
            "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
            "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
            "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
            "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
            "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
            "download": "http://www.moovatom.com/PATH_TO_FILE"
        },
        {
            "name": "small",
            "type": "video/mp4",
            "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
            "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
            "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
            "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
            "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
            "download": "http://www.moovatom.com/PATH_TO_FILE"
        },
        {
            "name": "medium",
            "type": "video/mp4",
            "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
            "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
            "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
            "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
            "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
            "download": "http://www.moovatom.com/PATH_TO_FILE"
        },
        {
            "name": "large",
            "type": "video/mp4",
            "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
            "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
            "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
            "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
            "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
            "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
            "download": "http://www.moovatom.com/PATH_TO_FILE"
        }
    ]
}
```

## Status

The `fetch_status()` method allows you to query a video that has begun encoding to check its progress.

```php
$me = new Moovatom( array(
  'uuid'     => 'j9i8h7g6f5e4d3c2b1a',
  'username' => 'USERNAME',
  'userkey'  => 'a1b2c3d4e5f6g7h8i9j'
  )
);

$me->get_status();

if ( ! $me->response->processing ) {
  $me->get_details();
}
```

A status request will POST the __uuid__, __username__ and __userkey__ variables from your `Moovatom` object. The `response` variable will contain either a success or error response:

*Status Success Response:*

```
{
    "uuid": "UUID",
    "processing": true,
    "percent_complete": 75,
    "error": 
}
```

*Status Error Response:*

```
{
    "uuid": "UUID",
    "processing": false,
    "percent_complete": 100,
    "error": "This was not a recognized format."
}
```

## Encode

You can start a new encoding on the Moovatom servers through the `encode()` method.

```php
$me = new Moovatom( array(
  'username'    => 'USERNAME',
  'userkey'     => 'a1b2c3d4e5f6g7h8i9j',
  'username'    => 'USERNAME',
  'title'       => 'Dolphin Training',
  'blurb'       => 'How to train your dolphin like a pro.',
  'sourcefile'  => 'http://example.com/dolphin.mp4',
  'callbackurl' => 'http://example.com/moovatom_callback',
  )
);

$me->encode();
```

An encode request will POST the __username__, __userkey__, __content type__, __title__, __blurb__, __sourcefile__ and __callbackurl__ variables from your `Moovatom` object. The body of the Moovatom response will contain the uuid assigned by Moovatom's servers to this new video as well as a message stating whether or not your job was started successfully:

*Encode Started Response:*

```
{
    "uuid": "UUID",
    "message": "Your job was started successfully."
}
```

After a successful response the `uuid` variable of your `Moovatom` object will be set to the uuid assigned by Moovatom. The encode action implemented on Moovatom's servers differs slightly from the other six actions. Once the encoding is complete, Moovatom's servers will send a response to the callback URL you set in the `callbackurl` variable. Your app should define a controller or action of some sort that will process these callbacks to save/update the video's details in your database. The body of the callback sent by Moovatom looks exactly like the response from a `fetch_details()` request.

Additionally, the video you are uploading to Moovatom must be in a __publicly accessibly location__. Moovatom will attempt to transfer that video from the url you define in the `sourcefile` variable. The ability to upload a video directly is planned for a future version of the API and this library.

For more specific information about the Moovatom API please see the [documentation](http://moovatom.com/support/v2/api.html).

## Cancel

If you decide, for whatever reason, that you no longer need or want a specific video on Moovatom you can cancel its encoding anytime __before it finishes__ using the `cancel()` method. A cancel request will POST the __uuid__, __username__ and __userkey__ instance variables from your `Moovatom` object. The body of the Moovatom response will contain a message telling you whether or not you successfully canceled your video:

```php
$me = new Moovatom( array(
  'uuid'     => 'j9i8h7g6f5e4d3c2b1a',
  'username' => 'USERNAME',
  'userkey'  => 'a1b2c3d4e5f6g7h8i9j'
  )
);

$me->get_status();

if ( $me->response->processing ) {
  $me->cancel();
}
```

*Example cancel request response:*

```
{
    "uuid": "UUID",
    "message": "This job was successfully cancelled."
}
```

## Delete

If you decide, for whatever reason, that you no longer need or want a specific video on Moovatom you can delete its encoding anytime __after it finishes__ using the `delete()` method. A delete request will POST the __uuid__, __username__ and __userkey__ instance variables from your `Moovatom` object. The body of the Moovatom response will contain a message telling you whether or not you've successfully deleted your video:

```php
$me = new Moovatom( array(
  'uuid'     => 'j9i8h7g6f5e4d3c2b1a',
  'username' => 'USERNAME',
  'userkey'  => 'a1b2c3d4e5f6g7h8i9j'
  )
);

$me->get_status();

if ( ! $me->response->processing ) {
  $me->delete();
}
```

*Example delete request response:*

```
{
    "uuid": "UUID",
    "message": "Your media was successfully deleted."
}
```

## Edit Player

The true power of Moovatom's streaming service becomes apparent only after you've placed a video on your site through their iframe embed code. But sometimes you need a little more control over how your video plays and what it looks like. This is where the `edit_player()` action method comes in. There are 17 attributes you can control through the API (shown here with their default values):

```
height: 480
width: 720
auto_play: False
sharing_enabled: True
show_hold_image: True
watermark: http://www.example.com/path/to/watermark.png
watermark_url: http://www.example.com
show_watermark: True
watermark_opacity: 0.8
background_color: #000000
duration_color: #FFFFFF
buffer_color: #6C9CBC
volume_color: #000000
volume_slider_color: #000000
button_color: #889AA4
button_over_color: #92B2BD
time_color: #01DAFF
```

The `edit_player()` method accepts the same associative array syntax as the first four action methods:

```php
$attrs = array( 'width' => '800', 'height' => '500', 'time_color' => '#12EBGG' );

$me->edit_player( $attrs );
```

Since the `player` variable is just a `Moovatom_player` object you can always add an attribute by calling into it using the '->' operator:

```php
$me = new Moovatom( array(
  'uuid' => 'j9i8h7g6f5e4d3c2b1a',
  'username' => 'USERNAME',
  'userkey'  => 'a1b2c3d4e5f6g7h8i9j'
  )
);

$me->player->height = '480';
$me->player->width = '720';
$me->player->auto_play = false;
$me->player->sharing_enabled = true;
$me->player->watermark_opacity = '0.8';
$me->player->background_color = '#000000';
$me->player->duration_color = '#FFFFFF';
$me->player->volume_color = '#000000';
$me->player->button_color = '#889AA4';
$me->player->time_color = '#01DAFF';

$me->edit_player();
```

## Media Search

The `media_search()` action method allows you to query the videos you've uploaded to and encoded on Moovatom's servers using search terms entered into the `search_term` variable. A media_search request will POST the __username__, __userkey__ and __search_term__ instance variables from your MoovEngine object. The body of the Moovatom response will be similar to a details request:

```php
$me = new Moovatom( array(
  'username' => 'USERNAME',
  'userkey'  => 'a1b2c3d4e5f6g7h8i9j',
  'search_term' => 'dolphin'
  )
);

$me->media_search();
```

*Example media search request response:*

```
{
    "result_count": "1",
    "user": "USERNAME",
    "results": [
        {
            "uuid": "UUID",
            "title": "Dolphin Training",
            "summary": "How to train your dolphin like a pro.",
            "duration": "45.347",
            "media_type": "video",
            "embed_code": "EMBED CODE IFRAME FOR SMART SWITCHING",
            "iframe_target": "http://www.moovatom.com/media/embed/ID",
            "original_download": "http://www.moovatom.com/media/download/orig/UUID",
            "versions": [
                {
                    "name": "sample",
                    "type": "video/mp4",
                    "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
                    "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
                    "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
                    "download": "http://www.moovatom.com/PATH_TO_FILE"
                },
                {
                    "name": "mobile",
                    "type": "video/mp4",
                    "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
                    "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
                    "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
                    "download": "http://www.moovatom.com/PATH_TO_FILE"
                },
                {
                    "name": "mobile_large",
                    "type": "video/mp4",
                    "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
                    "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
                    "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
                    "download": "http://www.moovatom.com/PATH_TO_FILE"
                },
                {
                    "name": "small",
                    "type": "video/mp4",
                    "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
                    "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
                    "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
                    "download": "http://www.moovatom.com/PATH_TO_FILE"
                },
                {
                    "name": "medium",
                    "type": "video/mp4",
                    "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
                    "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
                    "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
                    "download": "http://www.moovatom.com/PATH_TO_FILE"
                },
                {
                    "name": "large",
                    "type": "video/mp4",
                    "holdframe_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "thumbnail_download": "http://www.moovatom.com/PATH_TO_FILE",
                    "holdframe_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "thumbnail_serve": "http://static.moovatom.com/PATH_TO_FILE",
                    "rtmp_stream": "rtmp://media.moovatom.com/PATH_TO_FILE",
                    "http_stream": "http://media.moovatom.com:1935/PATH_TO_FILE",
                    "rtsp_stream": "rtsp://media.moovatom.com:1935/PATH_TO_FILE",
                    "download": "http://www.moovatom.com/PATH_TO_FILE"
                }
            ]
        }
    ]
}
```

For more specific information about the Moovatom API please see the [documentation](http://moovatom.com/support/v2/api.html).

# Demo Site

I've also included a basic demo site (based on the [HTML5Boilerplate](http://html5boilerplate.com) project) that you can use to test out the API before adding it to a production site. The `index.php` file has all of the associative arrays and markup you'll need to quickly create a Moovatom object and communicate with the API. Just fill in your specific credentials and video attributes.

# Moovatom

[MoovAtom](http://moovatom.com/) is an online video conversion and streaming service. The service insulates your videos from competitor's ads or links to inappropriate content. It offers customizable players that support hot linkable watermarks in addition to stream paths to your own player so you can control your videos, and your brand, on your own terms. Streaming is supported to all Apple mobile devices as well as most Android and Blackberry platforms. A unique QR Code is generated for each video for use in advertisements, allowing your viewers to simply "scan and play" your content. Advanced analytics and metrics provide valuable incite into how your viewers are watching your videos. The MoovAtom servers support both FTP access and direct uploads so huge file sizes are easy to handle. MoovAtom makes it easy to protect your copyrights, the streaming servers provide unparalleled protection over other services using progressive downloads to a user's browser cache.

For more specific information about the Moovatom service please see their [home page](http://moovatom.com/).
