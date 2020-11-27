<?php
include_once ('../../../tribe.init.php');
include_once (__DIR__.'/header.php');
var_dump($dash->do_shell_command('tail -200 /var/log/apache2/error.log'));
include_once (__DIR__.'/footer.php'); ?>