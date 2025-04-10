<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="logoutModalLabel">
          <i class="fas fa-sign-out-alt me-2"></i>Confirm Logout
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center py-4">
        <i class="fas fa-question-circle text-warning" style="font-size: 3rem;"></i>
        <h4 class="mt-3">Are you sure you want to logout?</h4>
        <p class="text-muted">You will need to login again to access your account.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Cancel
        </button>
        <!-- Updated redirect: now goes to index.php -->
        <a href="/lostfound/index.php" class="btn btn-danger">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Add this script to handle the logout button click -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Find all logout links
    const logoutLinks = document.querySelectorAll('a[href="../pages/logout.php"]');
    
    // Add click event listener to each logout link
    logoutLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            logoutModal.show(); // Show the modal
        });
    });
});
</script>
