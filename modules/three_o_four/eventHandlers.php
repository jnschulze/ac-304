<?php

/*
function three_o_four_handle_onBeforeInit()
{
}
*/

function three_o_four_handle_onAfterInit()
{
    ThreeOFourCore::handleOnAfterInit();
}

function three_o_four_handle_onShutdown()
{
    ThreeOFourCore::handleOnShutdown();
}

function three_o_four_handle_onAdminSections(&$sections)
{
    $sections[ADMIN_SECTION_TOOLS][THREE_O_FOUR_MODULE] = array(
      array(
        'name'        => lang('Cache Settings'),
        'description' => lang('Modify cache settings of the Three O Four module'),
        'url'         => assemble_url('three_o_four_settings'),
        'icon'        => get_image_url('admin/modules.gif'),
      ),
    );
}

function three_o_four_handle_onObjectInserted($object)
{
    if($object instanceof User) // new user added
    {
        ThreeOFourCacheInvalidationHelper::removeUserList(0, $object->values['company_id']);
    }
    
    if($object instanceof Company) // new company added
    {
        ThreeOFourCacheInvalidationHelper::removeUserList(0, 0);
    }
    
    if($object instanceof Category)
    {
        switch($object->values['module'])
        {
            case 'pages':
                ThreeOFourCacheInvalidationHelper::removePageList($object->values['project_id']);
                break;
            case 'files':
                ThreeOFourCacheInvalidationHelper::removeFileList($object->values['project_id']);
                break;
            case 'tickets':
                ThreeOFourCacheInvalidationHelper::removeTicketList($object->values['project_id']);
                break;
            case 'discussions':
                ThreeOFourCacheInvalidationHelper::removeDiscussionList($object->values['project_id']);
                break;
            default:
                break;
        }
    }
    
    if(class_exists('Page') && $object instanceof Page)
    {
        ThreeOFourCacheInvalidationHelper::removePageList($object->values['project_id']);
    }
    
    if(class_exists('Milestone') && $object instanceof Milestone) // new milestone added
    {
        ThreeOFourCacheInvalidationHelper::removeMilestoneList($object->values['project_id']);
    }
    
    if(class_exists('File') && $object instanceof File)
    {
        ThreeOFourCacheInvalidationHelper::removeFileList($object->values['project_id']);
    }
    
    if(class_exists('Ticket') && $object instanceof Ticket)
    {
        ThreeOFourCacheInvalidationHelper::removeTicketList($object->values['project_id']);
    }
    
    if(class_exists('Discussion') && $object instanceof Discussion)
    {
        ThreeOFourCacheInvalidationHelper::removeDiscussionList($object->values['project_id']);
    }
}

function three_o_four_handle_onObjectUpdated($object)
{
    if($object instanceof ConfigOption)
    {
        if($object->values['name'] == 'theme') // Theme change!
        {
            ThreeOFourCore::removeCacheEntryByPattern('*');
        }
    }
    
    if($object instanceof User)
    {
        ThreeOFourCacheInvalidationHelper::removeUserList(0, $object->values['company_id']);
        
        // remove everything for this user because the theme might has changed.
        // Todo: Figure out whether the theme really has changed
        ThreeOFourCacheInvalidationHelper::removeEntriesByUser($object->values['id']);
    }
    
    if($object instanceof Category)
    {
        switch($object->values['module'])
        {
            case 'pages':
                ThreeOFourCacheInvalidationHelper::removePageList($object->values['project_id']);
                break;
            case 'files':
                ThreeOFourCacheInvalidationHelper::removeFileList($object->values['project_id']);
                break;
            case 'tickets':
                ThreeOFourCacheInvalidationHelper::removeTicketList($object->values['project_id']);
                break;
            case 'discussions':
                ThreeOFourCacheInvalidationHelper::removeDiscussionList($object->values['project_id']);
                break;
            default:
                break;
        }
    }
    
    if(class_exists('Page') && $object instanceof Page)
    {
        ThreeOFourCacheInvalidationHelper::removePage($page);
        ThreeOFourCacheInvalidationHelper::removePageList($object->values['project_id']);
    }
    
    if(class_exists('Milestone') && $object instanceof Milestone)
    {
        ThreeOFourCacheInvalidationHelper::removeMilestone($object);
        ThreeOFourCacheInvalidationHelper::removeMilestoneList($object->values['project_id']);
    }
    
    if(class_exists('File') && $object instanceof File)
    {
        ThreeOFourCacheInvalidationHelper::removeFile($object);
        ThreeOFourCacheInvalidationHelper::removeFileList($object->values['project_id']);
    }
    
    if(class_exists('Ticket') && $object instanceof Ticket)
    {
        ThreeOFourCacheInvalidationHelper::removeTicket($object);
        ThreeOFourCacheInvalidationHelper::removeTicketList($object->values['project_id']);
    }
    
    if(class_exists('Discussion') && $object instanceof Discussion)
    {
        ThreeOFourCacheInvalidationHelper::removeDiscussion($object);
        ThreeOFourCacheInvalidationHelper::removeDiscussionList($object->values['project_id']);
    }
}

function three_o_four_handle_onObjectDeleted($object)
{
    if($object instanceof User)
    {
        ThreeOFourCacheInvalidationHelper::removeUserList(0, $object->values['company_id']);
    }
    
    if($object instanceof Company)
    {
        ThreeOFourCacheInvalidationHelper::removeUserList(0, 0);
    }
    
    if($object instanceof Category)
    {
        switch($object->values['module'])
        {
            case 'pages':
                ThreeOFourCacheInvalidationHelper::removePageList($object->values['project_id']);
                break;
            case 'files':
                ThreeOFourCacheInvalidationHelper::removeFileList($object->values['project_id']);
                break;
            case 'tickets':
                ThreeOFourCacheInvalidationHelper::removeTicketList($object->values['project_id']);
                break;
            case 'discussions':
                ThreeOFourCacheInvalidationHelper::removeDiscussionList($object->values['project_id']);
                break;
            default:
                break;
        }
    }
}

function three_o_four_handle_onProjectCreated($project, $template)
{
    ThreeOFourCacheInvalidationHelper::removeProjectListForEveryone();
}

function three_o_four_handle_onProjectUpdated($project)
{
    $projectUsers = $project->getUsers();
    
    foreach($projectUsers as $user)
    {
        // Delete project list - progress, owner etc. might have changed
        ThreeOFourCacheInvalidationHelper::removeProjectListForUser($user);

        // Remove the user profile page - progress might has changed
        ThreeOFourCacheInvalidationHelper::removeUserProfile($user);
    }
}

function three_o_four_handle_onProjectDeleted($project)
{
    // Remove project list
    ThreeOFourCacheInvalidationHelper::removeProjectListForEveryone();
}

function three_o_four_handle_onProjectUserAdded($project, $user, $role, $permissions)
{
    ThreeOFourCacheInvalidationHelper::removeProjectListForUser($user);
    ThreeOFourCacheInvalidationHelper::removeProjectUserList($project);
}

function three_o_four_handle_onProjectUserUpdated($project, $user, $role, $permissions)
{
    //die("three_o_four_handle_onProjectUserUpdated");
}

function three_o_four_handle_onProjectUserRemoved($project, $user, $role, $permissions)
{
    ThreeOFourCacheInvalidationHelper::removeProjectListForUser($user);
    ThreeOFourCacheInvalidationHelper::removeProjectUserList($project);
    ThreeOFourCacheInvalidationHelper::removeProjectForUser($project, $user);
}

function three_o_four_handle_onNewRevision($page, $version, $user)
{
    ThreeOFourCacheInvalidationHelper::removePage($page);
}