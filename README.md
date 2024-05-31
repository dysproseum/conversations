Conversations is an open-source messaging app for desktop and mobile

* Intended to be a replacement for Google Hangouts
* Use existing Google accounts
* Accessible from mobile and desktop

### A lightweight open-source correspondence platform

* Have a real conversation with someone (or yourself)
* Manage topics in message threads
* Search everything easily from your phone or computer

### Privacy and Security

* No personal account information is stored
* Delete messages at any time
* Run your own instance

## Setup instructions

1. Create API keys here: https://console.cloud.google.com/apis/credentials

2. Copy the example config file:

    `cp config.php.example config.php`

3. Edit config.php to add your OAuth 2.0 Client ID

4. Then add MySQL database credentials to config.php


## Included libraries

This application is using the Google API Client Library for PHP:

https://github.com/google/google-api-php-client/
