<?php

trait AP_Static_Handler
{
    public $prefix = '_';

    public function __call($method, $args)
    {
        return $this->call($method, $args);
    }

    public static function __callStatic($method, $args)
    {
        return (new static())->call($method, $args);
    }

    private function call($method, $args)
    {
        if (! method_exists($this , $this->prefix . $method)) {
            throw new Exception('Call undefined method ' . $method);
        }

        return $this->{'_' . $method}(...$args);
    }
}