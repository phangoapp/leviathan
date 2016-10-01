<?php


use PhangoApp\PhaModels\CoreFields;
use PhangoApp\PhaTime\DateTime;
use PhangoApp\PhaModels\Webmodel;

//Dangerous, in next versions is better safe the query in a json variable and check this in set_conditions

class SqlField extends CoreFields\CharField {
    
    public function check($value)
    {
        
        if($this->model_instance)
        {
        
            return $value;
            
        }
        else
        {
            
            $this->error=true;
            $this->txt_error='You need a model related for this field';
            
            return '';
            
        }
        
    }
    
}

class Task extends Webmodel {
    
    public function load_components ()
    {
        
        $this->register('name_task', new CoreFields\CharField(), true);
        $this->register('description_task', new CoreFields\CharField(), true);
        $this->register('codename_task', new CoreFields\CharField(), true);
        $this->register('path', new CoreFields\CharField());
        $this->register('data', new CoreFields\ArrayField(new CoreFields\CharField('data')));
        $this->register('server', new CoreFields\IpField());
        $this->register('hostname', new CoreFields\CharField());
        $this->register('where_sql_server', new SqlField());
        $this->register('user', new CoreFields\CharField());
        $this->register('password', new CoreFields\CharField());
        $this->register('user_path', new CoreFields\CharField());
        $this->register('os_codename', new CoreFields\CharField());
        $this->register('url_return', new CoreFields\CharField());
        
        /*
        $this->register('url_return', new CoreFields\UrlField());
        $this->register('data', new CoreFields\ArrayField(new CoreFields\CharField('data')));
        $this->register('files', new CoreFields\ArrayField(new CoreFields\ArrayField(new CoreFields\CharField())));
        $this->register('commands_to_execute', new CoreFields\ArrayField(new CoreFields\ArrayField(new CoreFields\CharField())));
        $this->register('delete_files', new CoreFields\ArrayField(new CoreFields\CharField()));
        $this->register('delete_directories', new CoreFields\ArrayField(new CoreFields\CharField()));
        $this->register('one_time', new CoreFields\BooleanField());
        $this->register('version', new CoreFields\CharField());
        $this->register('post_func', new CoreFields\CharField());
        $this->register('pre_func', new CoreFields\CharField());
        $this->register('error_func', new CoreFields\CharField());*/
    }
    
}

class LogTask extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('date', new CoreFields\DateField());
        $this->register('task_id', new CoreFields\ForeignKeyField(new Task(), $size=11, $default_id=0, $name_field='name_task'), true);
        $this->register('server', new CoreFields\IpField());
        $this->register('progress', new CoreFields\DoubleField());
        $this->register('no_progress', new CoreFields\BooleanField());
        $this->register('message', new CoreFields\TextField(), true);
        $this->register('error', new CoreFields\BooleanField());
        $this->register('status', new CoreFields\BooleanField());
        $this->register('data', new CoreFields\ArrayField(new CoreFields\CharField('data')));
        
    }
    
    public function log(array $post)
    {
        
        $this->fields_to_update=['task_id', 'error', 'progress', 'message', 'no_progress', 'status', 'server', 'data'];
        
        return Webmodel::$m->logtask->insert($post);
        
    }
    
}

//Search tasks in path vendor/phangoapp/tasks and root phango/tasks



/*
Webmodel::$model['task']->register('files', new CoreFields\ArrayField(CoreFields\ArrayField(CoreFields\CharField())));
Webmodel::$model['task']->register('commands_to_execute', new CoreFields\ArrayField(CoreFields\ArrayField(CoreFields\CharField())));
Webmodel::$model['task']->register('delete_files', new CoreFields\ArrayField(CoreFields\CharField()));
Webmodel::$model['task']->register('delete_directories', new CoreFields\ArrayField(CoreFields\CharField()));
Webmodel::$model['task']->register('error', new CoreFields\BooleanField());
Webmodel::$model['task']->register('status', new CoreFields\BooleanField());
Webmodel::$model['task']->register('url_return', new CoreFields\CharField());
Webmodel::$model['task']->register('server', new CoreFields\IpField());
Webmodel::$model['task']->register('where_sql_server', new CoreFields\WhereSqlField());
Webmodel::$model['task']->register('num_servers', new CoreFields\IntegerField());
Webmodel::$model['task']->register('user', new CoreFields\CharField());
Webmodel::$model['task']->register('password', new CoreFields\CharField());
Webmodel::$model['task']->register('path', new CoreFields\CharField());
Webmodel::$model['task']->register('one_time', new CoreFields\BooleanField());
Webmodel::$model['task']->register('version', new CoreFields\CharField());
Webmodel::$model['task']->register('post_func', new CoreFields\CharField());
Webmodel::$model['task']->register('pre_func', new CoreFields\CharField());
Webmodel::$model['task']->register('error_func', new CoreFields\CharField());
Webmodel::$model['task']->register('extra_data', new CoreFields\CharField());
*/

