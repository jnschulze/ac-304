<?php

class ThreeOFourModule extends Module
{
    var $name = 'three_o_four';
    var $is_system = false;   
    var $version = '0.1';

    function getDisplayName()
    {
        return 'Three O Four';
    }

    function getDescription()
    {
        return lang('Adds extended caching capabilities to activeCollab');
    }

    function defineHandlers(&$handlers)
    {
        // Base stuff
        //$handlers->listen('on_before_init', 'onBeforeInit');
        $handlers->listen('on_after_init', 'onAfterInit');
        $handlers->listen('on_admin_sections', 'onAdminSections');

        // Catch changes on various objects
        $handlers->listen('on_object_inserted', 'onObjectInserted');
        //$handlers->listen('on_object_save', 'onObjectSave');
        $handlers->listen('on_object_updated', 'onObjectUpdated');
        $handlers->listen('on_object_deleted', 'onObjectDeleted');

        // Project stuff
        $handlers->listen('on_project_created', 'onProjectCreated');
        $handlers->listen('on_project_updated', 'onProjectUpdated');
        $handlers->listen('on_project_deleted', 'onProjectDeleted');
        
        // User stuff
        $handlers->listen('on_project_user_added', 'onProjectUserAdded');
        $handlers->listen('on_project_user_updated', 'onProjectUserUpdated');
        $handlers->listen('on_project_user_removed', 'onProjectUserRemoved');
        //$handlers->listen('on_user_options', 'onUser')
        
        // Page stuff
        $handlers->listen('on_new_revision', 'onNewRevision');
    }

    function defineRoutes(&$router)
    {
        $router->map('three_o_four_settings', 'admin/three_o_four/', array('controller' => 'three_o_four_settings', 'action' => 'index'));
    }
    
    function canBeInstalled(&$log)
    {
        if(!function_exists('ob_start'))
        {
            $log[] = 'Output Buffering is not supported.';
            return false;
        }
        
        return true;
    }

    function install()
    {
        $this->addConfigOption('three_o_four_etag_enabled', SYSTEM_CONFIG_OPTION, true);
        $this->addConfigOption('three_o_four_response_cache_enabled', SYSTEM_CONFIG_OPTION, false);

        return parent::install();
    }
}
