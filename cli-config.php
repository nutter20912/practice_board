<?php
// cli-config.php
require_once "bootstrap/app.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);