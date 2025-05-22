import React from 'react';
import { Link } from 'react-router-dom';

const Navbar = () => {
  return (
    <header>
      <div className="container">
        <nav>
          <div className="logo">كاتب AI</div>
          <div className="nav-links">
            <Link to="/">الرئيسية</Link>
            <Link to="/generate">إنشاء قصيدة</Link>
            <Link to="/admin/settings/ai">إعدادات الذكاء الاصطناعي</Link>
            <Link to="/dashboard">لوحة التحكم</Link>
          </div>
        </nav>
      </div>
    </header>  );
};

export default Navbar;
