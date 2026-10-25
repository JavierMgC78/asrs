// ASRS - utilidades de sesión del lado cliente
document.addEventListener('DOMContentLoaded', () => {
    const logoutForm = document.getElementById('asrsLogoutForm');
    if (logoutForm) {
        logoutForm.addEventListener('submit', () => {
            sessionStorage.removeItem('asrs_validator');
            document.cookie = 'asrs_val=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT';
        });
    }
});
