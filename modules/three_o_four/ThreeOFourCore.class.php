<?php

require_once THREE_O_FOUR_MODULE_PATH . '/functions.php';

class ThreeOFourCore
{
    private static $_hashAlgoPrefs = array('md4', 'md5');
    private static $_cacheKeyPrefix = THREE_O_FOUR_MODULE;
    private static $_request; // request is cached here
    
    private static $_routeMap = array
    (
        // Depends on user's permissions, store per user
        'projects' => array('store_per_user' => true),
        'people' => array('store_per_user' => true),
        'people_company' => array('store_per_user' => true),
        'people_company_user' => array('store_per_user' => true),
        'project_people' => array('store_per_user' => true),
        
        'project_pages' => array('store_per_user' => true),
        'project_page' => array('store_per_user' => true),
        
        'project_milestones' => array('store_per_user' => true),
        'project_milestone' => array('store_per_user' => true),
        
        'project_files' => array('store_per_user' => true),
        'project_file' => array('store_per_user' => true),
        
        'project_tickets' => array('store_per_user' => true),
        'project_ticket' => array('store_per_user' => true),
        
        'project_discussions' => array('store_per_user' => true),
        'project_discussion' => array('store_per_user' => true),
    );
    
    public static function handleOnAfterInit()
    {
        // only allow GET requests
        if($_SERVER['REQUEST_METHOD'] != 'GET'
        // skip if there are flash messages
        || (defined('USE_FLASH') && (flash_get('success') != NULL || flash_get('error') != NULL)))
        {
            return;
        }
        
        $etagEnabled = ConfigOptions::getValue('three_o_four_etag_enabled');
        $cacheEnabled = ConfigOptions::getValue('three_o_four_response_cache_enabled');
        $engineEnabled = ($etagEnabled || $cacheEnabled);
        
        if(!$engineEnabled)
        {
            return;
        }
        
        $request = self::_getRequest();
        
        $request->three_o_four_emit_etag = $etagEnabled;
        
        if($cacheEnabled && isset(self::$_routeMap[$request->matched_route]))
        {
            $cacheOpts = self::$_routeMap[$request->matched_route];
            //$request->three_o_four_cache_response = self::_isCacheableRequest($request);
            $request->three_o_four_cache_response = true;
            $request->three_o_four_cache_opts = $cacheOpts;
            
            if(($cacheData = self::_getFromCache($request)) !== NULL)
            {
                // if the user has no view permissions, continue request as usual to output the standard 403
                if(self::_checkPermissions($cacheData, $request))
                {   
                    self::_sendResponse($cacheData['content'], $cacheData['contentLength'], $cacheData['etag'], $cacheData['headers']);
                    exit;
                }
            }
        }
        
        $application = application();
        $application->events_manager->listen('on_shutdown', 'onShutdown', THREE_O_FOUR_MODULE);
        
        ob_start();
        $request->three_o_four_buffering_started = true;
    }
    
    public static function handleOnShutdown()
    {
        $request = self::_getRequest();
        if(!$request->three_o_four_buffering_started) return;
        
        $response = ob_get_clean();
        
        if(http_response_code() != 200)
        {
            ob_end_flush();
            return;
        }
        
        $contentLength = strlen($response);
        $etag = null;
        
        if(isset($request->three_o_four_cache_response) && $request->three_o_four_cache_response == true)
        {
            $key = self::_generateCacheKey($request);

            $microtime = microtime();
            //$etag = md5($key . $microtime);
            $etag = self::_hash($response);
            
            $cacheData = array('changed' => $microtime, 'etag' => $etag, 'contentLength' => $contentLength, 'content' => $response, 'headers' => apache_response_headers(), 'params' => array());
            
            if(isset($request->three_o_four_cache_opts['store_params']))
            {
                foreach($request->three_o_four_cache_opts['store_params'] as $k)
                {
                    if(isset($request->url_params[$k]))
                    {
                        $cacheData['params'][$k] = $request->url_params[$k];
                    }
                }
            }
            
            // Optimization for pages
            
            cache_set($key, $cacheData);
            cache_save();
        }
        else if(isset($request->three_o_four_emit_etag) && $request->three_o_four_emit_etag == true)
        {
            $etag = self::_hash($response);
        }
        
        self::_sendResponse($response, $contentLength, $etag);
    }
    
    private static function _getRequest()
    {
        if(self::$_request === NULL)
        {
            $router = Router::instance();
            // TODO: Try to access the previously matched object
            self::$_request = $router->match(ANGIE_PATH_INFO, ANGIE_QUERY_STRING);
        }
        return self::$_request;
    }
    
    /*
    private static function _isCacheableRequest($request)
    {
        $urlParams = $request->getUrlParams();
        if(count($urlParams) == 3) // module/controller/action? fine.
        {
            return true;
        }
        
        if(count($urlParams) == 4 && isset($urlParams['page']))
        {
            return true;
        }
    }
    */
    
    protected static function _getFromCache($request)
    {   
        return cache_get(self::_generateCacheKey($request));
    }
    
    protected static function _checkPermissions($cacheData, $request)
    {
        return (isset($request->three_o_four_cache_opts['store_per_user']) && $request->three_o_four_cache_opts['store_per_user'] == true);
    }
    
    protected static function _sendResponse($content, $contentLength, $etag = null, $headers = array())
    {
        $requestHeaders = getallheaders();
        $notModified = ($etag != null && isset($requestHeaders['If-None-Match']) && $requestHeaders['If-None-Match'] == $etag);
        
        if($notModified)
            http_response_code(304);
        else
            http_response_code(200);
            
        if(isset($headers['Content-Type']))
        {
            header('Content-Type: ' . $headers['Content-Type'], true);
        }
        
        if($etag != null)
        {
            header('Etag: ' . $etag, true);
        }
        
        header('Pragma: ', true);
        header('Cache-Control: no-cache,must-revalidate', true);
        
        if(!$notModified)
        {
            header('Content-Length: ' . $contentLength);
            echo $content;
        }
    }
    
    private static function _generateCacheKey($request)
    {   
        $key = self::$_cacheKeyPrefix;
        
        if(isset($request->three_o_four_cache_opts['store_per_user']) && $request->three_o_four_cache_opts['store_per_user'] == true)
        {
            $key .= '_u' . get_logged_user()->values['id'];
        }
        
        if($request->isApiCall())
        {
            $key .= '_api';
        }
        
        $key .= '_' . str_replace('/', '-', trim(ANGIE_PATH_INFO, '/'));
        
        //var_dump($request->getUrlParams());
        //die();
        
        $urlParams = $request->getUrlParams();
        if(isset($urlParams['page']))
        {
            $key .= '_p' . (int)$urlParams['page'];
        }
        
        if(isset($request->three_o_four_cache_opts['evaluate_params']))
        {
            foreach($request->three_o_four_cache_opts['evaluate_params'] as $k => $v)
            {
                if(isset($urlParams[$v]))
                {
                    $key .= '_' . $k . $request->urlParams[$v];
                }
            }
        }
        
        return $key;
    }
    
    private static function _hash($data)
    {
        if(function_exists('hash_algos') && function_exists('hash'))
        {
            $algos = hash_algos();
            
            foreach(self::$_hashAlgoPrefs as $pref)
            {
                if(in_array($pref, $algos))
                {
                    return hash($pref, $data, false);
                }
            }
        }
        
        return md5($data);
    }
    
    public static function removeCacheEntryByPattern($pattern)
    {
        $pattern = self::$_cacheKeyPrefix . '_' . $pattern;
        //var_dump($pattern);
        
        cache_remove_by_pattern($pattern);
    }
}