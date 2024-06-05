{
    document.addEventListener('DOMContentLoaded', async () => {
        const response = await fetch('/users/me');
        const data = await response.json();

        if (data['message'] === 'Unauthorized') {
            return;
        }

        document.getElementById('login-form-container').classList.add('hidden');
        document.getElementById('user-info-container').classList.remove('hidden');
        document.getElementById('logged-in-user-icon').setAttribute('src', data['icon_path']);
    });
}
