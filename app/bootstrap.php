<?php

declare(strict_types=1);

use App\Repositories\PortfolioRepository;

require_once __DIR__ . '/config/site.php';
require_once __DIR__ . '/repositories/PortfolioRepository.php';
require_once dirname(__DIR__) . '/koneksi.php';

$portfolioRepository = new PortfolioRepository($koneksi);
