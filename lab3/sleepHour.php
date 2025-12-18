<?php
  $sleepHour = 8;
  echo "Sleep Duration: " . $sleepHour . " hours<br>";

  if ($sleepHour < 6){
    $energyLevel = "Low";
  } elseif ($sleepHour <= 9) {
    $energyLevel = "Medium";
  } else {
    $energyLevel = "High";
  }

  switch ($energyLevel) { 
    case "Low":
        echo "Status: Low Sleep (Too little sleep, you may feel tired)";
        break;

    case "Medium":
        echo "Status: Medium Sleep (Healthy amount of sleep)";
        break;

    case "High":
        echo "Status: High Sleep (Too much sleep may cause you a headache)";
        break;

    default:
        echo "Status: Unknown sleep level";
  }
?>