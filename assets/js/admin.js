// Admin JavaScript
import React from 'react';
import ReactDOM from 'react-dom';

// Admin app component
const AdminApp = () => {
    return (
        <div className="wpsma-admin-app">
            <h1>Social Media Automation</h1>
            <button className="button button-primary">
                Schedule New Post
            </button>
        </div>
    );
};

// Render the app
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('wpsma-admin-root');
    if (container) {
        ReactDOM.render(<AdminApp />, container);
    }
});