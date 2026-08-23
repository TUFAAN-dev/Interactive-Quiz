<?php
header('Content-Type: application/json');

$file = __DIR__ . '/../data/questions.json';
if (!file_exists($file)) {
    http_response_code(500);
    echo json_encode(['error' => 'Questions file not found']);
    exit;
}

$questions = json_decode(file_get_contents($file), true);
if (!is_array($questions)) {
    http_response_code(500);
    echo json_encode(['error' => 'Questions file is invalid']);
    exit;
}

echo json_encode($questions);
?>