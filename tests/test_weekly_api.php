<?php
function request($method, $url, $data=null) {
    $opts = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\n",
        ]
    ];
    if ($data !== null) $opts['http']['content'] = json_encode($data);
    $context = stream_context_create($opts);
    $res = @file_get_contents($url, false, $context);
    $info = $http_response_header[0] ?? '';
    return [$info, $res];
}

$base = 'http://localhost:8000/api/weekly_plan.php';
echo "Weekly API tests against $base\n";

// create a minimal plan with 2 entries
$entries = [
    ['week_start'=>'2026-03-28','day'=>'2026-03-28','social'=>'Call a friend','spiritual'=>'Prayer','physical'=>'Run','learning'=>'Read'],
    ['week_start'=>'2026-03-28','day'=>'2026-03-29','social'=>'Meet','spiritual'=>'Meditate','physical'=>'Yoga','learning'=>'Course']
];
list($h,$r) = request('POST', $base, $entries);
echo "POST -> $h\n$r\n";

list($h,$r) = request('GET', $base);
echo "GET -> $h\n$r\n";

echo "Weekly API tests done.\n";
