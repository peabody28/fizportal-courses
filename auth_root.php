<?php
session_start();
if(!isset($_SESSION["rights"]) or !$_SESSION["rights"]=="admin")
    header("Location: /main.php");
