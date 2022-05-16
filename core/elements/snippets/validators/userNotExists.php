<?php
if(!$modx->getCount('modUser', array('username' => $value))){
    $validator->addError($key, $scriptProperties[$key.'.vTextUserNotExists']);
}
return true;