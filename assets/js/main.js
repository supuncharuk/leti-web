/* Light Engineering Training Institute Ahangama - Main Scripts */

$(document).ready(function () {
  console.log('LETI Website Loaded');

  const itemsPerPage = 12;
  let currentPage = 1;
  let currentCategory = 'all';

  // Check for category in URL
  const urlParams = new URLSearchParams(window.location.search);
  const categoryParam = urlParams.get('category');
  if (categoryParam) {
    currentCategory = categoryParam;
    // Update active filter button
    $('.filter-btn').removeClass('active');
    $(`.filter-btn[data-filter="${currentCategory}"]`).addClass('active');
  }

  function renderNews() {
    // First identify which items match the filter
    let visibleItems = [];
    $('.news-item').each(function () {
      if (
        currentCategory === 'all' ||
        $(this).data('category') === currentCategory
      ) {
        visibleItems.push($(this));
      } else {
        $(this).hide();
      }
    });

    // Calculate pagination based on filtered items
    const totalItems = visibleItems.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    // Ensure currentPage is valid
    if (currentPage > totalPages) currentPage = 1;
    if (currentPage < 1) currentPage = 1;

    // Hide all passed filter check (we will show specific page slice next)
    visibleItems.forEach((item) => item.hide());

    // Calculate slice
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageItems = visibleItems.slice(startIndex, endIndex);

    // Show items for current page
    pageItems.forEach((item) => item.fadeIn('fast'));

    // Render Pagination Controls
    renderPagination(totalPages);
  }

  function renderPagination(totalPages) {
    const paginationContainer = $('#news-pagination');
    paginationContainer.empty();

    if (totalPages <= 1) return; // No pagination needed if only 1 page

    // Previous Button
    let prevDisabled = currentPage === 1 ? 'disabled' : '';
    paginationContainer.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" data-page="${
                  currentPage - 1
                }">Previous</a>
            </li>
        `);

    // Page Numbers
    for (let i = 1; i <= totalPages; i++) {
      let activeClass = i === currentPage ? 'active' : '';
      paginationContainer.append(`
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
    }

    // Next Button
    let nextDisabled = currentPage === totalPages ? 'disabled' : '';
    paginationContainer.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" data-page="${
                  currentPage + 1
                }">Next</a>
            </li>
        `);
  }

  // Filter Click Handler
  $('.filter-btn').click(function () {
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');

    currentCategory = $(this).data('filter');
    currentPage = 1; // Reset to page 1 on filter change
    renderNews();
  });

  // Pagination Click Handler
  $(document).on('click', '.page-link', function (e) {
    e.preventDefault();
    const page = parseInt($(this).data('page'));

    if (!isNaN(page)) {
      currentPage = page;
      renderNews();
      // Optional: Scroll to top of news grid
      $('html, body').animate(
        {
          scrollTop: $('#news-grid').offset().top - 100,
        },
        500
      );
    }
  });

  // Initial Render
  if ($('.news-item').length > 0) {
    renderNews();
  }
});
