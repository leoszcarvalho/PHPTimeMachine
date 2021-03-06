<?php

/**
*
* @author Henrique Fernandez Teixeira
*
* This is a set of functions that makes easily work with date in format Y-m-d.
*     
*/

namespace hft\TimeMachine;


class Date
{

	private $languages;
	private $config;
	
    /**
     * @param array $settings
     */
	public function __construct($settings = array())
	{

		$this->config = array(

					'show_format' => 'Y-m-d',
					'language' => 'EN',
					'timezone' => 'America/Sao_Paulo'
			);

		$this->languages = array( 


					'PT' => array(

								'days' => array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'),
								'months' => array('janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho','agosto','setembro','outubro','novembro','dezembro')

							),
							
					'EN' => array(

								'days' => array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'),
								'months' => array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december')

							),
							
					'ES' => array(

								'days' => array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'),
								'months' => array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre')

							)

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
				
				throw new \Exception("<p> Config parameter must be a array </p>");
				
			}

			$this->config = array_replace($this->config, array_intersect_key( $settings, $this->config ));

			date_default_timezone_set($this->config['timezone']);

		} catch (\Exception $e) {
			
            echo $e->getTraceAsString();
            die($e->getMessage());
		}

	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */
	public function isShow($date)
	{

		$d_config = date_create_from_format($this->config['show_format'], $date);

		return ($d_config)? true : false ;

	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */
	public function isWork($date)
	{

		$d_config = date_create_from_format('Y-m-d', $date);

		return ($d_config)? true : false ;

	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */
	public function toShow($date)
	{
		
		try {			

			$date_obj = date_create_from_format('Y-m-d', $date);

			if (!$date_obj) {

				throw new \Exception("<p> Work date format is: 'Y-m-d', '9999-12-31' passed: $date . </p>");	

			} else {

				return date_format($date_obj, $this->config['show_format']);
			}


		} catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
		}
	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */
	public function toWork($date)
	{
		
		try {

			$date_obj = date_create_from_format($this->config['show_format'], $date);

			if (!$date_obj) {

				throw new \Exception("<p> Config show date format is: " . $this->config['show_format'] . " passed: $date . </p>");	

			} else {

				return date_format($date_obj, 'Y-m-d');
			}


		} catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
		}

	}
	
    /**
     * @param string $date
     *
     * @return boolean
     */	
	public function validate($date)
	{
		
        if (!is_string($date)) {

            return false;
        }

        if (!date_create_from_format('Y-m-d', $date)) {

            return false;

        } else {
            
            $date = explode('-', $date);
            
            /* [0] Year / [1] Month / [2] Days */
            
            if (!is_numeric($date[0]) OR !is_numeric($date[1]) OR !is_numeric($date[2])) {

                return false;

            } elseif ( ($date[0] > 9999) OR ($date[1] > 12) OR ($date[2] > 31) ) {

                return false;

            } elseif ( ($date[0] < 1) OR ($date[1] < 1) OR ($date[2] < 1) ) {

                return false;
            }

        }

        return true;		
		
	}
	
    /**
     * @param string $start_date
     * @param number $days
     * @param boolean $interval
     *
     * @return array|string
     */
	public function addDays($start_date, $days = 7, $interval = true)
	{
		
		try {

			if (!$this->validate($start_date)) {

				throw new \Exception("<p> You passed value out from the work format 'Y-m-d', '9999-12-31' : $first, $second</p>");			
			}

			if($days < 1){

				throw new \Exception("<p> Days can't be 0 or negative, you passed: $days</p>");				
			}
    
			$dates = array();

			$date = date_create($start_date);
	        $dates[] = $start_date;
	            
	        for ($i=0; $i < $days ;$i++) {
	                
	            if ($i != 0) {
	                    
	                $date = date_create($current_date);
	            }
	            
	            date_add($date, date_interval_create_from_date_string("1 day"));
	            $current_date = date_format($date, "Y-m-d");

	            if($interval){

	            	$dates[] = $current_date;
	        	}
	        }

	     	return ($interval)? $dates : $current_date;

     	} catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
     	}
	}

    /**
     * @param string $first
     * @param string $second
     *
     * @return array
     */
	public function interval($first, $second)
	{

		try{

			if (!$this->validate($first) || !$this->validate($second)) {

				throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $first, $second</p>");			
			}

		    $return = array();
		    $interval = new \DateInterval('P1D');

		    $end = new \DateTime($second);
		    $end->add($interval);

		    $period = new \DatePeriod(new \DateTime($first), $interval, $end);

		    foreach ($period as $date) { 
		        $return[] = $date->format('Y-m-d'); 
		    }

		    return $return;

	    } catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());
     	}
	}
	
    /**
     * @param string $first
     * @param string $second
     * @param string $type
     *
     * @return array|string|int
     */	
	public function weekDays($first = '', $second = '', $type = 'numeric')
	{

		try {
			
			if (empty($first) || empty($first)) {

				if (!$this->validate($first) || !$this->validate($second)) {

					throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $first, $second</p>");			
				}

				return $this->languages[$this->config['language']]['days'];

			} else {

				if (!$this->validate($first) || !$this->validate($second)) {

					throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $first, $second</p>");			
				}

				$interval = $this->interval($first, $second);		

				if ($type === 'name') {
						
					$callback = function($date){

						return $this->languages[$this->config['language']]['days'][date('w', strtotime($date))];
					};


				} elseif($type === 'numeric') {

					$callback = function($date){

						return date('w', strtotime($date));
					};

				} else {

					throw new \Exception("<p>  The third parameter can only be 'name' or 'numeric' </p>");
				}

			    return array_map($callback, $interval);

			}


		} catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());

		}

	}
	
    /**
     * @param string $date
     * @param string $type
     *
     * @return string|int
     */
	public function weekDay($date, $type = 'numeric')
	{

		try {
	
			if (!$this->validate($date)) {
				
				throw new \Exception("<p> You passed values out from the work format 'Y-m-d', '9999-12-31': $date </p>");
			}
			
			if ($type === 'name') {

				return $this->languages[$this->config['language']]['days'][date('w', strtotime($date))];


			} elseif ($type === 'numeric') {

				return date('w', strtotime($date));

			} else {

				throw new \Exception("<p>  The second parameter can only be 'name' or 'numeric', you passed: $type </p>");
			}

		} catch(\Exception $e) {

            echo $e->getTraceAsString();
            die($e->getMessage());

		}

	}

}
