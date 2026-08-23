<?php 
header('Content-Type: application/json');

$resultsFile = '../data/results.json';
if (!file_exists($resultsFile)) {
      echo json_encode([]);
      exit;
}

$results = json_decode(file_get_contents($resultsFile), true);

// Sort by percentage descending
usort($results, function ($a, $b) {
      return $b['percentage'] - $a['percentage'];
});

echo json_encode($results);

?>

