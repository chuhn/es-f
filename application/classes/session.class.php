<?php
/**
 * Session handling class
 *
 * @author     Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright  2007-2011 Knut Kohl
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version    1.0.0
 * @version    $Id: v2.4.1-62-gb38404e 2011-01-30 22:35:34 +0100 $
 */
abstract class Session {

  /**
   *
   */
  const PROTECT = '~protected~session~data~';

  /**
   *
   * @var bool $Debug
   */
  public static $Debug = FALSE;

  /**
   *
   * @var array $Messages
   */
  public static $Messages = array();

  /**
   *
   * @var mixed $NVL
   */
  public static $NVL = NULL;

  /**
   * Renerate session Id on every session start
   */
  public static $RegenerateIdAlways = TRUE;

  /**
   * Set session save path
   *
   * @param string $path
   * @return void
   */
  public static function setSavePath( $path ) {
    self::dbg('Set save path to "%s"', $path);
    session_save_path($path);
  }

  /**
   * Set functions to handle e.g. session file access
   *
   * @param string $open Function on open session
   * @param string $close Function on close session
   * @param string $read Function on read session data
   * @param string $write Function on write session data
   * @param string $destroy Function on destroying session
   * @param string $gc Function on garbage collection
   * @return void
   */
  public static function SetHandler( $open, $close, $read, $write, $destroy, $gc) {
    session_set_save_handler($open, $close, $read, $write, $destroy, $gc);
  }

  /**
   * Set session name
   *
   * @param string $name New session name
   * @return string Name of the current session
   */
  public static function SetName( $name ) {
    self::dbg('Set name to "%s"', $name);
    $name = session_name($name);
    self::dbg('Old name was "%s"', $name);
    return $name;
  }

  /**
   * Is a session active
   *
   * @return bool
   */
  public static function active() {
    return (session_id() != '');
  }

  /**
   * Start session
   *
   * @param int $ttl Time to live for session cookie
   * @return void
   */
  public static function start( $ttl=0 ) {
    session_set_cookie_params($ttl);
    session_start();
    if (self::$RegenerateIdAlways) self::RegenerateId(TRUE);
    self::dbg('Started "%s" = "%s"', session_name(), session_id());
    self::_fixes();
    if (count(self::$Buffer)) {
      foreach(self::$Buffer as $key=>$value) {
        $key = strtolower($key);
        if (isset($_SESSION[$key]) AND is_array($_SESSION[$key])) {
          $_SESSION[$key] = array_merge($_SESSION[$key], $value);
        } else {
          $_SESSION[$key] = $value;
        }
      }
      self::$Buffer = array();
    }
    if (count(self::$Protected)) {
      foreach(self::$Protected as $key=>$value) {
        $key = strtolower($key);
        if (isset($_SESSION[self::PROTECT][$key]) AND
            is_array($_SESSION[self::PROTECT][$key])) {
          $_SESSION[self::PROTECT][$key] = array_merge($_SESSION[self::PROTECT][$key], $value);
        } else {
          $_SESSION[self::PROTECT][$key] = $value;
        }
      }
      self::$Protected = array();
    }
  }

  /**
   * Update the current session id with a newly generated one
   *
   * @param bool $delete Delete the old associated session file
   * @return bool Success
   */
  public static function RegenerateId( $delete=FALSE ) {
    self::dbg('Regenerate id, old = "%s"', session_id());
    if (session_regenerate_id($delete)) {
      self::_fixes();
      self::dbg('Regenerate id, new = "%s"', session_id());
      return TRUE;
    } else {
      self::$Debug AND self::$Messages[] = 'Session: regenerate id, FAILED';
    }
    return FALSE;
  }

  /**
   * Remove all session cookies
   *
   * idea from http://php.net/manual/function.session-get-cookie-params.php
   * UCN from powerlord at spamless dot vgmusic dot com, 19-Nov-2002 08:35
   *
   * @return void
   */
  public static function RemoveCookies() {
    self::$Debug AND self::$Messages[] = 'Session: remove cookies';

    $CookieInfo = session_get_cookie_params();

    if (empty($CookieInfo['domain']) AND empty($CookieInfo['secure'])) {
      setCookie(session_name(), session_id(), 1, $CookieInfo['path']);
    } elseif (empty($CookieInfo['secure'])) {
      setCookie(session_name(), session_id(), 1, $CookieInfo['path'], $CookieInfo['domain']);
    } else {
      setCookie(session_name(), session_id(), 1, $CookieInfo['path'], $CookieInfo['domain'], $CookieInfo['secure']);
    }
  }

  /**
   * Close the session
   *
   * Write the session data
   *
   * @see removeCookies()
   * @return void
   */
  public static function close() {
    @session_write_close();
  }

  /**
   * Destroy the session
   *
   * @see removeCookies()
   * @see close()
   * @param bool $removeCookies Remove also all session cookies
   * @return void
   */
  public static function destroy( $removeCookies=TRUE ) {
    self::dbg('Destroy "%s" = "%s"', session_name(), session_id());
    if ($removeCookies) self::removeCookies();
    $_SESSION = array();
    Session::close();
    @session_destroy();
  }

  /**
   * checkRequest, set session var to requested value or to a default
   *
   * Check if $param is member of $_REQUEST, if not, set to $default and
   * save this param to $_SESSION
   *
   * @param string $param Request parameter
   * @param mixed $default Default value
   * @return void
   */
  public static function checkRequest( $param, $default=FALSE ) {
    $lparam = self::mapKey($param);
    if (isset($_REQUEST[$param])) $_SESSION[$lparam] = $_REQUEST[$param];
    if (!isset($_SESSION[$lparam])) $_SESSION[$lparam] = $default;
  }

