<?php
require_once "rb.php";

R::setup( 'mysql:host=127.0.0.1;dbname=users_courses',
    'root', '1234' );
R::addDatabase( 'courses_list', 'mysql:host=127.0.0.1;dbname=courses_list', 'root', '1234');
