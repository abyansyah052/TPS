return [
    'optimize' => [
        'queue' => [
            'balance' => 'auto',
            'minProcesses' => 1, 
            'maxProcesses' => 3, 
            'tries' => 3,
            'timeout' => 60,
        ],
    ],
];
