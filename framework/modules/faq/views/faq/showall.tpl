{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module faq showall">
    <a name="top"></a>
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle|default:"Frequently Asked Questions"|gettext}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit text="Add a New FAQ"|gettext}
			{/if}
			{if $permissions.manage == 1}
				{icon action=manage text="Manage FAQs"|gettext}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='faq' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='faq' text="Manage Categories"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}    
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    {if $config.allow_user_questions}
		{icon class="helplink" action="ask_question" text='Ask a Question'|gettext}
    {/if}
    {if $config.use_toc}
        {if $config.usecategories}
            {foreach name=c from=$cats key=catid item=cat}
                {if $cat->name!=""}<a href="#cat{$catid}"><h4>{$cat->name}</a></h4>{/if}
                <ol>
                    {foreach name=a from=$cat->records item=qna}
                        <li><a href="#cat{$catid}q{$qna->rank}" title="{$qna->answer|summarize:"html":"para"}">{$qna->question}</a></li>
                    {/foreach}
                </ol>
            {/foreach}
        {else}
            <ol>
                {foreach from=$items item=question}
                    <li><em><a href="#faq_{$question->id}">{$question->question}</a></em></li>
                {/foreach}
            </ol>
        {/if}
        <hr/>
    {/if}
    {if $config.usecategories && $cats|@count>0}
        {foreach name=c from=$cats key=catid item=cat}
            <a name="cat{$catid}"></a>
            <h3 class="{$cat->color}">{$cat->name}</h3>
            {foreach name=a from=$cat->records item=qna}
                <div class="item">
                    <a name="cat{$catid}q{$qna->rank}"></a>
                    <h4>Q{$smarty.foreach.a.iteration}. {$qna->question}</h4>
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.edit == 1}
                                    {if $myloc != $qna->location_data}
                                        {icon action=merge id=$qna->id title="Merge Aggregated Content"|gettext}
                                    {/if}
                                    {icon action=edit record=$qna title="Edit FAQ"|gettext}
                                {/if}
                                {if $permissions.delete == 1}
                                    {icon action=delete record=$qna title="Delete this FAQ"|gettext|cat:"?" onclick="return confirm('"|cat:("Are you sure you want to delete this FAQ?"|gettext)|cat:"');"}
                                {/if}
                            </div>
                        {/permissions}
                    {tags_assigned record=$qna}
                    <div class="bodycopy">
                        {$qna->answer}
                    </div>
                </div>
            {foreachelse}
                {if ($config->enable_categories == 1 && $catid != 0) || ($config->enable_categories==0)}
                    <div class="item">
                        <em>{'There are currently no FAQ\'s'|gettext}</em>
                    </div>
                {/if}
            {/foreach}
            <div class="back-to-top"><a href="#top" title="{'Follow this link to go back to the top'|gettext}">{'Back to the top'|gettext}</a></div>
        {/foreach}
    {else}
        {foreach name=a from=$items item=question}
            <div>
                <a name="faq_{$question->id}"></a>
                <h3>Q{$smarty.foreach.a.iteration}. {$question->question}</h3>
                {tags_assigned record=$question}
                <div class="bodycopy">
                    <p>{$question->answer}</p>
                </div>
                    {permissions}
                    <div class="item-actions">
                        {if $permissions.edit == 1}
                            {if $myloc != $question->location_data}
                                {if $permissions.manage == 1}
                                    {icon action=merge id=$question->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$question title="Edit FAQ"|gettext}
                        {/if}
                        {if $permissions.delete == 1}
                            {icon action=delete record=$question title="Delete this FAQ"|gettext|cat:"?" onclick="return confirm('"|cat:("Are you sure you want to delete this FAQ?"|gettext)|cat:"');"}
                        {/if}
                    </div>
                {/permissions}
            </div>
        {foreachelse}
            <em>{'There are currently no FAQ\'s'|gettext}</em>
        {/foreach}
    {/if}
</div>
