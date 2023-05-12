<?php

return [
    'MASTER_JSON_FILE'          => 'master.json',
    'JSON_FILE_PATH'            => 'app/server-data-json',
    'EXCELS_FILE_PATH'          => 'app/server-data',
    'PAGE_SIZE'                 => 10,
    'TB_GB_CONVERTION'          => 1024,
    'STORAGE_KEY_NAME'          => 'HDD',
    'RAM_KEY_NAME'              => 'RAM',
    'LOCATION_KEY_NAME'         => 'Location',
    'PREG_MATCH_STORAGE'        => '/(\d+(?:\.\d+)?)(GB|TB)/',
    'PREG_MATCH_RAM'            => '/(\d+(?:\.\d+)?)(GB)/',
    'CACHE_EXPIRATION_TIME'     => 1440, //1 day
    'CACHE_LOCATION_LIST'       => 'location_list',
];