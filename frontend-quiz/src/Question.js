import React, { useState, useEffect } from 'react';

const Question = (props) => {
	const [selected, setSelected] = useState(null) // TODO select an answer

	const handleClick = () => {
		// TODO check answer selected and call next question
		// TODO highlight good answer
	};

	useEffect(() => {
		// TODO reset when question change
	}, []);

	return (
		<div className="question-container">
			<p>Question title</p>
			<div className="question-answers">
				<p>Answer 1</p>
				<p>Answer 2</p>
				<p>Answer 3</p>
				<p>Answer 4</p>
			</div>
			<button onClick={handleClick}>Validate</button>
		</div>
	)
}

export default Question;