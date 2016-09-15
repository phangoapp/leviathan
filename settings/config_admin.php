<?php

ModuleAdmin::$arr_modules_admin[]=[ 'levmail', [
array('levmail/servers', 'vendor/phangoapp/levmail/controllers/admin/servers', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'mail_servers', 'Mail servers') ) , 
array('levmail/domains', 'vendor/phangoapp/levmail/controllers/admin/domains', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'domains', 'Domains')), 
array('levmail/add_domain', 'vendor/phangoapp/levmail/controllers/admin/add_domain', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'add_domain', 'Add domain'), '' ),
array('levmail/change_quota', 'vendor/phangoapp/levmail/controllers/admin/change_quota', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'change_quota', 'Change quota'), '' ),
array('levmail/delete_domain', 'vendor/phangoapp/levmail/controllers/admin/delete_domain', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'delete_domain', 'Delete domain'), '' ),
array('levmail/mailboxes', 'vendor/phangoapp/levmail/controllers/admin/mailboxes', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'mailboxes', 'Mailboxes'), ''),  
array('levmail/add_mailbox', 'vendor/phangoapp/levmail/controllers/admin/add_mailbox', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'add_mailbox', 'Add mailbox'), '' ),
array('levmail/delete_mailbox', 'vendor/phangoapp/levmail/controllers/admin/delete_mailbox', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'delete_mailbox', 'Delete mailbox'), '' ),
array('levmail/change_quota_mailbox', 'vendor/phangoapp/levmail/controllers/admin/change_quota_mailbox', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'change_quota_mailbox', 'Change Mailbox quota'), '' ),
array('levmail/aliases', 'vendor/phangoapp/levmail/controllers/admin/aliases', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'aliases', 'Aliases'), '' ),
array('levmail/redirections', 'vendor/phangoapp/levmail/controllers/admin/redirections', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'redirections', 'Redirections'), '' ),
array('levmail/autoreply', 'vendor/phangoapp/levmail/controllers/admin/autoreply', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'autoreply', 'Autoreply'), '' ),
array('levmail/getquotas', 'vendor/phangoapp/levmail/controllers/admin/getquotas', PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'getquotas', 'Get quotas'), '' )
], 
PhangoApp\PhaI18n\I18n::lang('phangoapp/levmail', 'mail', 'Mail') ];

?>

