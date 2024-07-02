import React, { useState, useEffect } from "react";

import Question from "./Question";
import ScoreBoard from "./ScoreBoard";

const App = () => {
  const [questions, setQuestions] = useState([]);
  const [currentQuestion, setCurrentQuestion] = useState(null);

  const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
  const [score, setScore] = useState(0);

  const fetchQuestions = async () => {
    const response = await fetch(
      "https://the-trivia-api.com/v2/questions?limit=5"
    );
    setQuestions(await response.json());
  };

  const handleValidate = (args) => {
    // TODO : update score and go to next question
    const question = args.question;
    const selected = args.selected;

    if (selected === question.correctAnswer) {
      setScore(score + 1);
    }
  };

  const handleNextQuestion = () => {
    setCurrentQuestion(questions[currentQuestionIndex + 1]);
    setCurrentQuestionIndex(currentQuestionIndex + 1);
  };

  const handleResetState = () => {
    setScore(0);
    setCurrentQuestion(questions[0]);
    setCurrentQuestionIndex(0);

    fetchQuestions();
  };

  useEffect(() => {
    fetchQuestions();
  }, []);

  useEffect(() => {
    setCurrentQuestion(questions[0]);
    setCurrentQuestionIndex(0);
  }, [questions]);

  return (
    <div className="App">
      <div className="App-header">
        <h1>Quiz</h1>
      </div>

      <div className="App-content flex flex-col items-center justify-center min-h-[300px]">
        {currentQuestionIndex !== questions.length - 1 ? (
          <Question
            question={currentQuestion}
            onValidate={handleValidate}
            onNextQuestion={handleNextQuestion}
          />
        ) : (
          <ScoreBoard
            onRetry={handleResetState}
            score={score}
            total={questions.length}
          />
        )}
        {questions.length > 0 && (
          <span className="ml-auto">
            {currentQuestionIndex + 1} / {questions.length}
          </span>
        )}
      </div>
    </div>
  );
};

export default App;
