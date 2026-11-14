</div><!-- /main-content -->

</div><!-- /page-wrapper -->

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<button class="sidebar-toggle" id="sidebarToggle">
    <i class="bi bi-list"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var toggle  = document.getElementById('sidebarToggle');
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    if (!toggle || !sidebar) return;
    function open()  { sidebar.classList.add('open');  if(overlay) overlay.classList.add('open'); }
    function close() { sidebar.classList.remove('open'); if(overlay) overlay.classList.remove('open'); }
    toggle.addEventListener('click', function() { sidebar.classList.contains('open') ? close() : open(); });
    if (overlay) overlay.addEventListener('click', close);

    // Marcar link activo
    var path = window.location.pathname;
    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        var href = link.getAttribute('href') || '';
        var page = href.split('/').pop().replace('.php','');
        if (page && path.includes(page)) link.classList.add('active');
    });
})();
</script>
</body>
</html>
