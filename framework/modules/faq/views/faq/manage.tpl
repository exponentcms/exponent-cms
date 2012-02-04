{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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

{css unique="mangefaq-buttons" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

{css unique="managefaq" corecss="tables"}

{/css}

<div class="module faq manage">
    <h1>{'Manage Questions'|gettext}</h1>
    <p>{'Here you can view questions on your site and edit, delete, and answer unanswered questions'|gettext}</p>
    
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=create text="Add a New FAQ"|gettext}
			{/if}
			{if $permissions.manage == 1}
				{ddrerank items=$page->records model="faq" sortfield="question" label="FAQs"|gettext}
			{/if}
		</div>
    {/permissions}
    <table class="exp-skin-table">
		<thead>
			<tr>
				{$page->header_columns}
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$page->records item=question name=questions}
			<tr class="{cycle values="even, odd"}">
				<td>                
					{if $question->include_in_faq == 1}
						<a href="{link action=edit_toggle id=$question->id}" title="Remove this question from the FAQs"|gettext>
							{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_on.png'}
						</a>
					{else}
						<a href="{link action=edit_toggle id=$question->id}" title="Add this question to the FAQs"|gettext>
							{img src=$smarty.const.ICON_RELATIVE|cat:'toggle_off.png'}
						</a>   
					{/if}                
				</td>
				<td>{if $question->answer != ""}{img src=$smarty.const.ICON_RELATIVE|cat:'clean.png'}{/if}</td>
				<td>{$question->question}</td>
				<td>{$question->created_at|format_date}</td>
				<td>{$question->submitter_name}</td>
				<td>
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
								{icon action=edit record=$question title="Edit FAQ"|gettext}
							{/if}
							{if $permissions.delete == 1}
								{icon action=delete record=$question title="Delete this FAQ?"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this FAQ?"|gettext)|cat:"');"}
							{/if} 
						</div>					
					{/permissions}
				</td>
			</tr>
			{foreachelse}
			<tr><td>{'No questions found'|gettext}</td></tr>
			{/foreach}
		</tbody>
    </table>    
</div>

<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="javascript: history.go(-1)">Go Back</a>
