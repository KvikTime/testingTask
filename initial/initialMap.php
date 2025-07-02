<?php
include '../src/mapgen.php';

$pagesArray = [
  [
    'loc' => 'http://example.com/page1',
    'lastmod' => '2020-12-14',
    'changefreq' => 'monthly',
    'priority' => 0.8,
  ],
  [
    'loc' => 'http://example.com/page2',
    'lastmod' => '2020-12-07',
    'changefreq' => 'monthly',
    'priority' => 0.5,
  ],
  [
    'loc' => 'http://example.com/page3',
    'lastmod' => '>2020-12-12',
    'changefreq' => 'priority',
    'priority' => 0.8,
  ],
  [
    'loc' => 'http://example.com/page4',
    'lastmod' => '2020-12-11',
    'changefreq' => 'priority',
    'priority' => 1,
  ],
  [
    'loc' => 'http://example.com/page5',
    'lastmod' => '2020-12-12',
    'changefreq' => 'priority',
    'priority' => 0.1,
  ],
  [
    'loc' => 'http://example.com/page6',
    'lastmod' => '2020-12-11',
    'changefreq' => 'priority',
    'priority' => 0.1,
  ]
];

$format = 'json';
$srcDirectory = './folder/new'; 

$generator = new SiteMapGenerator($pagesArray, $format, $srcDirectory);
$generator->validateField($pagesArray);