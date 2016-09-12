<?php

use PhangoApp\Leviathan\Task;
use PhangoApp\PhaModels\Forms;
use PhangoApp\PhaModels\Webmodel;
use PhangoApp\PhaModels\ModelForm;
use PhangoApp\PhaI18n\I18n;

Webmodel::load_model('vendor/phangoapp/leviathan/models/tasks');

class ServerTask extends Task {
    
    public function define()
    {
        $this->files=[['vendor/phangoapp/leviathan/scripts/servers/databases/mariadb/${os_server}/install_mariadb.py', 0700]];
        
        $this->commands_to_execute=[['vendor/phangoapp/leviathan/scripts/servers/databases/mariadb/${os_server}/install_mariadb.py', '']];
        
        #THe files to delete
        
        $this->delete_files=[];
        
        $this->delete_directories=['vendor/leviathan/scripts/servers/databases/mariadb'];
                
        $this->name_task='Install MariaDB standalone server';
        
        $this->description_task='Install standalone Mariadb server';
        
        $this->codename_task='install_mariadb_server';
        
        $this->one_time=1;
        
        $this->version=__DIR__.'/version';
        
        $this->yes_form=true;
        
        $this->arr_form=[];
        
        $this->arr_form['password']=new Forms\PasswordForm('password', '');
        $this->arr_form['repeat_password']=new Forms\PasswordForm('repeat_password', '');
        
        $this->arr_form['password']->required=true;
        $this->arr_form['password']->label=I18n::lang('common', 'password', 'Password');
        
        $this->arr_form['repeat_password']->required=true;
        $this->arr_form['repeat_password']->label=I18n::lang('common', 'repeat_password', 'Repeat Password');
        
    }
    
    public function process_data()
    {
        
        $this->commands_to_execute=[['vendor/phangoapp/leviathan/scripts/servers/databases/mariadb/${os_server}/install_mariadb.py', '--password '.$this->data['password']]];
        
        return true;
        
    }
    
    public function form($values)
    {
        ob_start();
        
        echo '<h2>Create MariaDB server</h2>';
        echo ModelForm::show_form($this->arr_form, $values, $pass_values=false, $check_values=false);
        echo '<p><input type="submit" value="Add new MariaDB server" /></p>';
        
        $form=ob_get_contents();
        
        ob_end_clean();
        
        return $form;
    }
    
    public function process_form($values)
    {
     
        settype($values['password'], 'string');
     
        list($this->arr_form, $post)=ModelForm::check_form($this->arr_form, $values);
        
        if($post===false)
        {
            
            return false;
            
        }
        else
        {
            
            $values['password']=str_replace('"', '', $values['password']);
            $values['repeat_password']=str_replace('"', '', $values['repeat_password']);
            
            if((trim($values['password'])===trim($values['repeat_password'])) && trim($values['password'])!='')
            {
                
                $t=new \Task();
                
                $t->create_forms();
                
                $t->reset_require();
                
                if($t->where(['where IdTask=?', [$this->id]])->update(['data' => ['password' => $values['password']]]))
                {
                    
                    return true;
                    
                }
                
                return false;
                
            }
            else
            {
                $this->arr_form['password']->std_error=I18n::lang('phangoapp/leviathan', 'passwords_doesnt_match', 'Passwords doesn\'t match');
                
                return false;
                
            }
            
        }
        
    }
    
    
}

?>
