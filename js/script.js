let questions = [];
let timerInterval;
let timeLeft = 60;
let score = 0;

async function loadQuestions() {
  try {
    const response = await fetch('api/questions.php');
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    questions = await response.json();
    displayQuestions();
    startTimer();
  } catch (error) {
    const container = document.getElementById('quiz-container');
    container.innerHTML = '<p>Error loading questions.</p>';
    console.error('Error:', error);
  }
}

function displayQuestions() {
  const container = document.getElementById('quiz-container');
  container.innerHTML = '';

  questions.forEach((q) => {
    const questionDiv = document.createElement('div');
    questionDiv.className = 'question';

    const questionText = q.question || q.questions || `Question ${q.id}`;
    questionDiv.innerHTML = `<h3>${q.id}. ${questionText}</h3>`;

    const optionsDiv = document.createElement('div');
    optionsDiv.className = 'options';

    q.options.forEach((option, index) => {
      const label = document.createElement('label');
      label.innerHTML = `
        <input type="radio" name="q${q.id}" value="${index}" data-question-id="${q.id}">
        ${option}
      `;
      optionsDiv.appendChild(label);
    });

    questionDiv.appendChild(optionsDiv);
    container.appendChild(questionDiv);
  });
}

function startTimer() {
  clearInterval(timerInterval);
  timeLeft = 60;
  document.getElementById('time').textContent = timeLeft;

  timerInterval = setInterval(() => {
    timeLeft -= 1;
    document.getElementById('time').textContent = timeLeft;

    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      submitQuiz();
    }
  }, 1000);
}

document.addEventListener('change', function (event) {
  if (event.target.matches('input[type="radio"]')) {
    updateScore();
  }
});

function updateScore() {
  score = 0;

  questions.forEach((q) => {
    const selected = document.querySelector(`input[name="q${q.id}"]:checked`);
    if (selected && Number(selected.value) === Number(q.correct)) {
      score += 1;
    }
  });

  document.getElementById('score-value').textContent = score;
}

async function submitQuiz() {
  clearInterval(timerInterval);

  const answers = {};
  questions.forEach((q) => {
    const selected = document.querySelector(`input[name="q${q.id}"]:checked`);
    if (selected) {
      answers[q.id] = Number(selected.value);
    }
  });

  const name = prompt('Enter your name (or leave blank for Anonymous):');

  try {
    const response = await fetch('api/submit_quiz.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        name: name || 'Anonymous',
        answers: answers
      })
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.error || 'Submit failed');
    }

    const result = await response.json();
    displayResult(result);
  } catch (error) {
    console.error('Submit error:', error);
    alert('Error submitting quiz');
  }
}

function displayResult(result) {
  const resultDiv = document.getElementById('result');
  resultDiv.innerHTML = `
    <p>Your score: ${result.score} / ${result.total}</p>
    <p>Percentage: ${result.percentage}%</p>
    <a href="leaderboard.php">View Leaderboard</a>
  `;
  document.getElementById('controls').style.display = 'none';
}

document.getElementById('submit-btn').addEventListener('click', submitQuiz);
loadQuestions();


