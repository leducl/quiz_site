function initQuiz(questions, quizId) {
  const header    = document.getElementById('quiz-header');
  const container = document.getElementById('quiz-container');
  let current = 0, score = 0;

  function renderQuestion() {
    const q = questions[current];
    // Affiche X sur Y
    header.textContent = `Question ${current + 1} sur ${questions.length}`;
    // Génère le HTML
    container.innerHTML = `
      <h3>${q.text}</h3>
      ${q.options.map(o => `
        <div class="option">
          <input type="radio" name="opt" id="opt${o.oid}" value="${o.label}">
          <label for="opt${o.oid}" data-label="${o.label}">
            ${o.label}. ${o.text}
          </label>
        </div>
      `).join('')}
      <button id="validate">Valider</button>
    `;
    document.getElementById('validate').addEventListener('click', checkAnswer);
  }

  function checkAnswer() {
    const sel = container.querySelector('input[name="opt"]:checked');
    if (!sel) return alert('Veuillez sélectionner une réponse.');

    // Désactive tous les choix
    container.querySelectorAll('input[name="opt"]').forEach(i => i.disabled = true);

    const q = questions[current];
    const chosenLabel = sel.value;

    // Sur toutes les options, on surligne en vert la bonne, et en rouge la fausse sélectionnée
    q.options.forEach(o => {
      const lbl = container.querySelector(`label[data-label="${o.label}"]`);
      if (o.is_correct) {
        lbl.classList.add('correct');
      }
      if (o.label === chosenLabel && !o.is_correct) {
        lbl.classList.add('wrong');
      }
    });

    // Incrémente le score si la réponse était correcte
    if (q.options.find(o => o.label === chosenLabel).is_correct) {
      score++;
    }

    // Transforme le bouton Valider en Suivant
    const btn = document.getElementById('validate');
    btn.textContent = 'Suivant';
    btn.id = 'next';
    btn.removeEventListener('click', checkAnswer);
    btn.addEventListener('click', nextQuestion);
  }

  function nextQuestion() {
    current++;
    if (current < questions.length) {
      renderQuestion();
    } else {
      finishQuiz();
    }
  }

  function finishQuiz() {
    header.textContent = 'Quiz terminé';
    container.innerHTML = `<h3>Terminé ! Score : ${score}/${questions.length}</h3>`;

    // Sauvegarde de la note
    fetch('submit_score.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `quiz_id=${quizId}&score=${score}`
    });

    container.insertAdjacentHTML('beforeend',
      '<p><a href="quizzes.php">← Retour à la liste</a></p>'
    );
  }

  // Démarre
  renderQuestion();
}
