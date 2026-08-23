<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$rawInput = file_get_contents('php://input');
if ($rawInput === false || trim($rawInput) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Input is empty']);
    exit;
}

$input = json_decode($rawInput, true);
if (!is_array($input) || !isset($input['answers']) || !is_array($input['answers'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Input is invalid']);
    exit;
}

$questionsFile = __DIR__ . '/../data/questions.json';
if (!file_exists($questionsFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Questions file not found']);
    exit;
}

$questions = json_decode(file_get_contents($questionsFile), true);
if (!is_array($questions)) {
    http_response_code(500);
    echo json_encode(['error' => 'Questions file is invalid']);
    exit;
}

$score = 0;
$total = count($questions);
$userAnswers = $input['answers'];

foreach ($questions as $q) {
    $qid = (string) $q['id'];
    if (array_key_exists($qid, $userAnswers) && (int) $userAnswers[$qid] === (int) $q['correct']) {
        $score++;
    }
}

$result = [
    'name' => isset($input['name']) && is_string($input['name']) ? trim($input['name']) ?: 'Anonymous' : 'Anonymous',
    'score' => $score,
    'total' => $total,
    'percentage' => $total > 0 ? round(($score / $total) * 100, 2) : 0,
    'timestamp' => date('Y-m-d H:i:s')
];

$resultsFile = __DIR__ . '/../data/results.json';
$results = [];
if (file_exists($resultsFile)) {
    $existingResults = json_decode(file_get_contents($resultsFile), true);
    if (is_array($existingResults)) {
        $results = $existingResults;
    }
}

$results[] = $result;
file_put_contents($resultsFile, json_encode($results, JSON_PRETTY_PRINT));

echo json_encode([
    'score' => $score,
    'total' => $total,
    'percentage' => $result['percentage']
]);
?>