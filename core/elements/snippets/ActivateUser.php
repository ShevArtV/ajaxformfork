<?php
if($_GET['lu']){
    require_once MODX_CORE_PATH . 'elements/snippets/ajaxidentification.class.php';
    $username = AjaxIdentification::base64url_decode($_GET['lu']);
    if($user = $modx->getObject('modUser', array('username' => $username))){
        $profile = $user->getOne('Profile');
        $extended = $profile->get('extended');
        if($extended['activate_before'] - time() > 0){
            $user->set('active',1);
            $user->save();
            return true;
        }else{
            $user->remove();
        }
    }
}
return false;