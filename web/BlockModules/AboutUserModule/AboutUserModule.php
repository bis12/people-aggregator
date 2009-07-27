<?php
// global var $path_prefix has been removed - please, use PA::$path static variable
require_once "web/includes/classes/Pagination.php";


class AboutUserModule extends Module {
  
  public $module_type = 'user';
  public $module_placement = 'left|right';
  public $outer_template = 'outer_public_side_module.tpl';
  public $user;//User object.
  public $general_info, $personal_info, $professional_info;
 
  function __construct() {
    parent::__construct();
    $this->block_type = 'AboutUser';
    $this->html_block_id = 'AboutUserModule';
    $this->uid = 0;
    $this->login_uid = 0;
  }
  
  public function initializeModule($request_method, $request_data) {
    switch ($this->page_id) {
      case PAGE_USER_PRIVATE:
        $this->title = __('About Me');
        if(!empty(PA::$login_uid)){
          $this->uid = PA::$login_uid;
          $this->user = PA::$login_user;
          $this->login_uid = PA::$login_uid;
        }   
      break;
      case PAGE_USER_PUBLIC:
      default:
        $this->uid = PA::$page_uid;
        $this->user = PA::$page_user;
        if(!empty(PA::$login_uid)){
          $this->login_uid = PA::$login_uid;
        }   
        $this->title = __("About") . " " . abbreviate_text((ucfirst(PA::$page_user->display_name)), 18, 10);
    }

  }
  
  function render() {
    if (empty($this->user)) {
    //kept for backword compatibility. Can we removed later on when any module on any page refactoring is done.
      $this->user = new User();
      $this->user->load((int)$this->uid);
    }
//    $this->title = sprintf(__('About: %s'), chop_string($this->user->login_name, 12));
    $user_generaldata = User::load_user_profile((int)$this->uid, $this->login_uid, GENERAL);
    $this->general_info = sanitize_user_data($user_generaldata);

    $user_personaldata = User::load_user_profile((int)$this->uid, $this->login_uid, PERSONAL);
    $this->personal_info = sanitize_user_data($user_personaldata);

    $user_professionaldata = User::load_user_profile((int)$this->uid, $this->login_uid, PROFESSIONAL);
    $this->professional_info = sanitize_user_data($user_professionaldata);

    $this->inner_HTML = $this->generate_inner_html();
    $content = parent::render();
    return $content;
  }
  
  function generate_inner_html () {
    switch ($this->mode) {
      case PRI:
        $this->outer_template = 'outer_private_side_module.tpl';
        $inner_template = PA::$blockmodule_path .'/'. get_class($this) . '/side_inner_public.tpl';
      break;
      default:
        $inner_template = PA::$blockmodule_path .'/'. get_class($this) . '/side_inner_public.tpl';
      break;        
    }
    
    $user_profile_info = & new Template($inner_template);
    $user_profile_info->set('user_data_general', $this->general_info);
    $user_profile_info->set('user_data_personal', $this->personal_info);
    $user_profile_info->set('user_data_professional',$this->professional_info);
    $user_profile_info->set_object('user', $this->user);
    
    $inner_html = $user_profile_info->fetch();
    return $inner_html;
  }
 
}
?>  