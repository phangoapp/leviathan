<?php

use PhangoApp\PhaModels\CoreFields;
use PhangoApp\PhaTime\DateTime;
use PhangoApp\PhaModels\Webmodel;

class LonelyIpField extends CoreFields\IpField {
    
    public function __construct($size=255)
    {
        
        parent::__construct($size);
        
        $this->duplicated_ip=false;
        
    }
    
    public function check($value)
    {
        
        $value=parent::check($value);
        
        if($this->duplicated_ip==true)
        {
            $this->std_error='Error: you have a server with this ip in the database';
            $this->error=true;
            return $value;
        }
        
        return $value;
        
    }
    
}

class LastUpdatedField extends CoreFields\DateField {
    
    
    public function __construct()
    {
        
        parent::__construct();
        
        $escape=false;
    }
    
    public function show_formatted($value) 
    {
        
        $now=DateTime::now(false);
        
        $timestamp_now=DateTime::obtain_timestamp($now);
        
        $timestamp_value=DateTime::obtain_timestamp($value);

        $five_minutes=$timestamp_now-300;
        
        if($timestamp_value<$five_minutes)
        {
            
            return '<img src="'.PhangoApp\PhaView\View::get_media_url('images/status_red.png', $module='leviathan').'" />';
        
        }
        else
        {
            return '<img src="'.PhangoApp\PhaView\View::get_media_url('images/status_green.png', $module='leviathan').'" />';
            
        }
            
    }

}

class OsServer extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('name', new CoreFields\CharField(), true);
        $this->register('codename', new CoreFields\CharField(), true);
        
    }
    
}

class Server extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('hostname', new CoreFields\CharField(), true);
        $this->register('ip', new LonelyIpField(), true);
        $this->components['ip']->unique=true;
        $this->components['ip']->indexed=true;

        $this->register('status', new CoreFields\BooleanField());
        $this->register('monitoring', new CoreFields\BooleanField());

        $this->register('os_codename', new CoreFields\CharField(), true);

        $this->register('num_updates', new CoreFields\IntegerField());

        $this->register('actual_idle', new CoreFields\DoubleField());

        $this->register('date', new LastUpdatedField());

        
    }
    
}

class ServerGroup extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('name', new CoreFields\CharField(), true);
        $this->register('parent_id', new CoreFields\ParentField($size=11, $name_field='name', $name_value='IdServergroup'));
        
    }
    
}

class ServerGroupItem extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('group_id', new CoreFields\ForeignKeyField(new ServerGroup(), $size=11, $default_id=0, $name_field='name'), true);
        $this->register('server_id', new CoreFields\ForeignKeyField(new Server(), $size=11, $default_id=0, $name_field='hostname'), true);

        
    }
    
}

class ServerGroupTask extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('name_task', new CoreFields\CharField(), true);
        $this->register('ip', new CoreFields\IpField(), true);
        
    }
    
}

class StatusNet extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('ip', new CoreFields\IpField(), true);
        $this->components['ip']->indexed=true;
        $this->register('bytes_sent', new CoreFields\DoubleField());
        $this->register('bytes_recv', new CoreFields\DoubleField());
        $this->register('errin', new CoreFields\IntegerField());
        $this->register('errout', new CoreFields\IntegerField());
        $this->register('dropin', new CoreFields\IntegerField());
        $this->register('dropout', new CoreFields\IntegerField());
        $this->register('last_updated', new CoreFields\BooleanField());
        $this->register('date', new CoreFields\DateField());
        $this->register('server_id', new CoreFields\ForeignKeyField(new Server(), $size=11, $default_id=0, $name_field='hostname', $select_fields=['actual_idle', 'date']), true);
        
    }
    
}

class StatusCpu extends Webmodel {
    
    public function load_components()
    {
        $this->register('ip', new CoreFields\IpField(), true);
        $this->components['ip']->indexed=true;
        $this->register('num_cpu', new CoreFields\IntegerField());
        $this->register('idle', new CoreFields\DoubleField());
        $this->register('last_updated', new CoreFields\BooleanField());
        $this->register('date', new CoreFields\DateField());
        $this->register('server_id', new CoreFields\ForeignKeyField(new Server(), $size=11, $default_id=0, $name_field='hostname', $select_fields=['actual_idle', 'date']), true);
    }
    
}

