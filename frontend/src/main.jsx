import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter, Routes, Route } from 'react-router-dom'
import App from './App.jsx'
import PoemGenerator from './pages/PoemGenerator.jsx'
import AISettingsPage from './pages/Admin/AISettingsPage.jsx'
import './index.css'

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<App />} />
        <Route path="/generate" element={<PoemGenerator />} />
        <Route path="/admin/settings/ai" element={<AISettingsPage />} />
      </Routes>
    </BrowserRouter>
  </React.StrictMode>
)
