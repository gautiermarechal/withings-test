import React from "react";

export default function ScoreBoard({ score, total, onRetry }) {
  return (
    <div className="flex flex-col justify-center items-center">
      <h1 className="text-2xl font-bold">
        Number of correct answers: {score} out of {total}
      </h1>
      <button
        className="bg-blue-800 p-4 text-white font-bold mt-5 rounded"
        onClick={onRetry}
      >
        Retry a new game
      </button>
    </div>
  );
}
