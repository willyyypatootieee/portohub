<?php

declare(strict_types=1);

use App\Core\Database;
use App\Repositories\PortfolioRepository;

require_once __DIR__ . '/config/site.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/repositories/PortfolioRepository.php';

loadEnvironmentFile(dirname(__DIR__) . '/.env');

$databaseConfig = require __DIR__ . '/config/database.php';
$database = Database::connect($databaseConfig);
$portfolioRepository = new PortfolioRepository($database);
