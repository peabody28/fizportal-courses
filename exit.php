<?php
require_once __DIR__."/classes/User_session.php";


$session = new User_session();
$session->delete();
header("Location: /index");