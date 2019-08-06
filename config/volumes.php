<?php

$path = dirname(__DIR__) . '/web';

return [
  'images' => [
    'path' => $path . '/images',
    'url' => trim(getenv('BASE_URL'), '/') . '/images/',
  ],
  'files' => [
    'path' => $path . '/files',
    'url' => trim(getenv('BASE_URL'), '/') . '/files/',
  ],
];
