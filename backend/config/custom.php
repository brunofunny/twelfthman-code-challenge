<?php

return [

    'images' => [
        "destinationPath" => public_path() . "/media/images/",
        "temporaryPath" => storage_path() . "/tmp/",
        "maxAllowedSize" => 5242880, // Default: 5242880 (5MB)
    ]

];
