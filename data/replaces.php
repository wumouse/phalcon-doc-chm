<?php
/**
 * 要替换掉的内容，必须先TIDY后才能替换
 *
 * @author wuhao <wumouse@qq.com>
 * @version $Id$
 */

return [
"
<link href=
'http://fonts.googleapis.com/css?family=Ubuntu:400,500,700,300italic,400italic,500italic&amp;subset=latin,cyrillic-ext'
rel='stylesheet' type='text/css' />
",// 谷歌字体链接，一般需要翻墙访问，比较麻烦

        '<div class="size-wrap">
<div class="header clear-fix"><a class="header-logo" href=
"http://phalconphp.com"><span class="logo-text">Phalcon</span></a>
<div class="header-right"><iframe src=
"http://ghbtns.com/github-btn.html?user=phalcon&amp;repo=cphalcon&amp;type=watch&amp;count=true&amp;size=large"
allowtransparency="true" frameborder="0" scrolling="0" width=
"152px" height="30px"></iframe></div>
<ul class="header-nav">
<li><a href="http://phalconphp.com/" class=
"header-nav-link">Home</a></li>
<li><a href="http://phalconphp.com/download" class=
"header-nav-link">Download</a></li>
<li><a href="http://forum.phalconphp.com/" class=
"header-nav-link active">Forum</a></li>
<li><a href="http://blog.phalconphp.com/" class=
"header-nav-link">Blog</a></li>
<li><a href="http://phalconphp.com/support" class=
"header-nav-link">Support</a></li>
<li><a href="http://store.phalconphp.com/" class=
"header-nav-link">Store</a></li>
<li><a href="https://github.com/phalcon/cphalcon" class=
"header-nav-link">GitHub</a></li>
</ul>
</div>
</div>',// 网页才需要的头部信息

// 网页才需要的尾部信息
    <<<EOL
<div id="footer">
<p>Found a typo or an error? Want to improve this document? The
documentation sources are available on <a href=
"http://github.com/phalcon/docs">Github</a></p>
<p>Need support or have questions? Check our <a href=
"http://phalconphp.com/support">Support Page</a></p>
<p>The Phalcon PHP Framework is released under the <a href=
"https://github.com/phalcon/cphalcon/blob/master/docs/LICENSE.md">new
BSD license</a>.</p>
<p>Except where otherwise noted, content on this site is licensed
under the <a href=
"http://creativecommons.org/licenses/by/3.0/">Creative Commons
Attribution 3.0 License.</a></p>
最后更新于 Nov 28, 2014. Created using <a href=
"http://sphinx.pocoo.org/">Sphinx</a> 1.3b1.
<p>© 版权所有 2014, Phalcon Team and contributors.</p>
<div class="size-wrap footer-wrap">
<div class="donate-wrap">Donate to Phalcon: <a href=
"http://flattr.com/thing/1134206/Phalcon-PHP-Framework" target=
"_blank" class="button button-small orange">Flattr</a> or
<form action="https://www.paypal.com/cgi-bin/webscr" method="post"
style="display: inline"><input type="hidden" name="cmd" value=
"_s-xclick" /> <input type="hidden" name="hosted_button_id" value=
"7LSYMNMFZNG8W" /> <input class="button button-small orange" style=
"border: inherit; display: inline; font-weight: bold" type="submit"
value="via Paypal" title=
"PayPal — The safer, easier way to pay online." /></form>
</div>
<div class="social-links"><a href="https://twitter.com/phalconphp"
class="social-link tw">Twitter</a> <a href=
"http://www.facebook.com/pages/Phalcon/134230726685897" class=
"social-link fb">Facebook</a> <a href=
"https://plus.google.com/102376109340560896457" class=
"social-link gp">Google Plus</a> <a href=
"http://vimeo.com/user10964377" class=
"social-link vm">Vimeo</a></div>
</div>
</div>
</div>
<script type="text/javascript">
//<![CDATA[
    $(window).on("load", function(){
      var cx = '009733439235723428699:lh9ltjgvdz8';
      var gcse = document.createElement('script');
      gcse.type = 'text/javascript';
      gcse.async = true;
      gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//www.google.com/cse/cse.js?cx=' + cx;
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(gcse, s);
      /*window.setTimeout(function(){
        $('.gsc-search-button').css({
          'background': '#f06715',
          'padding': '2px',
          'border': '0'
        });
      }, 1000)*/
    });
//]]>
</script>
EOL
];
