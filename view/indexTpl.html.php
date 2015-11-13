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
    <?php foreach ($index->getConstants() as $item) { ?>
    <LI><OBJECT type="text/sitemap">
      <param name="Local" value="api/<?php echo $item->getLink(); ?>">
      <param name="Name" value="<?php echo $item->getIndex(); ?>">
    </OBJECT>
    <?php } ?>
    <?php foreach ($index->getMethods() as $item) { ?>
    <LI><OBJECT type="text/sitemap">
      <param name="Local" value="api/<?php echo $item->getLink(); ?>">
      <param name="Name" value="<?php echo $item->getIndex(); ?>">
    </OBJECT>
    <?php } ?>
    <?php foreach ($index->getClasses() as $item) { ?>
    <LI><OBJECT type="text/sitemap">
      <param name="Local" value="api/<?php echo $item->getLink(); ?>">
      <param name="Name" value="<?php echo $item->getIndex(); ?>">
    </OBJECT>
    <?php } ?>
    </ul>
  </body>
</html>
