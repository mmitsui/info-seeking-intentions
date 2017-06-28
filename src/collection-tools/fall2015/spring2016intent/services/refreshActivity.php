<?php
/*
Used by sidebar to cancel timeout if user is hovering over sidebar
*/
session_start();
require_once('../core/Connection.class.php');
require_once('../core/Settings.class.php');
require_once('../core/Base.class.php');

$base = Base::getInstance();
if ($base->isSessionActive()) {
  $base->registerActivity();
}
