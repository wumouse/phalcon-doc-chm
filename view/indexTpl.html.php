<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html>
  <head>
    <meta name="generator" content="PhD">
    <!-- Sitemap 1.0 -->
  </head>
  <body>
    <object type="text/site properties">
      <param name="Window Styles" value="0x800227">
    </object>
    <ul>
    <?php foreach ($indexesConstants as $index) { ?>
    <LI><OBJECT type="text/sitemap">
      <param name="Local" value="<?php echo $apiPathName; ?>/<?php echo $index['link']; ?>">
      <param name="Name" value="<?php echo $index['index']; ?>">
    </OBJECT>
    <?php } ?>
    <?php foreach ($indexesMethods as $index) { ?>
    <LI><OBJECT type="text/sitemap">
      <param name="Local" value="<?php echo $apiPathName; ?>/<?php echo $index['link']; ?>">
      <param name="Name" value="<?php echo $index['index']; ?>">
    </OBJECT>
    <?php } ?>
    <?php foreach ($indexesClasses as $index) { ?>
    <LI><OBJECT type="text/sitemap">
      <param name="Local" value="<?php echo $apiPathName; ?>/<?php echo $index['link']; ?>">
      <param name="Name" value="<?php echo $index['index']; ?>">
    </OBJECT>
    <?php } ?>
    </ul>
  </body>
</html>
