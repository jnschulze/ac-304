# activeCollab Three-o-Four

Although [activeCollab](http://activecollab.com) implements internal caching mechanisms it doesn't accommodate the web browser in terms of caching by not emitting the required headers (ETag, Last-Modified etc.).
Three-o-Four is a very simple module that aims to fix this issue by adding ETag headers to all responses to support client-side caching.

### Client-side caching? Why?
When I started developing an iOS App for activeCollab it turned out that there's just no built-in support for client-side caching which is hardly worth mentioning. In mobile environments, however, each saved byte matters, right?  
So I came up with the idea of writing this module.

### Important Notes

**This module is in an experimental state, see the points below**

* Works for activeCollab 2.3.x
* This early prototype always generates the ETag headers on the fly by buffering the output and hashing the content (instead of caching the whole response on the server-side)  
   **pros**:  
   caching the response as a whole...
   * ...requires a mechanism to invalidate the cache entries which would be difficult to implement (in respect of permissions, theme etc.)
   * ...requires additional storage space (since most resources need to be cache per user, see point #1)
   * ...shouldn't be that much faster because activeCollab caches objects anyway
   * ...doesn't affect the security of your aC instance because permission checking is still up to the aC core.

   **cons**:
   * Hashing the response on-the-fly is quite CPU-intensive.  
     => might turn out disadvantageous when having a fast connection


### Installation
Just put everything into the "modules" directory of you activeCollab instance.  
Enable the module in the admin area.  
That's it.


### ToDo
* Figure out the optimal hashing algorithm in PHP (high efficieny, low risk of collision)
* Add activeCollab 3.x support
* Add server-side response caching where cache invalidation is simple to implement (API requests etc.)
* If hashing turns out disadvantageous (in terms of CPU overhead), only allow mobile user agents