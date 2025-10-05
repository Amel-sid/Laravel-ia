import './bootstrap';

// Import global styles
import '../css/app.css';

// Global utilities
window.Policify = {
    // Global app utilities can go here
    version: '1.0.0',

    // Helper for showing notifications
    showNotification(message, type = 'info') {
        // Simple implementation - can be enhanced
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg z-50 ${
            type === 'success' ? 'bg-green-100 text-green-800' :
                type === 'error' ? 'bg-red-100 text-red-800' :
                    'bg-blue-100 text-blue-800'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
};
