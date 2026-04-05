{{-- Apply stored color scheme before CSS paint (P4-5). Mirrors resources/js/novel-theme.js --}}
<script>
(function () {
    try {
        var k = 'novel-color-scheme';
        var v = localStorage.getItem(k) || 'system';
        var dark = v === 'dark' || (v !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', dark);
    } catch (e) {}
})();
</script>
