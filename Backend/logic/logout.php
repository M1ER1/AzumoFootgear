<?php
session_start();

// Löschen aller Session-Variablen
session_unset();

// Zerstören der Session
session_destroy();
?>