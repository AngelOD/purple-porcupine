<?php

return [
    'sensorInfo' => [
        'co2' => [
            'dataType' => 'integer',
            'maxValue' => 8192,
            'minValue' => 0,
            'name' => 'co2',
            'unit' => 'ppm',
        ],

        'humidity' => [
            'dataType' => 'double',
            'maxValue' => 100.0,
            'minValue' => 0.0,
            'name' => 'humidity',
            'unit' => '%',
        ],

        'light' => [
            'dataType' => 'integer',
            'maxValue' => 1000,
            'minValue' => 0,
            'name' => 'light',
            'unit' => 'lux',
        ],

        'noise' => [
            'dataType' => 'integer',
            'maxValue' => 120,
            'minValue' => 30,
            'name' => 'noise',
            'unit' => 'dB',
        ],

        'pressure' => [
            'dataType' => 'double',
            'maxValue' => 1200.0,
            'minValue' => 300.0,
            'name' => 'pressure',
            'unit' => 'hPa',
        ],

        'temperature' => [
            'dataType' => 'double',
            'maxValue' => 100.0,
            'minValue' => 0.0,
            'name' => 'temperature',
            'unit' => '°C',
        ],

        'uv' => [
            'dataType' => 'integer',
            'maxValue' => 15,
            'minValue' => 0,
            'name' => 'uv',
            'unit' => 'index',
        ],

        'voc' => [
            'dataType' => 'integer',
            'maxValue' => 1187,
            'minValue' => 0,
            'name' => 'voc',
            'unit' => 'ppb',
        ],
    ],
];