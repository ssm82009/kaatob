import { useState } from 'react'
import './App.css'
import Navbar from './components/Navbar'
import Footer from './components/Footer'
import { Link } from 'react-router-dom'

function App() {
  const [count, setCount] = useState(0)

  return (
    <>
      <Navbar />
      <main className="container">
        <div className="welcome-section">
          <h1>مرحباً بك في كاتب AI</h1>
          <p>أنشئ قصائد عربية كلاسيكية ونبطية باستخدام تقنية الذكاء الاصطناعي</p>
          <Link to="/generate" className="primary-button">
            اضغط هنا لبدء الإنشاء
          </Link>
        </div>
      </main>      <Footer />
    </>
  )
}

export default App
