import React from 'react';
import { Link } from 'react-router-dom';

const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="footer">
      <div className="flex flex-col md:flex-row justify-center md:justify-between items-center gap-3 py-5 px-5 lg:px-7.5">
        <div className="flex order-2 md:order-1 gap-2 font-normal text-sm">
          <span className="text-muted-foreground">{currentYear} &copy;</span>
          <Link
            to="/"
            className="text-secondary-foreground hover:text-primary"
          >
            QuickDrive Inc.
          </Link>
        </div>
        <nav className="flex order-1 md:order-2 gap-4 font-normal text-sm text-muted-foreground">
          <Link
            to="/docs"
            className="hover:text-primary"
          >
            Docs
          </Link>
          <Link
            to="/pricing"
            className="hover:text-primary"
          >
            Pricing
          </Link>
          <Link
            to="/faq"
            className="hover:text-primary"
          >
            FAQ
          </Link>
          <a
            href="https://github.com"
            target="_blank"
            rel="noopener noreferrer"
            className="hover:text-primary"
          >
            Support
          </a>
          <Link
            to="/license"
            className="hover:text-primary"
          >
            License
          </Link>
        </nav>
      </div>
    </footer>
  );
};

export default Footer;