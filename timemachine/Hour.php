<?php

/**
*
* @author Henrique Fernandez Teixeira
*
* This is a set of functions that makes easily work with hours in format HH:MM:SS.
*     
*/

namespace hft\timemachine;


class Hour
{

    private $config;

    /**
     * @param array $settings
     */
    public function __construct($settings = array())
    {

        $this->times = array();

        $this->config = array(

                'show_format' => 'H:i:s',

            );

        $this->config($settings);

    }

    /**
     * @param array $settings
     */
    public function config($settings)
    {

        try {

            if (!is_array($settings)) {
                    
                throw new \Exception("Config parameter must be a array");
                    
            }

            $this->config = array_replace($this->config, array_intersect_key($settings, $this->config));

        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());

        }
    }

    /**
     * @param string $time
     *
     * @return boolean
     */
    public function isShow($time = '')
    {

        $d_config = date_create_from_format($this->config['show_format'], $time);

        return ($d_config)? true : false ;

    }
    
    /**
     * @param string $time
     *
     * @return boolean
     */
    public function isWork($time = '')
    {

        $d_config = date_create_from_format('H:i:s', $time);

        return ($d_config)? true : false ;

    }

    /**
     * @param string $time
     *
     * @return string
     */
     
     //passar por referência
    public function toShow($time = '')
    {
        try {            

            $time_obj = date_create_from_format('H:i:s', $time);

            if (!$time_obj) {

                throw new \Exception("<p> Work hour format is: 'H:i:s' passed: $time . </p>");    

            } else {

                return date_format($time_obj, $this->config['show_format']);
            }


        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());

        }
    }

    /**
     * @param string $class
     *
     * @return string
     */
     
      //passar por referência
    public function toWork($time = '')
    {
        try { 

            $time_obj = date_create_from_format($this->config['show_format'], $time);

            if (!$time_obj) {

                throw new \Exception("<p> Config show hour format is: " . $this->config['show_format'] . " passed: $time . </p>");    

            } else {

                return date_format($time_obj, 'H:i:s');
            }


        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
        }

    }

    /**
     * @param string $class
     *
     * @return boolean
     */
    public function validate($time)
    {

        if (!is_string($time)) {

            return false;
        }

        if (!date_create_from_format('H:i:s', $time)) {

            return false;

        } else {
            
            $time = explode(':', $time);
            
            /* [0] Hour / [1] Minutes / [2] Seconds */
            
            if (!is_numeric($time[0]) OR !is_numeric($time[1]) OR !is_numeric($time[2])) {

                return false;

            } elseif ( ($time[0] > 23) OR ($time[1] > 59) OR ($time[2] > 59) ) {

                return false;

            } elseif ( ($time[0] < 0) OR ($time[1] < 0) OR ($time[2] < 0) ) {

                return false;
            }

        }

        return true;
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function toSeconds($time)
    {

        try {

            $time = date_parse($time);
            
            $this->reset();
            
            return $time['hour'] * 3600 + $time['minute'] * 60 + $time['second'];

        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
        }

    }

    /**
     * @param string $first
     * @param string $second
     * @param string $comparison
     *
     * @return boolean
     */
    public function compare($first, $second, $comparison = '==')
    {

        try {
                
            if (!$this->validate($first) || !$this->validate($second)) {

                throw new \Exception("<p> Hour interval must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $second </p>");
            };

            /* Start function */

            $aux = date_parse($first);
            $first = $aux['hour'] * 3600 + $aux['minute'] * 60 + $aux['second'];

            $aux = date_parse($second);
            $second = $aux['hour'] * 3600 + $aux['minute'] * 60 + $aux['second'];


            switch ($comparison) {

                case '>': 
                    return $first > $second; 
                    break;

                case '<': 
                    return $first < $second; 
                    break;

                case '==': 
                    return $first == $second; 
                    break;

                case '>=': 
                    return $first >= $second; 
                    break;

                case '<=': 
                    return $first <= $second; 
                    break;

                case '!=': 
                    return $first != $second; 
                    break;

                default: 
                    throw new \Exception("<p> Comparison must be '>', '<', '==', '>=', '<=' or '!=', you passed: $comparison </p>");
                    break;
            }

            if ($this->config['reset']) {

                $this->reset();              
            }

        } catch(\Exception $e) { 

            echo $e->getTraceAsString();
            die($e->getMessage());
        }


    }

    /**
     * @param string $class
     *
     * @return string|null
     */
    public function interval($first, $second, $time = '01:00:00')
    {
        try {
                
            if (!$this->validate($first) || !$this->validate($time) || !$this->validate($second)) {

                throw new \Exception("<p> Hour must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $time, $second </p>");
            }

            if($time == '00:00:00'){

                throw new \Exception("<p> Interval can't be 00:00:00 </p>");                
            }

            $interval = array();
            $interval[] = $first;

            while ($this->compare($first, $second, '<')) {

                $first = $this->sum($first, $time);

                if($first == '00:00:00'){

                    break;
                }

                $interval[] = $first;

            }

            return $interval;


        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
        }

    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function diff($first, $second)
    {
       try {
                
            if (!$this->validate($first) || !$this->validate($second)) {

                throw new \Exception("<p> Hour must be in format 'HH:MM:SS' / '23:59:59' , you passed: $first, $time, $second </p>");
            }

            if($this->compare($first, $second, '<')){

                throw new \Exception("<p> The second paremeter must be smaller than the fist, you passed: $first, $second</p>");
            }

            $second = strtotime($second);

            $first = strtotime($first);
                                
            if ($first < $second) {

                $first += 86400;
            }
                                
            return date("H:i:s", strtotime("00:00:00") + ($first - $second)); 

        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
        }

    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function sum()
    {

        try {
                
            /* Get func args */

            $times = array();

            $num_args = func_num_args();

            if ($num_args > 24) {

                throw new \Exception("<p> Can't sum more than 24 values </p>");                
            }

            for ($i=0; $i < $num_args; $i++) {

                if (!$this->validate(func_get_arg($i))) {

                    throw new \Exception("<p> Hour must be in format 'HH:MM:SS' / '23:59:59' , you passed: " . func_get_arg($i) ."</p>");
                };

                $times[] = func_get_arg($i);
            };

            if ($num_args < 2) {

                throw new \Exception('<p> Few values to do a sum: ' . $num_args . '</p>' );
                    
            }

            /* Start function */

            $result = array_reduce($times, function($first,$second){

                    $first = date_parse($first);

                    $second = date_parse($second);

                    $final = '';
                    $rest = 0;
                            
                    /* Seconds */
                            
                    $val = $first['second'] + $second['second'];
                            
                    if (($val - 60) >= 0) {
                            
                        $rest = 1;
                            
                        $val = $val - 60;
                            
                    }
                            
                    $final = ($val < 10)? ':0' . (string)$val : ':' . (string)$val ;
                            
                            
                    /* Minutes */
                            
                    $val = $first['minute'] + $second['minute'] + $rest;
                            
                    $rest = 0;
                            
                    if (($val - 60) >= 0) {
                            
                        $rest = 1;
                            
                        $val = $val - 60;
                            
                    }
                            
                    $final = ($val < 10)? ':0' . (string)$val . $final : ':' . (string)$val . $final;
                            
                            
                    /* Hours */
                            
                    $val = $first['hour'] + $second['hour'] + $rest;
                            
                    if ($val > 23) {
                            
                        $final = '00:00:00';
                            
                    } else {
                            
                        $final = ($val < 10)? '0' . (string)$val . $final : (string)$val . $final;
                            
                    }

                    return $final;

            });

            return $result;
                

        } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());

        }
            
    }
    
} 

    
