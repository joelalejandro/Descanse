Descanse
========

A really really simple API framework for PHP.

## The name

"Descanse" is the Spanish translation for "rest".

## Requirements

PHP >= 5.3.0

## Usage

1. Add the .htaccess file included in this repository. This file redirects all incoming requests to a single dispatcher.
2. Create a dispatcher file (i.e. index.php) that includes the Descanse core.
3. Create a class that has the word "Service" as a suffix (UserService, SearchService, etc.).
4. Create a method in that class with a name starting with get, post, put or delete (i.e. getUser, postUser, putUser, deleteUser, etc.). The method must return a PHP type, object or array.
5. Include the Service class in the dispatcher (this is done automatically by default).
6. Tell Descanse to run!

## A Service Class example

<code><pre><?php
// user.php
class UserService {
  public static function getName($context) {
    return array("first" => "Joel", "last" => "Villarreal");
  }
}
?>
</pre></code>

## The Dispatcher

<code><pre><?php
require "descanse.php";
require "user.php";
Descanse::go();
?></pre></code>

## Guidelines

- Descanse creates routes automatically. Thus, UserProfileService becomes /userprofile/, and UserProfileService::getUserData() becomes GET /userprofile/userdata.
- All methods within the Service class must be *static*.
- All methods within the Service class must contain a *$context* argument, which contains an array with two keys: *request* (contains $_REQUEST data) and *args* (contains all of the slugs in the URL beyond the second slash, i.e. if the route is /userprofile/userdata/1/abc/3f, *args* will contain [0] => 1, [1] => abc, [2] => 3f).
- All methods must start with *get*, *post*, *put* or *delete*.
- If you wish to change the default settings of Descanse, you may do so using *Descanse::$settings* before *Descanse::go()*.

## Settings

- *auto_register*: true to search in all declared classes those whose name ends with "Service", false to allow manual registration. To do so, use the *Descanse::registerService($name)** method.

## License

Descanse is licensed under the MIT License.