  /**
   * Set a variable value into $_SESSION
   *
   * Deletes variable from session if value is NULL
   *
   * @see add()
   * @see get()
   * @param string $key Varibale name
   * @param mixed $val Varibale value
   * @return void
   */
  public static function set( $key, $val=NULL ) {
    $key = self::mapKey($key);
    if (!self::active()) {
      self::$Buffer[$key] = $val;
    } else {
      if (is_null($val)) {
        unset($_SESSION[$key]);
      } else {
        $_SESSION[$key] = $val;
      }
    }
  }

  /**
   * Set a bunch of variables at once into $_SESSION
   *
   * Deletes variable from session if value is NULL
   *
   * @see set()
   * @param array $array Array of Variable => Value
   * @return void
   */
  public static function setA( $array ) {
    foreach ((array)$array as $key => $value) self::set($key, $value);
  }

  /**
   * Add a value to $_SESSION
   *
   * @param string $key Varibale name
   * @param mixed $val Varibale value
   * @return void
   */
  public static function add( $key, $val ) {
    $key = self::mapKey($key);
    if (!self::active()) {
      self::$Buffer[$key][] = $val;
    } else {
      if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = array();
      } elseif (!is_array($_SESSION[$key])) {
        $_SESSION[$key] = array($_SESSION[$key]);
      }
      $_SESSION[$key][] = $val;
    }
  }

  /**
   * Remove a value from $_SESSION
   *
   * @param string $var Varibale name
   * @return void
   */
  public static function delete( $var ) {
    self::set($var);
  }

  /**
   * Chck if a $_SESSION variable is set
   *
   * @param string $var Varibale name
   * @return bool
   */
  public static function is_set( $var ) {
    return isset($_SESSION[self::mapKey($var)]);
  }

  /**
   * Get a value from a $_SESSION variable, return $default if not set
   *
   * @see set()
   * @param string $var Variable name
   * @param mixed $default Return if $var not set
   * @return mixed
   */
  public static function get( $var, $default=NULL ) {
    $var = self::mapKey($var);
    return isset($_SESSION[$var])
         ? $_SESSION[$var]
         : ( isset($default)
           ? $default
           : self::$NVL );
  }

  /**
   * Set a "protected" variable value into $_SESSION
   *
   * It lifes over session lifetime in case of login/logout
   *
   * Deletes variable from session if value is NULL
   *
   * @see addP()
   * @see getP()
   * @param string $key Varibale name
   * @param mixed $val Varibale value
   * @return void
   */
  public static function setP( $key, $val=NULL ) {
    $key = self::mapKey($key);
    if (!self::active()) {
      self::$Protected[$key] = $val;
    } else {
      if (is_null($val)) {
        unset($_SESSION[self::PROTECT][$key]);
      } else {
        $_SESSION[self::PROTECT][$key] = $val;
      }
    }
  }

  /**
   * Add a value to a "protected" $_SESSION variable
   *
   * @see setP()
   * @param string $key Varibale name
   * @param mixed $val Varibale value
   * @return void
   */
  public static function addP( $key, $val ) {
    $key = self::mapKey($key);
    if (!self::active()) {
      self::$Protected[$key][] = $val;
    } else {
      if (!isset($_SESSION[self::PROTECT][$key])) {
        $_SESSION[self::PROTECT][$key] = array();
      } elseif (!is_array($_SESSION[self::PROTECT][$key])) {
        $_SESSION[self::PROTECT][$key] = array($_SESSION[self::PROTECT][$key]);
      }
      $_SESSION[self::PROTECT][$key][] = $val;
    }
  }

  /**
   * Remove a "protected" $_SESSION variable
   *
   * @param string $var Varibale name
   * @return void
   */
  public static function deleteP( $var ) {
    self::setP($var);
  }

  /**
   * Get a value from a protected $_SESSION variable
   *
   * @see setP()
   * @see addP()
   * @param string $key Varibale name
   * @param mixed $default Return if $key not set
   * @return mixed
   */
  public static function getP( $key=NULL, $default=NULL ) {
    $key = self::mapKey($key);
    return isset($key)
         ? ( isset($_SESSION[self::PROTECT][$key])
           ? $_SESSION[self::PROTECT][$key]
           : $default )
         : ( isset($_SESSION[self::PROTECT])
           ? $_SESSION[self::PROTECT]
           : array());
  }

  //---------------------------------------------------------------------------
  // PRIVATE
  //---------------------------------------------------------------------------

  /**
   * Data container
   *
   * @var array $Buffer
   */
  private static $Buffer = array();

  /**
   * Data container
   *
   * @var array $Protected
   */
  private static $Protected = array();

  /**
   * Transform $key for common use
   *
   * @param string $key
   */
  private static function mapKey( $key ) {
    return strtolower($key);
  }

  /**
   * Some statements to fix bugs in IE and PHP < 4.3.3
   */
  private static function _fixes() {
    // to overcome/fix a bug in IE 6.x
    Header('Cache-control: private');
    // from http://php.net/manual/function.session-regenerate-id.php
    // UCN from Gant at BleachEatingFreaks dot com, 24-Jan-2006 09:57
    if (version_compare(PHP_VERSION, '4.3.3', '<')) {
      setCookie( session_name(), session_id(), ini_get('session.cookie_lifetime'));
    }
  }

  /**
   * Collect debug infos
   */
  private static function dbg() {
    if (!self::$Debug) return;

    $params = func_get_args();
    $msg = array_shift($params);
    self::$Messages[] = vsprintf($msg, $params);
  }
}