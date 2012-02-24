{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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
    {if !$config.hidemoduletitle}<h1>{$moduletitle|default:"Frequently Asked Questions"|gettext}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=create text="Add a New FAQ"|gettext}
			{/if}
			{br}
			{if $permissions.manage == 1}
				{icon action=manage text="Manage FAQs"|gettext}
			{/if}
		</div>
    {/permissions}    
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {if $config.allow_user_questions}
        <a href="{link action="ask_question"}">{'Ask a Question'|gettext}</a>
    {/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
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
            <h3>{$cat->name}</h3>
            {foreach name=a from=$cat->records item=qna}
                {assign var=qna_found value=0}
                {math equation="x-1" x=$qna->rank assign=prev}
                {math equation="x+1" x=$qna->rank assign=next}
                <div class="item">
                    <a name="cat{$catid}q{$qna->rank}"></a>
                    <h4>Q{$smarty.foreach.a.iteration}. {$qna->question}</h4>
                        {permissions}
                            <div class="item-actions">
                                {if $permissions.edit == 1}
                                    {icon action=edit record=$qna title="Edit FAQ"|gettext}
                                {/if}
                                {if $permissions.delete == 1}
                                    {icon action=delete record=$qna title="Delete this FAQ"|gettext|cat:"?" onclick="return confirm('"|cat:("Are you sure you want to delete this FAQ?"|gettext)|cat:"');"}
                                {/if}
                            </div>
                        {/permissions}
                    {if $qna->expTag|@count>0 && !$config.disabletags}
                        <span class="tags">
                            {'Tags'|gettext}:
                            {foreach from=$qna->expTag item=tag name=tags}
                                <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
                            {/foreach}
                        </span>
                    {/if}
                    <div class="bodycopy">
                        {$qna->answer}
                    </div>
                </div>
                {assign var=qna_found value=1}
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
        {foreach from=$items item=question}
            <div>
                <a name="faq_{$question->id}"></a>
                <h3>{$question->question}</h3>
                {if $question->expTag|@count>0 && !$config.disabletags}
                    <span class="tags">
                        {'Tags'|gettext}:
                        {foreach from=$question->expTag item=tag name=tags}
                            <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
                        {/foreach}
                    </span>
                {/if}
                <div class="bodycopy">
                    <p>{$question->answer}</p>
                </div>
                    {permissions}
                    <div class="item-actions">
                        {if $permissions.edit == 1}
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
