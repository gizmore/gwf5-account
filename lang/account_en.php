<?php
return array(
'btn_account' => 'Account',
'link_account_form' => 'Profile',
'link_account_encryption' => 'GPG',
'link_account_security' => 'Security',
'link_account_access' => 'Logs',
'link_account_delete' => 'Delete',
##########################################################
'ft_account_settings' => '[%s] %s Settings',
'msg_settings_saved' => 'Your settings for the %s module have been saved.<br/>%s',
##########################################################
'ft_account_form' => '[%s] Account',
'infobox_account_form' => 'Please note that you cannot change your "Realname" after it has been set.<br/>Your demographic options can be set once every %s.',
'section_login' => 'Login Settings',
'section_email' => 'EMail Settings',
'section_demographic' => 'Demography',
'section_options' => 'Options',
'user_hide_online' => 'Hide your online status',
'user_want_adult' => 'Show adult content',
'user_show_birthdays' => 'Show my and other\'s birthday',
'msg_real_name_now' => 'Your realname is now set to %s.',
'msg_user_hide_online_on' => 'Your online status is now hidden.',
'msg_user_show_birthdays_on' => 'You have enabled Birthday announcementa and your own Birthday is shown to others.',
'msg_user_allow_email_on' => 'You now do allow people to email you without spoiling your E-Mail address.',
'msg_user_want_adult_on' => 'You have now enabled adult content for your account.',
'msg_mail_sent' => 'We have sent you an E-Mail with instructions how to proceed.',
'msg_demo_changed' => 'Your demographic settings have been changed.',
##########################################################
'ft_account_encryption' => '[%s] GPG Setup',
'infob_gpg_upload' => 'Here you can upload a GPG key to enable email encryption.',
'err_gpg_fail_fingerprinting' => 'Fingerprinting your upload failed.',
'err_gpg_token' => 'Your GPG token is invalid.',
'msg_gpg_key_added' => 'Your GPG key has been imported and encryption of your E-Mails is enabled.',
##########################################################
'ft_account_security' => '[%s] Security Options',
'box_account_security' => 'You have to enable IP recording to get alerts.',
'accset_record_ip' => 'Record successful login IPs',
'accset_uawatch' => 'Alert on UserAgent change',
'accset_ipwatch' => 'Alert on IP change',
'accset_ispwatch' => 'Alert on Provider change',
##########################################################
'ft_account_delete' => '[%s] Delete Account',
'box_info_deletion' => 'You can choose between disabling your account, and preserving your identity on %s,
Or completely prune your account and all information associated.
If you like, you can leave us a message with feedback on why you wanted to leave.',
'btn_delete_account' => 'Mark Deleted',
'btn_prune_account' => 'Prune Account',
'msg_account_marked_deleted' => 'Your account has been marked as deleted.',
'msg_account_pruned' => 'Your account has been wiped from the database.',
##########################################################
'ft_change_mail' => '[%s] Change E-Mail',
'err_email_retype' => 'Please recheck your E-Mail, as you did not retype it correctly.',
'btn_changemail' => 'Change E-Mail',
##########################################################
'mail_subj_account_deleted' => '[%s] %s Account Deletion',
'mail_body_account_deleted' => '
Hello %s,

The user %s has just executed the following operation on his account: %s.

He has left the following note: (may be empty)
----------------------------------------------
%s
----------------------------------------------
Kind Regards
The %s Script',
##########################################################
'mail_subj_chmail_a' => '[%s] Change E-Mail',
'mail_body_chmail_a' => '
Hello %s,

You want to change your E-Mail on %s to your new Address: <b>%s</b>.

If you want to accept this change, please visit the following link.

%s

Kind Regards
The %2$s Team',
##########################################################
'mail_subj_chmail_b' => '[%s] Confirm E-Mail',
'mail_body_chmail_b' => '
Hello %s,

You want to change your E-Mail on %s to this one (%s).

If you want to accept the change, please visit the following link.

%s

Kind Regards,
The %2$s Team.',
##########################################################
'mail_subj_demochange' => '[%s] Change Demography',
'mail_body_demochange' => '
Hello %s,

You want to change your demographic settings on %s.
Please check if the following settings are correct,
because you can only change them once every %s.

Country: %s
Language: %s
Gender: %s
Date of Birth: %s

If the information is correct, you can accept these settings by visiting this link.

%s

Otherwise, please ignore this E-Mail and try again anytime.

Kind Regards
The %2$s Team',
##########################################################
'mail_subj_account_alert' => '[%s] Access Alert',
'mail_body_account_alert' => '
Hello %s,

There has been access to your %s account with an unusual configuration.

UserAgent: %s
IP Address: %s
Hostname/ISP: %s

You can check your access history here.

%s

You can toggle your access alerts here.

%s

Kind Regards,
The %2$s Team',
##########################################################
		
);
