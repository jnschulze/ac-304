{title}Three O Four Cache Settings{/title}
{add_bread_crumb}{lang}Cache Settings{/lang}{/add_bread_crumb}

<div id="three_o_four_admin">
  {form action='?route=three_o_four_settings' method=post}
    {wrap field=etagEnabled}
      {label for=etagEnabled required=yes}Enable ETag{/label}
      {yes_no name='three_o_four[etag_enabled]' value=$three_o_four.etag_enabled id=etagEnabled}
    {/wrap}
    
    {wrap field=responseCacheEnabled}
      {label for=responseCacheEnabled required=yes}Enable Response Cache{/label}
      {yes_no name='three_o_four[response_cache_enabled]' value=$three_o_four.response_cache_enabled id=responseCacheEnabled}
    {/wrap}
    
    {wrap_buttons}
      {submit}Submit{/submit}
    {/wrap_buttons}
  {/form}
</div>

{empty_slate name=settings module=three_o_four}



