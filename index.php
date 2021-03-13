<?php
session_start();
if(isset($_SESSION["name"]))
    header("Location: /main.php");
else
    header("Location: /login.php");
