<h2>{'Email Notifications'|gettext}</h2>
{control type="checkbox" name="notify_of_new_question" label="Send email notification of new questions"|gettext value=1 checked=$config.notify_of_new_question}
{control type="text" name="notification_email_addy" label="Email address of the person to notify"|gettext value=$config.notification_email_addy}

<h2>{'These settings are used when email answers to users who have submitted a question'|gettext}</h2>
{control type="text" name="answer_subject" label="Email Subject"|gettext value=$config.answer_subject}
{control type="text" name="answer_from_address" label="Email From Address (leave blank to use site default)"|gettext value=$config.answer_from_address}
{control type="text" name="answer_from_name" label="Email From Name"|gettext value=$config.answer_from_name}
