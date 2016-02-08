{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="module user manage-user-password">
    <h1>{'Change Password for'|gettext} {$u->username}</h1>
    {form action=update_userpassword}
        {control type="hidden" name="id" value=$u->id}
        {control type="hidden" name="username" value=$u->username}
        <div class="row">
            {control class="col-sm-4" type="password" name="new_password1" label="Type New Password"|gettext}
            <div class="col-sm-4" style="padding-top: 8px;">
                <div class="pwstrength_viewport_progress"></div>
            </div>
        </div>
        {control type="password" name="new_password2" label="Retype Password"|gettext focus=1}
        {br}
        {control type="buttongroup" submit="Change Password"|gettext cancel="Cancel"|gettext}
    {/form}
</div>

{script unique="showlogin" jquery='pwstrength-bootstrap-1.2.10'}
{literal}
    $(document).ready(function () {
        "use strict";
        var options = {};
        options.common = {
            minChar: {/literal}{$smarty.const.MIN_PWD_LEN}{literal},
        };
//        options.rules = {
//            activated: {
//                wordNotEmail: true,
//                wordLength: true,
//                wordSimilarToUsername: true,
//                wordSequences: true,
//                wordTwoCharacterClasses: false,
//                wordRepetitions: false,
//                wordLowercase: true,
//                wordUppercase: true,
//                wordOneNumber: true,
//                wordThreeNumbers: true,
//                wordOneSpecialChar: true,
//                wordTwoSpecialChar: true,
//                wordUpperLowerCombo: true,
//                wordLetterNumberCombo: true,
//                wordLetterNumberCharCombo: true
//            }
//        };
        options.ui = {
            container: ".manage-user-password",
            showVerdictsInsideProgressBar: true,
            showErrors: true,
            viewports: {
                progress: ".pwstrength_viewport_progress",
                errors: ".pwstrength_viewport_progress",
            }
        };
        $('#new_password1').pwstrength(options);
    });
{/literal}
{/script}
