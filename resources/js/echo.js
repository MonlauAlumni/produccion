
const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');

if (userId) {
    window.Echo.private(`user.${userId}`)
        .listen('.MessageSent', (e) => {
            console.log('📩 Mensaje recibido:', e.message);
          
        });
}
