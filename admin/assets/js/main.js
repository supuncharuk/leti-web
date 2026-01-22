document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggle-sidebar');
  const closeBtn = document.getElementById('close-sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const toggleIcon = document.getElementById('toggle-icon');
  const body = document.body;

  function updateIcon() {
    if (!toggleIcon) return;
    if (body.classList.contains('sidebar-toggled')) {
      toggleIcon.classList.replace('fa-bars', 'fa-times');
    } else {
      toggleIcon.classList.replace('fa-times', 'fa-bars');
    }
  }

  function toggleSidebar() {
    body.classList.toggle('sidebar-toggled');
    updateIcon();
  }

  function closeSidebar() {
    body.classList.remove('sidebar-toggled');
    updateIcon();
  }

  if (toggleBtn) {
    toggleBtn.addEventListener('click', toggleSidebar);
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', closeSidebar);
  }

  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }
});

/**
 * Reusable SweetAlert Functions
 */

// Simple Alert
function showAlert(message, type = 'success', title = '', reload = true) {
  if (!title) {
    title = type.charAt(0).toUpperCase() + type.slice(1);
  }
  return Swal.fire({
    icon: type,
    title: title,
    text: message,
    confirmButtonColor: '#1e3c72',
    timer: 2000,
    timerProgressBar: true,
  }).then(() => {
    if (reload) {
      window.location.href = window.location.pathname;
    }
  });
}

// Confirmation Dialog
function showConfirm(
  message,
  callback,
  title = 'Are you sure?',
  confirmBtnText = 'Yes, do it!',
  type = 'warning',
) {
  Swal.fire({
    title: title,
    text: message,
    icon: type,
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: confirmBtnText,
  }).then((result) => {
    if (result.isConfirmed && typeof callback === 'function') {
      callback();
    }
  });
}

/**
 * Smart Delete Confirmation
 * Automatically redirects to the current page with ?delete=ID
 */
function confirmDelete(id, message = 'This item will be permanently removed!') {
  const page = window.location.pathname.split('/').pop();
  showConfirm(
    message,
    function () {
      window.location.href = page + '?delete=' + id;
    },
    'Are you sure?',
    'Yes, delete it!',
  );
}
