<?php
/**
 * Layout switcher
 *
 * @ingroup    Plugin-LayoutSwitch
 * @author     Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright  2009-2011 Knut Kohl
 * @license    http://www.gnu.org/licenses/gpl.txt GNU General Public License
 * @version    $Id: v2.4.1-31-g1f61435 - Sat Jan 8 20:25:54 2011 +0100 $
 */
class esf_Plugin_LayoutSwitch extends esf_Plugin {

  /**
   * @return array Array of events handled by the plugin
   */
  public function handles() {
    return array('Start', 'OutputFilterHtmlEnd');
  }

  /**
   *
   */
  public function Start() {
    if ($layout = $this->Request('switchlayout')) Session::setP('Layout', $layout);
  }

  /**
   *
   */
  public function OutputFilterHtmlEnd( &$output ) {
    if (Registry::get('esf.ContentOnly')) return;

    $data['LAYOUTS'] = getLayouts();
    $data['LAYOUT'] = Session::getP('Layout');
    $data['TARGET'] = 'footer_after';
    $output .= $this->Render('content', $data);
  }
}

Event::attach(new esf_Plugin_LayoutSwitch);