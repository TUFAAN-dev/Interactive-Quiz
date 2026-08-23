<?php
header('Content-Type: applications/json');

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      echo json_encode(['error' => 'Method not allowed']);
      exit;
}

// Get the raw POST body and decode JSON
$input = json_encode(file_get_contents('php://input'), true);
if (!$input || !isset($input['answers'])) {
      http_response_code(400);
      echo json_encode(['error' => 'Input Invalid']);
      exit;
}

// Load questions
$questionsFile = '../data/questions.json';
if (!file_exists($questionsFile)) {
      http_response_code(500);
      echo json_encode(['error' => 'Questions file not found']);
      exit;
}
$questions = json_decode(file_get_contents($questionsFile), true);

// Calculate Score
$score = 0;
$total = count($questions);
$userAnswers = $input['answers'];

foreach ($questions as $q) {
      $qid = $q['id'];
      if (isset($userAnswers[$qid]) && $userAnswers[$qid] == $q['correct']) {
            $score++;
      }
}

// Prepare result entry
$result = [
      'name' => $input['name'] ?? 'Anonymous',
      'score'=> $score,
      'total'=> $total,
      'percentage'=> round(($score/$total) * 100, 2),
      'timestamp' => date('Y-M-D H:i:s')
];

// Save result to results.json
$resultsFile = '../data/results.json';
$results = [];
if (file_exists($resultsFile)) {
      $results = json_decode(file_get_contents($resultsFile), true);
      if (!is_array($results)) $results = [];
}
$results[] = $result;
file_put_contents($resultsFile, json_encode($results, JSON_PRETTY_PRINT));

// Return the results to the client
echo json_encode([
      'score' => $score,
      'total' => $total,
      'percentage' => $result['percentage']
]);

?>