<?php

use PhangoApp\Leviathan\Task;

class ServerTask extends Task {
    
    public function define()
    {
        $this->files=[['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/install_postfix.sh', 0700]];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/main.cf', 0644];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/master.cf', 0644];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_domain.py', 0700];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/remove_domain.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_user.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/remove_user.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_redirection.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/remove_redirection.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_alias.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/remove_alias.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/change_quota.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/add_autoreply.py', 0750];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/get_quotas.py', 0750];
        #dd_alias.py        add_user.py      remove_domain.py add_domain.py       autoreply.py     remove_redirection.py add_redirection.py  remove_alias.py  remove_user.py

        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/scripts/autoreply.py', 0755];
        #$this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/utilities/add_account.py', 0700];
        #$this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/utilities/remove_domain.py', 0700];
        #$this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/files/utilities/remove_account.py', 0700];
        
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/dovecot/${os_server}/install_dovecot.sh', 0700];
        
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/dovecot/${os_server}/files/10-auth.conf', 0644];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/dovecot/${os_server}/files/10-mail.conf', 0644];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/dovecot/${os_server}/files/10-master.conf', 0644];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/dovecot/${os_server}/files/10-ssl.conf', 0644];
        
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/databases/sqlite/${os_server}/install_sqlite.sh', 0700];
        
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/sqlgrey/${os_server}/install_sqlgrey.sh', 0700];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/sqlgrey/${os_server}/files/sqlgrey.conf', 0644];
        
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/opendkim/${os_server}/install_opendkim.sh', 0700];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/opendkim/${os_server}/files/opendkim.conf', 0644];
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/mail/opendkim/${os_server}/files/opendkim', 0644];
        
        $this->files[]=['vendor/phangoapp/leviathan/scripts/servers/system/quota/${os_server}/install_quota_home.py', 0700];
        
        # Format first array element is command with the interpreter, the task is agnostic, the files in os directory. The commands are setted with 750 permission.
        # First element is the file, next elements are the arguments
        
        $this->commands_to_execute=[['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/postfix/${os_server}/install_postfix.sh', '']];
        
        $this->commands_to_execute[]=['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/dovecot/${os_server}/install_dovecot.sh', ''];
        
        $this->commands_to_execute[]=['sudo', 'vendor/phangoapp/leviathan/scripts/servers/databases/sqlite/${os_server}/install_sqlite.sh', ''];
        
        $this->commands_to_execute[]=['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/sqlgrey/${os_server}/install_sqlgrey.sh', ''];
        
        $this->commands_to_execute[]=['sudo', 'vendor/phangoapp/leviathan/scripts/servers/mail/opendkim/${os_server}/install_opendkim.sh', ''];
        
        $this->commands_to_execute[]=['sudo', 'vendor/phangoapp/leviathan/scripts/servers/system/quota/${os_server}/install_quota_home.py', '', ''];
        
        #THe files to delete
        
        $this->delete_files=[];
        
        //$this->delete_directories=['vendor/phangoapp/leviathan/scripts/servers/mail', 'vendor/phangoapp/leviathan/scripts/servers/system'];
                
        $this->name_task='Install postfix';
        
        $this->description_task='Install standalone postfix server using unix accounts style';
        
        $this->codename_task='install_standalone_postfix';
        
        $this->one_time=1;
        
        $this->version=__DIR__.'/version';
        
    }
    
}

?>
