<?php

require_once __DIR__ . '/app/PlayerSession.php';
require_once __DIR__ . '/app/ResponsePreparer.php';

$results = (new PlayerSession())->getFirstSessionData();

echo ResponsePreparer::prepare($results);