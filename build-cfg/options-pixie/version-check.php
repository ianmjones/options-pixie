<?php
$version_checks = array(
	"$plugin_slug.php" => array( '@Version:\s+(.*)\n@' => 'header' ),
	"includes/class-$plugin_slug.php" => array( "@\\\$this->version\\s+=\\s+'(.*?)';@" => 'Class variable' ),
);
