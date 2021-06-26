<?php
session_start();
if($_SESSION["rights"]!="admin")
    header("Location: /main.php");
