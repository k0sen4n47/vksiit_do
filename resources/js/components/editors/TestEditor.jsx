import React, { useState } from 'react';

const TestEditor = ({ onChange, onBack }) => {
    const [title, setTitle] = useState('');
    const [questions, setQuestions] = useState([{
        text: '',
        type: 'single',
        answers: [''],
        correctAnswer: 0
    }]);

    const handleQuestionChange = (index, field, value) => {
        const newQuestions = [...questions];
        newQuestions[index] = {
            ...newQuestions[index],
            [field]: value
        };
        setQuestions(newQuestions);
        handleChange();
    };

    const handleAnswerChange = (questionIndex, answerIndex, value) => {
        const newQuestions = [...questions];
        newQuestions[questionIndex].answers[answerIndex] = value;
        setQuestions(newQuestions);
        handleChange();
    };

    const addQuestion = () => {
        setQuestions([
            ...questions,
            {
                text: '',
                type: 'single',
                answers: [''],
                correctAnswer: 0
            }
        ]);
    };

    const removeQuestion = (index) => {
        const newQuestions = questions.filter((_, i) => i !== index);
        setQuestions(newQuestions);
        handleChange();
    };

    const addAnswer = (questionIndex) => {
        const newQuestions = [...questions];
        newQuestions[questionIndex].answers.push('');
        setQuestions(newQuestions);
        handleChange();
    };

    const removeAnswer = (questionIndex, answerIndex) => {
        const newQuestions = [...questions];
        newQuestions[questionIndex].answers = newQuestions[questionIndex].answers.filter((_, i) => i !== answerIndex);
        setQuestions(newQuestions);
        handleChange();
    };

    const handleChange = () => {
        onChange({
            title,
            questions
        });
    };

    return (
        <div className="space-y-6">
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Название теста
                </label>
                <input
                    type="text"
                    value={title}
                    onChange={(e) => {
                        setTitle(e.target.value);
                        handleChange();
                    }}
                    className="w-full px-3 py-2 border rounded-md"
                    placeholder="Введите название теста"
                />
            </div>

            {questions.map((question, questionIndex) => (
                <div key={questionIndex} className="border rounded-lg p-4">
                    <div className="flex justify-between items-start mb-4">
                        <h3 className="text-lg font-medium">Вопрос {questionIndex + 1}</h3>
                        <button
                            onClick={() => removeQuestion(questionIndex)}
                            className="text-red-500 hover:text-red-700"
                        >
                            Удалить вопрос
                        </button>
                    </div>

                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Текст вопроса
                            </label>
                            <input
                                type="text"
                                value={question.text}
                                onChange={(e) => handleQuestionChange(questionIndex, 'text', e.target.value)}
                                className="w-full px-3 py-2 border rounded-md"
                                placeholder="Введите текст вопроса"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">
                                Тип вопроса
                            </label>
                            <select
                                value={question.type}
                                onChange={(e) => handleQuestionChange(questionIndex, 'type', e.target.value)}
                                className="w-full px-3 py-2 border rounded-md"
                            >
                                <option value="single">Один правильный ответ</option>
                                <option value="multiple">Несколько правильных ответов</option>
                            </select>
                        </div>

                        <div>
                            <div className="flex justify-between items-center mb-2">
                                <label className="block text-sm font-medium text-gray-700">
                                    Варианты ответов
                                </label>
                                <button
                                    onClick={() => addAnswer(questionIndex)}
                                    className="text-blue-500 hover:text-blue-700"
                                >
                                    + Добавить ответ
                                </button>
                            </div>
                            {question.answers.map((answer, answerIndex) => (
                                <div key={answerIndex} className="flex items-center gap-2 mb-2">
                                    <input
                                        type={question.type === 'multiple' ? 'checkbox' : 'radio'}
                                        checked={question.correctAnswer === answerIndex}
                                        onChange={() => handleQuestionChange(questionIndex, 'correctAnswer', answerIndex)}
                                        className="form-checkbox h-4 w-4"
                                    />
                                    <input
                                        type="text"
                                        value={answer}
                                        onChange={(e) => handleAnswerChange(questionIndex, answerIndex, e.target.value)}
                                        className="flex-1 px-3 py-2 border rounded-md"
                                        placeholder={`Вариант ответа ${answerIndex + 1}`}
                                    />
                                    <button
                                        onClick={() => removeAnswer(questionIndex, answerIndex)}
                                        className="text-red-500 hover:text-red-700"
                                    >
                                        ✕
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            ))}

            <button
                onClick={addQuestion}
                className="w-full py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-gray-400 hover:text-gray-700"
            >
                + Добавить вопрос
            </button>
        </div>
    );
};

export default TestEditor; 