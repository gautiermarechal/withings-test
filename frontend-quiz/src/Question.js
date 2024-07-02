import React, { useState, useEffect } from "react";
import LoadingSpinner from "./LoadingSpinner";
import { shuffleArray } from "./lib";

const Question = ({ question, onValidate, onNextQuestion }) => {
  const [selected, setSelected] = useState(null); // TODO select an answer
  const [allAnswers, setAllAnswers] = useState([]);
  const [isAnswered, setIsAnswered] = useState(false);
  const [isShowAnswer, setIsShowAnswer] = useState(false);

  useEffect(() => {
    if (question) {
      setAllAnswers(
        shuffleArray([...question.incorrectAnswers, question.correctAnswer])
      );
    }
  }, [question]);

  const handleClick = () => {
    setIsAnswered(true);
    onValidate({ question, selected });
  };

  const handleNextQuestion = () => {
    setIsAnswered(false);
    setSelected(null);
    onNextQuestion();
  };

  return !question ? (
    <LoadingSpinner />
  ) : (
    <div className="flex flex-col w-full justify-center items-center">
      <h1 className="text-xl font-bold">{question.question.text}</h1>
      <div className="grid grid-cols-2">
        {allAnswers.map((answer) => (
          <button
            key={answer}
            onClick={() => setSelected(answer)}
            disabled={isAnswered}
            className={`${
              selected === answer
                ? "bg-blue-500 text-white"
                : "bg-white text-gray-800"
            }
                ${
                  isAnswered && selected && answer === question.correctAnswer
                    ? "bg-green-500 text-white"
                    : ""
                }
                 p-2 m-2 rounded-md px-8 py-6`}
          >
            {answer}
          </button>
        ))}
      </div>
      <button
        className="bg-blue-800 p-4 text-white font-bold mt-5 rounded"
        onClick={() => {
          !isAnswered ? handleClick() : handleNextQuestion();
        }}
        disabled={!selected}
      >
        {!isAnswered ? "Validate" : "Next"}
      </button>
    </div>
  );
};

export default Question;
