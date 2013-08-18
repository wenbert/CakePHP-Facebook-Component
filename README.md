CakePHP-Facebook-Component
==========================

Make sure you have the facebook php sdk files (https://github.com/facebook/facebook-php-sdk)

    ./vendors/facebook
    ./vendors/facebook/base_facebook.php
    ./vendors/facebook/facebook.php
    ./vendors/facebook/fb_ca_chain_bundle.crt

USAGE
=====

Put this file in: 

    app/Controller/Component/FacebookComponent.php

Set it up like this:

    public $components = array(
        'Facebook' => array(
            'appId' => 'xxx',
            'secret' => 'xxx',
            'cookie' => true,
            'fileUpload' => 1,
            'canvas' => 1,
            'fbconnect' => 1,
            'display' => 'page',
            'scope' => 'user_about_me,email,publish_actions,publish_stream,photo_upload',
            'redirect_uri' => 'https://www.facebook.com/pages/My-Test-Page/12345?id=12345&sk=app_12345'
        )
    );

Then in you controllers, you can do this:

    debug($this->userProfile);
    debug($this->loginUrl);
    debug($this->logoutUrl);
    debug($this->hasLiked);
    debug($this->signed_request);
