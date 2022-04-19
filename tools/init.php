<?php
    $time = time();
    $token = bin2hex(openssl_random_pseudo_bytes (32)); // pour php 5.3 et +
    $idSession = substr($token, 0, 3).substr($time, 6);
    $_SESSION['idSession'] = $idSession;
    $_SESSION[$_SESSION['idSession']]['startTime'] = $time;
    $_SESSION[$_SESSION['idSession']]['currentTime'] = microtime(true);
    $_SESSION[$_SESSION['idSession']]['lastGet'] = ["", "", ""];
    
    $_SESSION[$_SESSION['idSession']]['was-validated'] = false;
    $_SESSION[$_SESSION['idSession']]['error'] = [];
    $_SESSION[$_SESSION['idSession']]['label'] = [];
    $_SESSION[$_SESSION['idSession']]['rule'] = [];
    $_SESSION[$_SESSION['idSession']]['guarded'] = [];
    $_SESSION[$_SESSION['idSession']]['ruleFile'] = [];
    $_SESSION[$_SESSION['idSession']]['dbRuleEverValidate'] = false;
    $_SESSION[$_SESSION['idSession']]['msg'] = "";
    $_SESSION[$_SESSION['idSession']]['oldPost'] = [];
    $_SESSION[$_SESSION['idSession']]['oldGet'] = [];
    $_SESSION[$_SESSION['idSession']]['user'] = "";
    $_SESSION[$_SESSION['idSession']]['userId'] = "";
    csrfInit();
    // $_SESSION[$_SESSION['idSession']]['csrf'] = "";
    // $_SESSION[$_SESSION['idSession']]['csrfTime'] = "";
    $_SESSION[$_SESSION['idSession']]['lastTime'] = "";
    $_SESSION[$_SESSION['idSession']]['toUnsetFormSessionInfos'] = false;
    $_SESSION[$_SESSION['idSession']]['uniqVal'] = 0;
    
	
	