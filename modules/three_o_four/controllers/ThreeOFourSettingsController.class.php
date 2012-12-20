<?php

use_controller('admin', SYSTEM_MODULE);

class ThreeOFourSettingsController extends AdminController
{
    var $controller_name = 'three_o_four_settings';
    
    function __construct($request)
    {
        parent::__construct($request);
        
        $three_o_four_data = $this->request->post('three_o_four');
        if(!is_array($three_o_four_data))
        {
            $three_o_four_data = array(
            'etag_enabled' => ConfigOptions::getValue('three_o_four_etag_enabled'),
            'response_cache_enabled' => ConfigOptions::getValue('three_o_four_response_cache_enabled'),
          );
        } // if
        $this->smarty->assign(array(
          'three_o_four' => $three_o_four_data,
        ));
        

        if ($this->request->isSubmitted())
        {
            ConfigOptions::setValue('three_o_four_etag_enabled', array_var($three_o_four_data, 'etag_enabled', null));
            ConfigOptions::setValue('three_o_four_response_cache_enabled', array_var($three_o_four_data, 'response_cache_enabled', null));
            flash_success('Cache settings have been updated');
            $this->redirectTo('three_o_four_settings');
        }
    }
    
    function index()
    {

    }
}