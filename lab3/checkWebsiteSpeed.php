<?php
function checkWebsiteSpeed($loadTime) {
    if ($loadTime <= 2) {
        return "Fast";
    } elseif ($loadTime <= 5) {
        return "Normal";
    } else {
        return "Slow";
    }
}

$websiteLoadTime = 4;

echo "Website load time: " . $websiteLoadTime . " seconds<br>";
echo "Speed status: " . checkWebsiteSpeed($websiteLoadTime);
?>