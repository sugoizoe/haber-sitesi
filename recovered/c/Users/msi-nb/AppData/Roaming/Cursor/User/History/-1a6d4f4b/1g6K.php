    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Haber Sitesi</h5>
                    <p class="text-muted">Güncel haberler ve en son gelişmeler</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Kategoriler</h5>
                    <ul class="list-unstyled">
                        <?php
                        $stmt = $pdo->query("SELECT * FROM categories LIMIT 5");
                        $footer_categories = $stmt->fetchAll();
                        foreach ($footer_categories as $cat) {
                            echo '<li><a href="index.php?page=category&id=' . $cat['id'] . '" class="text-muted text-decoration-none">' . htmlspecialchars($cat['name']) . '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>İletişim</h5>
                    <p class="text-muted">Email: info@habersitesi.com</p>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center text-muted">
                <small>&copy; <?php echo date('Y'); ?> Haber Sitesi. Tüm hakları saklıdır.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

