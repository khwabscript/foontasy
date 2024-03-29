<?php

return [
    'lineup_positions' => [
        '1:1' => 'GK', '2:2' => 'CB', '2:3' => 'CB', '3:3' => 'CM', '5:1' => 'CF', '5:2' => 'CF',
        2 => [
            3 => ['2:1' => 'LB'],
            4 => ['2:1' => 'LB', '2:4' => 'RB'],
            5 => ['2:1' => 'LWB', '2:4' => 'CB', '2:5' => 'RWB'],
        ],
        3 => [
            3 => ['3:1' => 'CM', '3:2' => 'CM'],
            4 => ['3:1' => 'LM', '3:2' => 'CM', '3:4' => 'RM'],
            5 => ['3:1' => 'LM', '3:2' => 'CM', '3:4' => 'CM', '3:5' => 'RM'],
            '1-4' => ['3:1' => 'CDM'],
            '1-3' => ['3:1' => 'CDM'],
            '2-2' => ['3:1' => 'CDM', '3:2' => 'CDM'],
            '2-3' => ['3:1' => 'CDM', '3:2' => 'CDM'],
            '3-1' => ['3:1' => 'CM', '3:2' => 'CM'],
            '3-3' => ['3:1' => 'CM', '3:2' => 'CM'],
            '4-1' => ['3:1' => 'LM', '3:2' => 'CM', '3:4' => 'RM'],
            '4-2' => ['3:1' => 'LM', '3:2' => 'CM', '3:4' => 'RM'],
            '5-1' => ['3:1' => 'LM', '3:2' => 'CM', '3:4' => 'CM', '3:5' => 'RM'],
        ],
        4 => [
            1 => ['4:1' => 'CF'],
            2 => ['4:1' => 'CF', '4:2' => 'CF'],
            3 => ['4:1' => 'LW', '4:2' => 'CF', '4:3' => 'RW'],
            '1-4' => ['4:1' => 'LM', '4:2' => 'CM', '4:3' => 'CM', '4:4' => 'RM'],
            '1-3' => ['4:1' => 'CM', '4:2' => 'CM', '4:3' => 'CM'],
            '2-2' => ['4:1' => 'LAM', '4:2' => 'CAM'],
            '2-3' => ['4:1' => 'LAM', '4:2' => 'CAM', '4:3' => 'RAM'],
            '3-1' => ['4:1' => 'CAM'],
            '3-3' => ['4:1' => 'LAM', '4:2' => 'CAM', '4:3' => 'RAM'],
            '4-1' => ['4:1' => 'CAM'],
            '4-2' => ['4:1' => 'CAM', '4:2' => 'CAM'],
            '5-1' => ['4:1' => 'CAM'],
        ],
        '3-4-2-1' => ['4:1' => 'CAM', '4:2' => 'CAM'],
        '3-2-4-1' => [
            '2:1' => 'CB',
            '3:1' => 'CDM', '3:2' => 'CDM',
            '4:1' => 'LAM', '4:2' => 'CAM', '4:3' => 'CAM', '4:4' => 'RAM',
        ],
        '4-2-3-1' => [
            '2:1' => 'LB', '2:4' => 'RB',
            '3:1' => 'CDM', '3:2' => 'CDM',
            '4:1' => 'LAM', '4:2' => 'CAM', '4:3' => 'RAM',
        ],
    ],
];
