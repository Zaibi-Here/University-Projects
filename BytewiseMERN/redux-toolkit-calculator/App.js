import React, { useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { add, subtract, multiply, divide } from './features/calculator/calculatorSlice';

function App() {
  const dispatch = useDispatch();
  const value = useSelector((state) => state.calculator.value);
  const [input, setInput] = useState(0);

  return (
    <div style={{ textAlign: 'center', marginTop: '50px' }}>
      <h1>Redux Calculator</h1>
      <div>
        <input
          type="number"
          value={input}
          onChange={(e) => setInput(Number(e.target.value))}
        />
      </div>
      <div>
        <button onClick={() => dispatch(add(input))}>Add</button>
        <button onClick={() => dispatch(subtract(input))}>Subtract</button>
        <button onClick={() => dispatch(multiply(input))}>Multiply</button>
        <button onClick={() => dispatch(divide(input))}>Divide</button>
      </div>
      <h2>Result: {value}</h2>
    </div>
  );
}

export default App;
