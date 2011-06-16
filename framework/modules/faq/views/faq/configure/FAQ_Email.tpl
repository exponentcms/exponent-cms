<h2>Email Notifications</h2>
{control type="checkbox" name="notify_of_new_question" label="Send email notification of new questions" value=1 checked=$config.notify_of_new_question}
{control type="text" name="notification_email_addy" label="Email address of the person to notify" value=$config.notification_email_addy}

<h2>These settings are used when email answers to users who have submitted a question</h2>
{control type="text" name="answer_subject" label="Email Subject" value=$config.answer_subject}
{control type="text" name="answer_from_address" label="Email From Address (leave blank to use site default)" value=$config.answer_from_address}
{control type="text" name="answer_from_name" label="Email From Name" value=$config.answer_from_name}
