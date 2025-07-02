<?php
include '../src/mapgen.php';

$pagesArray = [
  [
    'loc' => 'http://example.com/page1',
    'lastmod' => '2020-12-14',
    'priority' => 0.8,
    'changefreq' => 'monthly',
  ],
  [
    'loc' => 'http://example.com/page2',
    'lastmod' => '2020-12-07',
    'priority' => 0.5,
    'changefreq' => 'monthly',
  ],
  [
    'loc' => 'http://example.com/page3',
    'lastmod' => '>2020-12-12',
    'priority' => 0.8,
    'changefreq' => 'monthly ',
  ],
  [
    'loc' => 'http://example.com/page4',
    'lastmod' => '2020-12-11',
    'priority' => 1,
    'changefreq' => 'always',
  ],
  [
    'loc' => 'http://example.com/page5',
    'lastmod' => '2020-12-12',
    'priority' => 0.1,
    'changefreq' => 'monthly',
  ],
  [
    'loc' => 'http://example.com/page6',
    'lastmod' => '2020-12-11',
    'priority' => 0.1,
    'changefreq' => 'monthly',
  ]
];

$format = 'json';
$srcDirectory = './folder/new'; 

$generator = new SiteMapGenerator($pagesArray, $format, $srcDirectory);
$generator->validateField($pagesArray);