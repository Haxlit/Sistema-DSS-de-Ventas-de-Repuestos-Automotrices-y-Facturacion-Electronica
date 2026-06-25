import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from './assets/vite.svg'
import heroImg from './assets/hero.png'
import ProductsTest from './components/ProductsTest';
import './App.css'

function App() {
  const [count, setCount] = useState(0)

  return (
    <>
      <ProductsTest />
    </>
  )
}

export default App
