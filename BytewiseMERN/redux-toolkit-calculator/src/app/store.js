import { configureStore } from '@reduxjs/toolkit';
import calculatorReducer from '../features/calculator/calculatorSlice';

const store = configureStore({
  reducer: {
    calculator: calculatorReducer,
  },
});

export default store;
