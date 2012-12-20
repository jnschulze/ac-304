<?php

class ThreeOFourCacheInvalidationHelper
{
    public static function removeEntriesByUser($userId)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u'.$userId.'_*');
    }
    
    public static function removeProjectForUser($project, $user)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u'.$user->values['id'].'-projects-'.$project->values['id'].'*');
    }
    
    public static function removeProjectListForEveryone()
    {
        // applies to all project users and all list pages
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects');
    }
    
    public static function removeProjectListForUser($user)
    {
        // Remove project list for user
        ThreeOFourCore::removeCacheEntryByPattern('u'.$user->values['id'].'_projects');
        
        // Remove all pages of the project list
        ThreeOFourCore::removeCacheEntryByPattern('u'.$user->values['id'].'_projects_p*');
    }
    
    public static function removeUserList($userId = 0, $companyId = 0)
    {
        if($companyId == 0)
        {
            ThreeOFourCore::removeCacheEntryByPattern('u\d+_people');
            ThreeOFourCore::removeCacheEntryByPattern('u\d+_people_p*');
        }
        else
        {
            ThreeOFourCore::removeCacheEntryByPattern('u\d+_people-'.$companyId);
            ThreeOFourCore::removeCacheEntryByPattern('u\d+_people-'.$companyId.'_p*');
        }
    }
    
    public static function removeProjectUserList($project)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$project->values['id'].'-people');
    }
    
    public static function removeUserProfile($user)
    {
        //die('u\d+_people-'.$user->values['company_id'].'-users-'.$user->values['id']);
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_people-'.$user->values['company_id'].'-users-'.$user->values['id']);
    }
    
    // ---------------------------------------------------
    //  Pages
    // ---------------------------------------------------
    
    public static function removePageList($projectId)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$projectId.'-pages');
    }
    
    public static function removePage($page)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-\d+-pages'.$page->values['id']);
    }
    
    // ---------------------------------------------------
    //  Milestones
    // ---------------------------------------------------
    
    public static function removeMilestoneList($projectId)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$projectId.'-milestones');
    }
    
    public static function removeMilestone($milestone)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$milestone->values['project_id'].'-milestones-'.$milestone->values['id']); 
    }
    
    // ---------------------------------------------------
    //  Files
    // ---------------------------------------------------
    
    public static function removeFileList($projectId)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$projectId.'-files');
    }
    
    public static function removeFile($file)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$file->values['project_id'].'-files-'.$file->values['id']); 
    }
    
    // ---------------------------------------------------
    //  Tickets
    // ---------------------------------------------------
    
    public static function removeTicketList($projectId)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$projectId.'-tickets');
    }
    
    public static function removeTicket($ticket)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$ticket->values['project_id'].'-tickets-'.$ticket->values['id']); 
    }
    
    // ---------------------------------------------------
    //  Discussions
    // ---------------------------------------------------
    
    public static function removeDiscussionList($projectId)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$projectId.'-discussions');
    }
    
    public static function removeDiscussion($discussion)
    {
        ThreeOFourCore::removeCacheEntryByPattern('u\d+_projects-'.$discussion->values['project_id'].'-discussions-'.$discussion->values['id']); 
    }
}