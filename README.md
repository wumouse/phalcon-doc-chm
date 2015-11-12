# phalcon-doc-chm
script for phalcon-doc-chm

generate index for chm

usage:

bin/chm.php
    
    --backup    backup html
    --encoding  convert encoding from gb2312 to utf-8
    --restore   restore from backup
    --main      parse html dom , and then remove some tag cause document load slowly, parse and generate index for chm
