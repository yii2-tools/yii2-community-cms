<?php

$config = require(dirname(__DIR__) . '/config/acceptance.php');

// fix: disable modules: 'debug' during prepare stage
unset($config['bootstrap'][2]);
unset($config['modules']['debug']);

new yii\web\Application($config);
