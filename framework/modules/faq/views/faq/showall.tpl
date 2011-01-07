{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

<div class="module faq showall">
    <h1>{$moduletitle|default:"Frequently Asked Questions"}</h1>
    
    {if $config.allow_user_questions}
        <a href="{link action="ask_question"}">Ask a question</a>    
    {/if}
    
    {if $config.use_toc}
        <ol>
        {foreach from=$questions item=question}
            <li><em><a href="#faq_{$question->id}">{$question->question}</a></em></li>
        {/foreach}
        </ol>
    {/if}
    
    {foreach from=$questions item=question}        
        <div>
            <a name="faq_{$question->id}"></a>
            <h3>{$question->question}</h3>
            <p>{$question->answer}</p>
            <span class="editicons">
                {permissions level=$smarty.const.UILEVEL_NORMAL}
                
                    {if $permissions.edit == 1}
                        {icon img=edit.png action=edit id=$question->id title="Edit FAQ"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete img=delete.png id=$question->id title="Delete this FAQ?" onclick="return confirm('Are you sure you want to delete this FAQ?');"}
                    {/if}                  
              
                    {if $permissions.edit == true}
                        {if $smarty.foreach.items.first == 0}
                            {icon controller=text action=rerank img=up.png id=$text->id push=up}    
                        {/if}
                        {if $smarty.foreach.items.last == 0}
                            {icon controller=text action=rerank img=down.png id=$text->id push=down}
                        {/if}
                    {/if}
                {/permissions}
            </span>
            
            
        </div>
    {foreachelse}
        <em>There are currently no FAQ's</em>
    {/foreach}

    
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == 1}
            {icon class=add action=create title="Add a new FAQ" text="Add a New FAQ"}
        {/if}
        {br}
        {if $permissions.manage == 1}
            {icon action=manage title="Manage FAQs" text="Manage FAQs"}
        {/if}
    {/permissions}    
</div>


