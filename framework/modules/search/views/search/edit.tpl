{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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

{if $record->id != ""}
	<h1>Edit Information for {$modelname}</h1>
{else}
	<h1>New {$modelname}</h1>
{/if}

{form action=update}
	{control name=id type=hidden value=$record->id}
    {control type="text" name="title" label="Title" value=$record->title}
    {control type="text" name="url" label="URL" value=$record->url}
    {control type="files" name="image" label="Image" value=$record->expFile}
    {control type="editor" name="body" label="URL Description" value=$record->body}
    {control type="buttongroup" submit="Save" cancel="Uh.. Nevermind..."}
{/form}
