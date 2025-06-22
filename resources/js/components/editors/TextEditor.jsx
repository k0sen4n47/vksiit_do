import React, { useState } from 'react';

const TextEditor = ({ onChange, onBack }) => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');

    const handleChange = () => {
        onChange({
            title,
            description
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
                    className="w-full px-3 py-2 border rounded-md h-48"
                    placeholder="Введите описание задания"
                />
            </div>
        </div>
    );
};

export default TextEditor; 