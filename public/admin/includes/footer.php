        </div>
        <!-- End of Main Content -->
        
        <!-- Footer -->
        <footer class="sticky-footer bg-white">
            <div class="container">
                <div class="copyright text-center">
                    <span>Copyright &copy; Rankolab <?php echo date('Y'); ?></span>
                </div>
            </div>
        </footer>
        <!-- End of Footer -->
    </div>
    <!-- End of Content Wrapper -->
    
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Dropdown toggle
        document.querySelectorAll('.nav-item.dropdown .nav-link').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                this.parentElement.classList.toggle('show');
                const collapseElement = this.nextElementSibling;
                collapseElement.style.display = collapseElement.style.display === 'block' ? 'none' : 'block';
            });
        });
        
        // Automatically set collapse state based on active item
        document.querySelectorAll('.collapse-inner a').forEach(function(element) {
            if (element.classList.contains('active')) {
                element.closest('.collapse').style.display = 'block';
                element.closest('.nav-item.dropdown').classList.add('show');
            }
        });
        
        // Handle modal open/close
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }
        
        // Close modals when clicking outside
        document.querySelectorAll('.modal-backdrop').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });
        
        // Prevent form submission if validation fails
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if (!this.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
</body>
</html>