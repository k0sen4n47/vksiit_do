import React, { useState } from 'react';
import MonacoEditor from '@monaco-editor/react';

const CodeEditor = ({ onChange, onBack }) => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [initialCode, setInitialCode] = useState('');
    const [expectedOutput, setExpectedOutput] = useState('');
    const [language, setLanguage] = useState('javascript');

    const handleChange = () => {
        onChange({
            title,
            description,
            initialCode,
            expectedOutput,
            language
        });
    };

    return (
        <div className="space-y-4">
            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Заголовок задания
                </label>
                <input
                    type="text"
                    value={title}
                    onChange={(e) => {
                        setTitle(e.target.value);
                        handleChange();
                    }}
                    className="w-full px-3 py-2 border rounded-md"
                    placeholder="Введите заголовок задания"
                />
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Описание задания
                </label>
                <textarea
                    value={description}
                    onChange={(e) => {
                        setDescription(e.target.value);
                        handleChange();
                    }}
                    className="w-full px-3 py-2 border rounded-md h-32"
                    placeholder="Опишите задание и требования к решению"
                />
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Язык программирования
                </label>
                <select
                    value={language}
                    onChange={(e) => {
                        setLanguage(e.target.value);
                        handleChange();
                    }}
                    className="w-full px-3 py-2 border rounded-md"
                >
                    <option value="javascript">JavaScript</option>
                    <option value="python">Python</option>
                    <option value="java">Java</option>
                    <option value="cpp">C++</option>
                </select>
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Начальный код
                </label>
                <div className="border rounded-md overflow-hidden">
                    <MonacoEditor
                        height="200px"
                        language={language}
                        value={initialCode}
                        onChange={(value) => {
                            setInitialCode(value);
                            handleChange();
                        }}
                        theme="vs-light"
                        options={{
                            minimap: { enabled: false },
                            scrollBeyondLastLine: false,
                            fontSize: 14,
                            lineNumbers: 'on',
                            automaticLayout: true
                        }}
                    />
                </div>
            </div>

            <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                    Ожидаемый вывод
                </label>
                <textarea
                    value={expectedOutput}
                    onChange={(e) => {
                        setExpectedOutput(e.target.value);
                        handleChange();
                    }}
                    className="w-full px-3 py-2 border rounded-md h-32 font-mono"
                    placeholder="Введите ожидаемый вывод программы"
                />
            </div>
        </div>
    );
};

export default CodeEditor; 