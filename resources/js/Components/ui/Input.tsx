import { forwardRef, InputHTMLAttributes } from 'react';

type InputType = 'text' | 'number';

interface InputProps extends Omit<InputHTMLAttributes<HTMLInputElement>, 'type'> {
    type?: InputType;
    label?: string;
    error?: string;
}

const Input = forwardRef<HTMLInputElement, InputProps>(
    ({ type = 'text', label, error, className = '', id, ...props }, ref) => {
        const inputId = id || props.name;
        const inputElement = (
            <input
                ref={ref}
                id={inputId}
                type={type}
                className={`input ${error ? 'input--error' : ''} ${className}`}
                {...props}
            />
        );

        // If no label and no error, render input directly without wrapper
        if (!label && !error) {
            return inputElement;
        }

        return (
            <div className="input-wrapper">
                {label && (
                    <label htmlFor={inputId} className="input-label">
                        {label}
                    </label>
                )}
                {inputElement}
                {error && <span className="input-error">{error}</span>}
            </div>
        );
    }
);

Input.displayName = 'Input';

export default Input;
