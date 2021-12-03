<?php
use eftec\bladeone\BladeOne;
use Wildfire\Auth\Auth;

$views = __DIR__.'/views';
$cache = __DIR__.'/cache';
$blade = new BladeOne($views,$cache,BladeOne::MODE_DEBUG);
$auth = new Auth;
$currentUser = $auth->getCurrentUser();

$blade->setAuth($currentUser['name'], $currentUser['role_slug']);

echo $blade->run("hello", ["hello" => "world"]);
