<?php
header('Content-Type: application/json');

$resultsFile = __DIR__ . '/../data/results.json';
if (!file_exists($resultsFile)) {
    echo json_encode([]);
    exit;
}

$results = json_decode(file_get_contents($resultsFile), true);
if (!is_array($results)) {
    $results = [];
}

usort($results, function ($a, $b) {
    $left = isset($a['percentage']) ? (float) $a['percentage'] : 0;
    $right = isset($b['percentage']) ? (float) $b['percentage'] : 0;
    return $right <=> $left;
});

echo json_encode($results);
?>

