<?php
require_once MODX_CORE_PATH . 'elements/snippets/ajaxidentification.class.php';
if($scriptProperties['method']){
    $method = $scriptProperties['method'];
    $ajaxident = new AjaxIdentification($modx,$hook,$scriptProperties);
    $ajaxident->$method();
}
return true;