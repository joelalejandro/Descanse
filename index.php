<?php
/**
 * Load the framework.
 */
require "descanse.php";

/**
 * Example web service
 * Can be accessible via GET /profile/user
 */
class ProfileService {

  public static function getUser($context) {
    return array("name" => "Joel", "age" => 23);
  }

}
/**/

/**
 * Attend the request!
 */
Descanse::go();
