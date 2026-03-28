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

$base = 'http://localhost:8000/api/values.php';
echo "Running Values API smoke tests against $base\n";

// Create
list($h,$r) = request('POST', $base, ['name'=>'TST-A']);
echo "POST -> $h\n$r\n";

list($h,$r) = request('GET', $base);
echo "GET -> $h\n$r\n";

// Parse list
$list = json_decode($r, true);
if (!is_array($list)) { echo "FAIL: GET did not return JSON list\n"; exit(2); }

echo "Found " . count($list) . " values\n";

// Update first item if exists
if (count($list)>0) {
    $id = $list[0]['id'];
    list($h,$r) = request('PUT', $base . '?id=' . urlencode($id), ['name'=>'TST-A-UPDATED','description'=>'desc']);
    echo "PUT -> $h\n$r\n";

    list($h,$r) = request('DELETE', $base . '?id=' . urlencode($id));
    echo "DELETE -> $h\n$r\n";
}

echo "Tests completed.\n";
