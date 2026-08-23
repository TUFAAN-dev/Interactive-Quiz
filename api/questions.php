<?php

// Set JSN header
header('Content-Type: application/json');

// Read questions from the json file
$file = '../data/questions.json';
if (!file_exists($file)) {
      http_response_code(500);
      echo json_encode(['error' => 'Questions file not found']);
      exit;
}

$questions = json_decode(file_get_contents($file), true);

// Return all questions (or yu could return only a subset)
echo json_encode($questions);

?>