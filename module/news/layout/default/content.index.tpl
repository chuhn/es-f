<!-- COMMENT
/*
 *
 * @version   $Id$
 * @revision  $Rev$
 */
-->

<div id="news_nav" style="text-align:center">

  <a href="#twitter">Twitter</a>

  <!-- BEGIN NEWS -->
    <a href="#{$ROWID}">{NAME}</a>
    {if:$ROWLAST,"<>",TRUE,"&nbsp;|&nbsp;"}
  <!-- END NEWS -->

</div>

<script type="text/javascript">
  // <![CDATA[
  // if we have JS enabled, we get tabs and don't need anchor links
  FastInit.addOnLoad(function() { $('news_nav').remove() });
  // ]]>
</script>

<div class="tabber">

  <div class="tabbertab">

    <h2><a name="twitter"></a>Twitter Updates</h2>

    <noscript>Sorry, JavaScript is required...</noscript>

    <ul id="twitter_update_list"><li>Connecting to Twitter (JS required) ...</li></ul>
    <a href="http://twitter.com/es_f" id="twitter-link">Follow <tt style="font-size:1.1em">|es|f|</tt> on Twitter</a>
    <br>
    <script type="text/javascript" src="http://twitter.com/javascripts/blogger.js"></script>
    <script type="text/javascript" src="http://twitter.com/statuses/user_timeline/es_f.json?callback=twitterCallback2&amp;count=15"></script>

  </div>

<!-- BEGIN NEWS -->

  <div class="tabbertab">

    <h2><a name="{$ROWID}"></a>{NAME}</h2>

    <!-- IF ERROR -->

      <em>Error fetching news from {a:URL}</em>

    <!-- ELSE -->

      <p>{LINK} ({a:URL})</p>

      <!-- BEGIN DATA -->
      <div class="li">
        <div style="font-size:120%;font-weight:bold">{LINK}</div>
        <small>{nvl:PUBDATE}</small>
        <!-- IF CATEGORY -->
        <br><small><em>[[News.Category]]: {nvl:CATEGORY}</em></small>
        <!-- ENDIF -->
        {nvl:DESCRIPTION}
      </div>
      <!-- END DATA -->

    <!-- ENDIF -->

  </div>

<!-- END NEWS -->

</div>
