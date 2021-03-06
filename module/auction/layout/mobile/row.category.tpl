<!-- COMMENT
/*
 * Copyright (c) 2006-2009 Knut Kohl <knutkohl@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 */
-->

<!-- /** Format mouse over tip */ -->
{format:CATEGORY.NAME,[[Auction.ShowAuctionsOfCategory]] > SHOW_TIP}

</tbody>

<tbody id="{CATEGORY.NAME|hash}">

  <tr id="row_{CATEGORY.NAME|hash}" class="category">

    <td colspan="4">
      <span style="float:left;font-size:130%;font-weight:bold">{nvl:CATEGORY.NAME,[[Auction.NoCategory]]}</span>
      <span style="float:left;margin-left:10px">(<span id="{CATEGORY.NAME|hash}_count">{CATEGORY.COUNT}</span>)</span>
    </td>

    <td class="group2">
      <!-- IF CONST.MODULE.REFRESHBUTTONS >= "1" -->
      <a href="?module=auction&amp;action=refreshcategory&amp;category={CATEGORY.NAME|hash}">
        <img class="icon" src="layout/default/images/refresh.gif" alt="refresh"
             title="[[Auction.RefreshCategory|striptags|quote]]"
             onmouseover="Tip('{js:[[Auction.RefreshCategory]]}')">
      </a>
      &nbsp;
      <!-- ENDIF -->
      <a href="#pagetop"><img class="icon" src="layout/default/images/arrow-up.gif" alt="/\"
             title="top" onmouseover="Tip('top')">
      </a>
    </td>
  </tr>

</tbody>

<tbody id="tbody_{CATEGORY.NAME|hash}">
