<script>
    (function () {
        try {
            const saved = localStorage.getItem('atoz_theme');
            let theme;
            if (saved === 'light' || saved === 'dark') {
                theme = saved;
            } else {
                const app = document.documentElement.getAttribute('data-app');
                // Deterministic First-Run Defaults: Admin is light, Store and Auth are dark
                theme = (app === 'admin') ? 'light' : 'dark';
            }
            document.documentElement.setAttribute('data-theme', theme);
        } catch (e) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    })();
</script>
