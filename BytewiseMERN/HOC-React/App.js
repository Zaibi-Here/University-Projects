import React, { useState } from 'react';
import logg from './logg';
import comp from './comp';

const complogg = logg(comp);

const App = () => {
  const [name, setName] = useState('World');

  return (
    <div>
      <h1>Welcome to my React App</h1>
      <complogg name={name} />
      <button onClick={() => setName('React')}>Change Name</button>
    </div>
  );
};

export default App;
