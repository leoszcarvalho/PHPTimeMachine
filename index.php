<?php
  
  include('TimeMachine/Date.php');
  include('TimeMachine/Hour.php');

  $config = array(

  		'show_format' => 'H:i'

  	);

  $hour = new hft\TimeMachine\Hour($config);
  $date = new hft\TimeMachine\Date();

  echo "<pre>";
  var_dump($date->addDays('1999-12-12',-200000,false));
  echo "</pre>";

  echo "<pre>";
  var_dump($hour->isWork('00:00:00'));
  echo "</pre>";


  echo "<pre>";
  var_dump($hour->toWork('12:59'));
  echo "</pre>";


  echo "<pre>";
  var_dump($hour->toShow('12:59:59'));
  echo "</pre>";

  echo "<pre>";
  var_dump($hour->interval('00:00:00','23:59:59','00:59:00'));
  echo "</pre>";



