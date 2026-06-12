        </main>
        <footer class="text-center py-3" style="color:#6c757d; font-size:13px; border-top:1px solid #e9ecef;">
            &copy; <?= date('Y') ?> Sabores & Recetas - Plataforma de Cursos
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset_url('js/main.js') ?>"></script>
<script>document.getElementById('menu-toggle')?.addEventListener('click', function(){ document.getElementById('sidebar').classList.toggle('show'); });</script>
</body>
</html>
