<?php

namespace Yxz\LaravelTools\Support;

use Illuminate\Foundation\Application;

class RegisterApplication extends Application
{
    private $provider_details = [];


    public function register($provider, $options = [], $force = false)
    {
        $provider_name =is_string($provider)?$provider:get_class($provider);

       $old_binding = $this->bindings;
       $old_instances = $this->instances;

       parent::register($provider, $options, $force);

        $new_binding = $this->bindings;
        $new_instances = $this->instances;

       $this->provider_details[$provider_name] = [
            'binding' => array_keys(array_diff_key($new_binding,$old_binding)) ,
            'instances' => array_keys(array_diff_key($new_instances,$old_instances)) ,
            'trace' => debug_backtrace(2,8),
        ];
    }

    public function getProviderDetails()
    {
        return $this->provider_details;
    }

}