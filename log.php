<?php
use Wildfire\Core;
$dash = new Wildfire\Core\Dash();
include_once __DIR__ . '/header.php';
print_r($dash->do_shell_command('tail -200 /var/log/apache2/error.log'));
include_once __DIR__ . '/footer.php';?>