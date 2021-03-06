<?php
/**
 * Core english configuration
 *
 * @ingroup    es-f
 * @author     Knut Kohl <knutkohl@users.sourceforge.net>
 * @copyright  2007-2011 Knut Kohl
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl.txt
 * @version    1.0.0
 * @version    $Id: v2.4.1-62-gb38404e 2011-01-30 22:35:34 +0100 $
 */

defined('_ESF_OK') || die('No direct call allowed.');

/**
 * remainng auction time                   0        1        >1
 */
Registry::set('Format.Remain.day',  array('',      '%dd. ', '%dd. '));
Registry::set('Format.Remain.hour', array('%02d:', '%02d:', '%02d:'));
Registry::set('Format.Remain.min',  array('%02d:', '%02d:', '%02d:'));
Registry::set('Format.Remain.sec',  array('%02d',  '%02d',  '%02d' ));

# -----------------------------------------------------------------------------
# date/time format for date()
Registry::set('Format.Date',     'Y-m-d');
Registry::set('Format.Time',     'H:i:s');
Registry::set('Format.DateTime', 'Y-m-d H:i:s');
#Registry::set('Format.DateTime', 'D, Y-m-d H:i:s');

# date/time format for strftime()
Registry::set('Format.DateS',     '%Y-%m-%d');
Registry::set('Format.TimeS',     '%H:%M:%S');
Registry::set('Format.DateTimeS', '%Y-%m-%d %H:%M:%S');
#Registry::set('Format.DateTimeS', '%a, %Y-%m-%d %H:%M:%S');

Registry::set('Format.DecimalChar',        '.');
Registry::set('Format.ThousandsSeparator', ',');
Registry::set('Format.DecimalPlaces',      2);
