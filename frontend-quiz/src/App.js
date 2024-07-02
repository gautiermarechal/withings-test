import React, { useState, useEffect } from 'react';

import Question from './Question';

const App = () => {
	const [questions, setQuestions] = useState([]);
	const [currentQuestion, setCurrentQuestion] = useState(null);
	
	const fetchQuestions = async () => {
		const response = null // TODO: do the API call to Triva here
		setQuestions() // TODO keep questions from the API
		setCurrentQuestion() // TODO get the first question to display it
	};

	const nextQuestion = () => {
		// TODO : update score and go to next question
	}

	useEffect(() => {
		fetchQuestions();
	}, [])

	return(
		<div className="App">
			<div className="App-header">
				<h1>Quiz</h1>
			</div>

			<div className="App-content">
				<Question 
					question={currentQuestion}
					nextQuestion={nextQuestion}
				/>
			</div>
		</div> 
	);
}

export default App;