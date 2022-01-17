<?php

if ($_POST['ids']) {
    $_POST['ids'] = json_decode($_POST['ids']);

    $dash = new \Wildfire\Core\Dash;
    $dash->doDeleteObjects($_POST['ids'], $_POST['type']);

    header("Location: {$dash::$last_redirect}");
}
