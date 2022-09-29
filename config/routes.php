<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['licences/release/(:num)/(:any)'] = 'licence/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['licences/list'] = 'mylicence/list';
$route['licences/proposed/(:num)/(:any)'] = 'mylicence/show/$1/$2';
$route['licences/released/(:num)/(:any)'] = 'mylicence/show/$1/$2';
$route['licences/pdf/(:num)'] = 'mylicence/pdf/$1';