/*
class Task(WebModel):
    
    def __init__(self, connection):
        
        super().__init__(connection)
    
        $this->register(CoreFields\CharField('name_task'), True)        
        $this->register(CoreFields\CharField('description_task'), True)
        $this->register(CoreFields\CharField('codename_task'))
        $this->register(ArrayField('files', ArrayField('', CoreFields\CharField(''))))
        $this->register(ArrayField('commands_to_execute', ArrayField('', CoreFields\CharField(''))))
        $this->register(ArrayField('delete_files', CoreFields\CharField('')))
        $this->register(ArrayField('delete_directories', CoreFields\CharField('')))
        $this->register(CoreFields\BooleanField('error'))
        $this->register(CoreFields\BooleanField('status'))
        $this->register(CoreFields\CharField('url_return'))
        $this->register(IpField('server'))
        $this->register(CoreFields\TextField('where_sql_server'))
        $this->fields['where_sql_server'].escape=True
        $this->register(CoreFields\IntegerField('num_servers'))
        $this->register(CoreFields\CharField('user'))
        $this->register(CoreFields\CharField('password'))
        $this->register(CoreFields\CharField('path'))
        $this->register(CoreFields\BooleanField('one_time'))
        $this->register(CoreFields\CharField('version'))
        $this->register(CoreFields\CharField('post_func'))
        $this->register(CoreFields\CharField('pre_func'))
        $this->register(CoreFields\CharField('error_func'))
        $this->register(DictField('extra_data', CoreFields\CharField('')))
*/
/*

#!/usr/bin/env python3

from modules.pastafari.models import servers
from paramecio.cromosoma.webmodel import WebModel
from paramecio.cromosoma import corefields
from paramecio.cromosoma.extrafields.dictfield import DictField
from paramecio.cromosoma.extrafields.arrayfield import ArrayField
from paramecio.cromosoma.extrafields.datefield import DateField
from paramecio.cromosoma.extrafields.urlfield import UrlField
from paramecio.cromosoma.extrafields.ipfield import IpField

class Task(WebModel):
    
    def __init__(self, connection):
        
        super().__init__(connection)
    
        $this->register(CoreFields\CharField('name_task'), True)        
        $this->register(CoreFields\CharField('description_task'), True)
        $this->register(CoreFields\CharField('codename_task'))
        $this->register(ArrayField('files', ArrayField('', CoreFields\CharField(''))))
        $this->register(ArrayField('commands_to_execute', ArrayField('', CoreFields\CharField(''))))
        $this->register(ArrayField('delete_files', CoreFields\CharField('')))
        $this->register(ArrayField('delete_directories', CoreFields\CharField('')))
        $this->register(CoreFields\BooleanField('error'))
        $this->register(CoreFields\BooleanField('status'))
        $this->register(CoreFields\CharField('url_return'))
        $this->register(IpField('server'))
        $this->register(CoreFields\TextField('where_sql_server'))
        $this->fields['where_sql_server'].escape=True
        $this->register(CoreFields\IntegerField('num_servers'))
        $this->register(CoreFields\CharField('user'))
        $this->register(CoreFields\CharField('password'))
        $this->register(CoreFields\CharField('path'))
        $this->register(CoreFields\BooleanField('one_time'))
        $this->register(CoreFields\CharField('version'))
        $this->register(CoreFields\CharField('post_func'))
        $this->register(CoreFields\CharField('pre_func'))
        $this->register(CoreFields\CharField('error_func'))
        $this->register(DictField('extra_data', CoreFields\CharField('')))
    

class LogTask(WebModel):
    
    def __init__(self, connection):
        
        super().__init__(connection)
        
        $this->register(DateField('date'))
        $this->register(CoreFields\ForeignKeyField('task_id', Task(connection)), True)
        $this->register(IpField('server'))
        $this->register(CoreFields\DoubleField('progress'))
        $this->register(CoreFields\BooleanField('no_progress'))
        $this->register(CoreFields\TextField('message'), True)
        $this->register(CoreFields\BooleanField('error'))
        $this->register(CoreFields\BooleanField('status'))
        $this->register(DictField('data', CoreFields\CharField('data')))


*/

?>
