<?php
/** @defgroup Plugin-CategoryJump Plugin CategoryJump

Category jump link

*/

/**
 * Plugin CategoryJump
 *
 * @ingroup    Plugin
 * @ingroup    Plugin-CategoryJump
 * @author     Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright  2009-2011 Knut Kohl
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version    1.0.0
 * @version    $Id$
 */
class esf_Plugin_CategoryJump extends esf_Plugin {

  /**
   * @return array Array of events handled by the plugin
   */
  public function handles() {
    return array('OutputFilterContent');
  }

  /**
   * Manipulate HTML content before output
   *
   * @param array &$output
   */
  public function OutputFilterContent( &$output ) {
    if (!Request::check('auction', 'index') OR !esf_User::isValid()) return;

    $c = esf_Auctions::getCategories(TRUE);
    sort($c);

    if (count($c) >= $this->Count) {
      $data['Category'] = $c;
      $data['DropDown'] = $this->DropDown;
      $output = $this->Render('content', $data) . $output;
    }
  }

}

Event::attach(new esf_Plugin_CategoryJump);