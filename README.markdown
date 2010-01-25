
# GoCard PHP API #

## Description ##

A simple PHP interface to access the Queensland Transport GoCard website.

Supports:

* Login
* Get Balance
* Get Activity History
* Logout

## Usage ##
    <?php

    require 'gocard.php';
    $gocard = new GoCard('card_number', 'password);
    if($gocard->login()) {
        echo $gocard->get_balance();
        $gocard->logout();
    }

It is important to note that _you will need a file, writable by the web-server to store the cURL cookies_. This file is defined in the Weightbot class as $\_cookies\_file (default: 'cookies').
