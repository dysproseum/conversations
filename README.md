## Conversations

_Inspired by the shutdown of Google Talk and Google Hangouts_

An open-source messaging app for desktop and mobile

## A lightweight open-source correspondence platform

* Have a real conversation with someone (or yourself)
* Manage topics in message threads
* Search everything easily from your phone or computer

## Privacy and Security

* No personal account information is stored
* Delete messages at any time
* Run your own instance

---

### Updates

Added `iframe` directory to accommodate [Dysproseum Desktop](https://github.com/dysproseum/desktop) windowing.

There is some duplication with posts and buddylist now, but draggable windows was our end goal anyway.

**Next steps:**

* Adding integration to more pages (search, account)
* Keeping in mind if there is a better refactoring path reducing duplication
* Does conversations continue as a configuration of desktop?
* Or just keep the shell and look?

How to support both experiences like [kplaylist with themes](https://github.com/dysproseum/kplaylist/blob/main/kptheme/README.md)?

---

### Setup instructions

1. Create API keys here: https://console.cloud.google.com/apis/credentials

2. Copy the example config file:

    `cp config.php.example config.php`

3. Edit config.php to add your OAuth 2.0 Client ID

4. Then add MySQL database credentials to config.php


## Included libraries

This application is using the Google API Client Library for PHP:

https://github.com/google/google-api-php-client/
