import React, { Suspense, lazy } from 'react';

const comp = lazy(() => import('./comp'));

const App = () => {
  return (
    <div>
      <h1>Welcome to my React App</h1>
      <Suspense fallback={<div>Loading...</div>}>
        <comp />
      </Suspense>
    </div>
  );
};

export default App;
