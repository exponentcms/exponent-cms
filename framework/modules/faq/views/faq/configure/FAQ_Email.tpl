{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{group label="Email Notification"|gettext}
    {control type="checkbox" name="notify_of_new_question" label="Send email notification of new questions"|gettext value=1 checked=$config.notify_of_new_question}
    {control type="text" name="notification_email_subject" label="Subject of Email Notification"|gettext value=$config.notification_email_subject|default:'Someone asked a question'|gettext}
    {*{control type="text" name="notification_email_address" label="Email address of the person to notify"|gettext value=$config.notification_email_address|default:$smarty.const.SMTP_FROMADDRESS}*}
    {control type="email" name="notification_email_address" label="Email address of the person to notify"|gettext value=$config.notification_email_address|default:$smarty.const.SMTP_FROMADDRESS}
{/group}
{group label="Answer Reply Default Settings"|gettext}
    <blockquote>{'These default settings are used when emailing answers to users who have submitted a question'|gettext}</blockquote>
    {control type="text" name="answer_subject" label="Email Subject"|gettext value=$config.answer_subject|default:'Your question was answered'|gettext}
    {*{control type="text" name="answer_from_address" label="Email From Address (leave blank to use site default)"|gettext value=$config.answer_from_address|default:$smarty.const.SMTP_FROMADDRESS}*}
    {control type=email name="answer_from_address" label="Email From Address (leave blank to use site default)"|gettext value=$config.answer_from_address|default:$smarty.const.SMTP_FROMADDRESS}
    {control type="text" name="answer_from_name" label="Email From Name"|gettext value=$config.answer_from_name}
{/group}