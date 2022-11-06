<?php
require_once MODX_CORE_PATH . 'elements/snippets/loadToMediaSource.class.php';
$loadToSelectel = new loadToSelectel($modx);
$fileInfo = $loadToSelectel->uploadFiles('print', 4, (int)$_POST['createdby'].'/{day}-{month}-{year}/');