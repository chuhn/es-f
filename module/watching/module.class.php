<?php
/**
 * Watching module
 *
 * @ingroup    Module-Watching
 * @author     Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright  2009-2011 Knut Kohl
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @version    $Id: v2.4.1-51-gfeddc24 - Sun Jan 16 21:09:59 2011 +0100 $
 */
class esf_Module_Watching extends esf_Module {

  /**
   * @return array Array of actions handled by the module
   */
  public function handles() {
    return array('index', 'empty');
  }

  /**
   *
   */
  public function IndexAction() {

    esf_Auctions::writeEsniperCfg(TRUE);
    $cmd = array('WATCHING::WATCHEDITEMS',
                 Registry::get('bin_esniper'), esf_User::UserDir());
    $rc = Exec::getInstance()->ExecuteCmd($cmd, $res);
    esf_Auctions::removeEsniperCfg();

    $myitems = array();
    $item = FALSE;

    foreach ($res as $line) {
      if (stristr($line, 'Login failed')) {
        Messages::Error($res);
        break;
      }

      // skip empty lines
      if (empty($line)) continue;

      // Description can contain a ":"...
      list($key, $val) = explode(':', $line, 2);
      $key = str_replace(' ', '_', trim($key));
      $val = trim($val);

      if (strtolower($key) == 'itemnr') {
        $item = $val;
        $myitems[$item]['ITEM'] = $item;
      } else {
        if (!$item) {
          // somthing wrong, possible data line without leading "ItemNr" line...
          // $myitems is still empty
          break;
        }
        $myitems[$item][strtoupper($key)] = $val;
      }
    }

    if (empty($myitems)) {
      if (!empty($res)) {
        $header = array('subject' => ESF_LONG_TITLE.': Watched items log',
                        'body'    => ESF_FULL_TITLE.', Module Version: '
                                    .$this->Version);
        TplData::set('EMAIL', Core::Email($this->Email, $this->Author, TRUE, $header));
        TplData::set('RESULT', array_merge(array('# '.$cmd, ''), $res));
      }
      $this->forward('empty');
    } else {
      $auctionIds = array_keys(esf_Auctions::$Auctions);
      foreach ($myitems as $item => $data) {
        if (strtolower($data['TIME']) != 'ended' OR $this->Ended) {
          $data['ACTIVE']  = in_array($item, $auctionIds);
          $data['ITEMURL'] = htmlspecialchars(sprintf(Registry::get('ebay.ShowUrl'), $item));
          TplData::add('Auctions', $data);
        }
      }

      TplData::set('Categories', esf_Auctions::getCategories());
      TplData::set('Groups', esf_Auctions::getGroups());
      TplData::set('GetCategoryFromGroup', FROMGROUP);
    }
  }

}