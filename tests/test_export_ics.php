<?php
function request($method, $url, $data=null) {
    $opts = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
        ]
    ];
    if ($data !== null) $opts['http']['content'] = json_encode($data);
    $context = stream_context_create($opts);
    $res = @file_get_contents($url, false, $context);
    $info = $http_response_header[0] ?? '';
    return [$info, $res, $http_response_header ?? []];
}

$baseApi = 'http://localhost:8000/api/weekly_plan.php';
$exportUrl = 'http://localhost:8000/api/export_calendar.php?week_start=2026-03-28';

echo "Export ICS test against $exportUrl\n";

$entries = [
    ['week_start'=>'2026-03-28','day'=>'2026-03-28','social'=>'Call a friend','spiritual'=>'Prayer','physical'=>'Run','learning'=>'Read'],
    ['week_start'=>'2026-03-28','day'=>'2026-03-29','social'=>'Meet','spiritual'=>'Meditate','physical'=>'Yoga','learning'=>'Course']
];

list($h,$r) = request('POST', $baseApi, $entries);
echo "POST weekly_plan -> $h\n";

list($h,$r,$hdrs) = request('GET', $exportUrl);
echo "EXPORT GET -> $h\n";
// show important headers
foreach ($hdrs as $hh) {
    if (stripos($hh,'Content-Type:')===0 || stripos($hh,'Content-Disposition:')===0) echo "$hh\n";
}

if ($r === false) {
    echo "Failed to fetch export. Is the dev server running on localhost:8000?\n";
    exit(1);
}

// Print first 10 lines of ICS
$lines = preg_split('/\r?\n/', $r);
echo "--- ICS Preview ---\n";
for ($i=0;$i<min(10,count($lines));$i++) echo $lines[$i]."\n";

echo "Export ICS test complete.\n";
