# phalcon-doc-chm
script for phalcon-doc-chm

generate index for chm

usage:

    bin/chm.php

    Notice: The order of arguments passed is the execute order

    Just use --auto if you don't know the procedure.

    --auto      execute all task in the procedure automatically
    ----------  -----------------------------------------------------
    --backup    backup html
    --restore   restore from backup
    --toUtf8    convert encoding from gb2312 to utf-8
    --toGb2312  convert encoding from utf-8 to gb2312
    --tidy      tidy incomplete html
    --main      parse html dom , and then remove some tag cause document load slowly, parse and generate index for chm
    --clean     clean the sign files, but execute always before and after the loop
    --save      flush the result into file
