import React from 'react';

const Footer = () => {
  return (
    <footer>
      <div className="container">
        <p>جميع الحقوق محفوظة &copy; {new Date().getFullYear()} كاتب AI</p>
      </div>
    </footer>
  );
};

export default Footer;