class StatusDisk extends Webmodel {
    
    public function load_components()
    {
        $this->register('disk', new CoreFields\CharField(), true);

        $this->register('ip', new CoreFields\IpField(), true);
        $this->components['ip']->indexed=true;

        $this->register('size', new CoreFields\DoubleField());
        $this->register('used', new CoreFields\DoubleField());
        $this->register('free', new CoreFields\DoubleField());
        $this->register('percent', new CoreFields\DoubleField());
        $this->register('date', new CoreFields\DateField());
        $this->register('server_id', new CoreFields\ForeignKeyField(new Server(), $size=11, $default_id=0, $name_field='hostname', $select_fields=['actual_idle', 'date']), true);
    }
    
}

class StatusMemory extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('ip', new CoreFields\IpField(), true);
        $this->components['ip']->indexed=true;
        $this->register('total', new CoreFields\BigIntegerField());
        $this->register('available', new CoreFields\BigIntegerField());
        $this->register('percent', new CoreFields\DoubleField());
        $this->register('used', new CoreFields\BigIntegerField());
        $this->register('free', new CoreFields\BigIntegerField());
        $this->register('active', new CoreFields\BigIntegerField());
        $this->register('inactive', new CoreFields\BigIntegerField());
        $this->register('buffers', new CoreFields\BigIntegerField());
        $this->register('cached', new CoreFields\BigIntegerField());
        $this->register('shared', new CoreFields\BigIntegerField());
        $this->register('last_updated', new CoreFields\BooleanField());
        $this->register('date', new CoreFields\DateField());
        $this->register('server_id', new CoreFields\ForeignKeyField(new Server(), $size=11, $default_id=0, $name_field='hostname', $select_fields=['actual_idle', 'date']), true);
        
    }
    
}

class DataServer extends Webmodel {
    
    public function load_components()
    {
        
        $this->register('ip', new CoreFields\IpField(), true);
        $this->components['ip']->indexed=true;

        $this->register('server_id', new CoreFields\ForeignKeyField(new Server(), $size=11, $default_id=0, $name_field='hostname', $select_fields=['actual_idle', 'date']), true);

        $this->register('net_id', new CoreFields\ForeignKeyField(new StatusNet(), $size=11, $default_id=0, $name_field='bytes_sent', $select_fields=['bytes_sent', 'bytes_recv']), true);

        $this->register('memory_id', new CoreFields\ForeignKeyField(new StatusMemory(), $size=11, $default_id=0, $name_field='free', $select_fields=['free','userd','cached']), true);

        $this->register('cpu_id', new CoreFields\ForeignKeyField(new StatusCpu(), $size=11, $default_id=0, $name_field='idle', $select_fields=['num_cpu']), true);

        $this->register('disk0_id', new CoreFields\ForeignKeyField(new StatusDisk(), $size=11, $default_id=0, $name_field="disk", $select_fields=['free', 'used', 'size', 'percent']));

        $this->register('disk1_id', new CoreFields\ForeignKeyField(new StatusDisk(), $size=11, $default_id=0, $name_field="disk", $select_fields=['free', 'used', 'size', 'percent']));

        $this->register('disk2_id', new CoreFields\ForeignKeyField(new StatusDisk(), $size=11, $default_id=0, $name_field="disk", $select_fields=['free', 'used', 'size', 'percent']));
        $this->register('disk3_id', new CoreFields\ForeignKeyField(new StatusDisk(), $size=11, $default_id=0, $name_field="disk", $select_fields=['free', 'used', 'size', 'percent']));
        $this->register('disk4_id', new CoreFields\ForeignKeyField(new StatusDisk(), $size=11, $default_id=0, $name_field="disk", $select_fields=['free', 'used', 'size', 'percent']));
        $this->register('disk5_id', new CoreFields\ForeignKeyField(new StatusDisk(), $size=11, $default_id=0, $name_field="disk", $select_fields=['free', 'used', 'size', 'percent']));
        
    }
    
}





?>
