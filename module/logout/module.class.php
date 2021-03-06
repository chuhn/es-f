<?php
/**
@defgroup Module-Logout Module Logout

*/

/**
 * Module Logout
 *
 * @ingroup    Module
 * @ingroup    Module-Logout
 * @author     Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright  2009-2011 Knut Kohl
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version    1.0.0
 * @version    $Id: v2.4.1-70-g8bb353a 2011-02-07 20:54:16 +0100 $
 */
class esf_Module_Logout extends esf_Module {

  /**
   * @return array Array of actions handled by the module
   */
  public function handles() {
    return array('index');
  }

  /**
   *
   */
  public function IndexAction() {
    Cookie::set('ttl');
    Session::setP('ClearCache', TRUE);
    // logout user and restart session
    Core::StartSession(TRUE);
    Messages::Success(Translation::get('Logout.Success'));
    if (isset($_GET['returnto'])) {
      Core::redirect($_GET['returnto']);
    } else {
      // redirect to default start page/module
      $this->redirect(STARTMODULE);
    }
  }

}