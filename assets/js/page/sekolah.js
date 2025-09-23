let scrapingInterval = null;
let currentLogId = null;
let allUrls = [];
let filteredUrls = [];
let dataTable = null;

// Fungsi untuk highlight teks pencarian
function highlightText(text, searchTerm) {
  if (!searchTerm) return text;
  const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
  return text.replace(regex, '<span class="search-highlight">$1</span>');
}

// Fungsi untuk pencarian URL
function searchUrls(searchTerm) {
  searchTerm = searchTerm.toLowerCase().trim();
  if (!searchTerm) {
    filteredUrls = [...allUrls];
    displayFilteredUrls();
    $('#noResultsMessage').hide();
    $('#urlListContainer').show();
    return;
  }
  filteredUrls = allUrls.filter(url => {
    const description = (url.description || '').toLowerCase();
    const urlText = url.url.toLowerCase();
    return description.includes(searchTerm) || urlText.includes(searchTerm);
  });

  if (filteredUrls.length === 0) {
    $('#urlListContainer').hide();
    $('#noResultsMessage').show();
  } else {
    $('#noResultsMessage').hide();
    $('#urlListContainer').show();
    displayFilteredUrls(searchTerm);
  }
  updateModalFooter(filteredUrls.length, allUrls.length);
  updateSelectedCount();
}

// Fungsi untuk menampilkan URL yang telah difilter
function displayFilteredUrls(searchTerm = '') {
  const container = document.getElementById('urlListContainer');
  if (filteredUrls.length === 0) {
    container.innerHTML = '<div class="alert alert-info m-3"><i class="fas fa-info-circle mr-2"></i> Tidak ada URL yang cocok.</div>';
    return;
  }
  let html = '<div class="list-group list-group-flush">';
  filteredUrls.forEach((item, index) => {
    const shortUrl = item.url.length > 80 ? item.url.substring(0, 80) + '...' : item.url;
    const description = item.description || `URL ${index + 1}`;
    const highlightedDescription = highlightText(description, searchTerm);
    const highlightedUrl = highlightText(shortUrl, searchTerm);

    html += `
      <div class="list-group-item list-group-item-action border-0 url-item ${searchTerm ? 'search-match' : ''}" data-url-id="${item.id}">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input url-checkbox" id="url-${item.id}" value="${item.id}">
          <label class="custom-control-label w-100" for="url-${item.id}">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="font-weight-medium text-dark">${highlightedDescription}</div>
                <small class="text-muted d-block mt-1">
                  <i class="fas fa-link mr-1"></i> ${highlightedUrl}
                </small>
                <small class="badge badge-secondary">ID: ${item.id}</small>
              </div>
            </div>
          </label>
        </div>
      </div>`;
  });
  html += '</div>';
  container.innerHTML = html;
}

// Fungsi untuk memuat URL ke dalam Modal
function loadUrlsIntoModal() {
  const container = document.getElementById('urlListContainer');
  if (!container) {
    console.error('Container urlListContainer tidak ditemukan!');
    return;
  }
  $('#searchUrlInput').val('');
  $('#noResultsMessage').hide();
  container.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div><p class="mt-3 text-muted"><i class="fas fa-database"></i> Memuat URL aktif...</p></div>`;

  // Menggunakan AJAX untuk memuat URL
  $.ajax({
    url: 'get_active_urls.php',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      if (data.success) {
        allUrls = data.urls || [];
        filteredUrls = [...allUrls];
        displayUrls(data);
      } else {
        displayError(new Error(data.message || 'Gagal memuat URL.'), container);
      }
    },
    error: function(xhr, status, error) {
      console.error('Error dalam fetch URL:', error);
      displayError(new Error('Terjadi kesalahan saat memuat URL: ' + error), container);
    }
  });
}

function displayUrls(data) {
  const container = document.getElementById('urlListContainer');
  if (!data.urls || data.urls.length === 0) {
    container.innerHTML = `<div class="alert alert-info m-3"><i class="fas fa-info-circle mr-2"></i> ${data.message || 'Tidak ada URL aktif ditemukan.'}</div>`;
    updateModalFooter(0, 0);
    return;
  }
  displayFilteredUrls();
  updateModalFooter(data.urls.length, data.urls.length);
  updateSelectedCount();
}

function displayError(error, container) {
  const errorMessage = error.message || 'Terjadi kesalahan tidak diketahui';
  container.innerHTML = `<div class="alert alert-danger m-3">
    <h6 class="alert-heading">Gagal Memuat URL</h6>
    <p>${errorMessage}</p>
    <button class="btn btn-sm btn-outline-danger" onclick="loadUrlsIntoModal()">
      <i class="fas fa-redo mr-1"></i> Coba Lagi
    </button>
  </div>`;
  updateModalFooter(0, 0);
}

function updateModalFooter(visibleCount, totalCount) {
  const infoElement = document.querySelector('#scrapingModal .url-count-info');
  if (!infoElement) return;

  if (totalCount > 0) {
    if (visibleCount === totalCount) {
      infoElement.innerHTML = `<i class="fas fa-link text-success"></i> Total: <strong>${totalCount}</strong> URL aktif`;
    } else {
      infoElement.innerHTML = `<i class="fas fa-search text-info"></i> Menampilkan: <strong>${visibleCount}</strong> dari <strong>${totalCount}</strong> URL`;
    }
  } else {
    infoElement.innerHTML = `<i class="fas fa-exclamation-triangle text-warning"></i> Tidak ada URL aktif`;
  }
}

function updateSelectedCount() {
  const selectedCount = $('.url-checkbox:checked').length;
  const visibleCount = $('.url-checkbox:visible').length;
  const btnSelected = $('#btnStartScrapingSelected');

  if (selectedCount > 0) {
    btnSelected.html(`<i class="fas fa-tasks"></i> Update Terpilih (${selectedCount})`).prop('disabled', false);
  } else {
    btnSelected.html(`<i class="fas fa-tasks"></i> Update Terpilih`).prop('disabled', true);
  }

  const checkAllElement = $('#checkAllUrls');
  if (checkAllElement.length && visibleCount > 0) {
    checkAllElement.prop('indeterminate', selectedCount > 0 && selectedCount < visibleCount);
    checkAllElement.prop('checked', selectedCount > 0 && selectedCount === visibleCount);
  } else {
    checkAllElement.prop('checked', false);
  }
}

function startScraping(mode) {
  let selectedUrlIds = [];
  if (mode === 'selected') {
    $('.url-checkbox:checked').each(function() {
      selectedUrlIds.push(parseInt($(this).val()));
    });
    if (selectedUrlIds.length === 0) {
      iziToast.warning({
        title: 'Tidak Ada Pilihan',
        message: 'Silakan pilih setidaknya satu URL.',
        position: 'topCenter'
      });
      return;
    }
  }

  const confirmationMessage = mode === 'selected' ?
    `Anda akan mengupdate data dari ${selectedUrlIds.length} URL terpilih. Lanjutkan?` :
    'Yakin ingin memulai update untuk SEMUA URL aktif?';

  if (!confirm(confirmationMessage)) return;

  $('#scrapingModal').modal('hide');
  setTimeout(() => {
    showProcessingState();
    
    // Menggunakan AJAX untuk menjalankan scraper
    $.ajax({
      url: 'run_scraper.php',
      type: 'POST',
      dataType: 'json',
      contentType: 'application/json',
      data: JSON.stringify({
        mode: mode,
        urls: selectedUrlIds
      }),
      success: function(data) {
        if (data.success) {
          currentLogId = data.log_id;
          iziToast.success({
            title: 'Proses Dimulai',
            message: data.message,
            position: 'topCenter'
          });
          monitorProgress(data.log_id);
        } else {
          showError('Gagal memulai proses: ' + data.message);
        }
      },
      error: function(xhr, status, error) {
        showError('Terjadi kesalahan teknis: ' + error);
      }
    });
  }, 300);
}

function stopScraping() {
  if (!currentLogId) return;
  if (confirm('Apakah Anda yakin ingin menghentikan proses scraping?')) {
    const btnCancel = document.getElementById('btnCancel');
    if (btnCancel) {
      btnCancel.disabled = true;
      btnCancel.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghentikan...';
    }
    
    // Menggunakan AJAX untuk menghentikan scraper
    $.ajax({
      url: 'stop_scraper.php',
      type: 'POST',
      dataType: 'json',
      contentType: 'application/json',
      data: JSON.stringify({
        log_id: currentLogId
      }),
      success: function(data) {
        if (data.success) {
          iziToast.success({
            title: 'Proses Dihentikan',
            message: data.message,
            position: 'topCenter'
          });
        } else {
          iziToast.error({
            title: 'Gagal Menghentikan',
            message: data.message,
            position: 'topCenter'
          });
          if (btnCancel) {
            btnCancel.disabled = false;
            btnCancel.innerHTML = '<i class="fas fa-stop"></i> Batal';
          }
        }
      },
      error: function(xhr, status, error) {
        console.error('Error stopping scraper:', error);
        iziToast.error({
          title: 'Error',
          message: 'Gagal menghentikan: ' + error,
          position: 'topCenter'
        });
        if (btnCancel) {
          btnCancel.disabled = false;
          btnCancel.innerHTML = '<i class="fas fa-stop"></i> Batal';
        }
      }
    });
  }
}

function monitorProgress(logId) {
  if (scrapingInterval) clearInterval(scrapingInterval);
  scrapingInterval = setInterval(() => {
    // Menggunakan AJAX untuk memeriksa progress
    $.ajax({
      url: `check_scraping_progress.php?log_id=${logId}`,
      type: 'GET',
      dataType: 'json',
      success: function(data) {
        if (data.success) {
          updateProgress(data);
          if (['completed', 'failed', 'cancelled'].includes(data.status)) {
            clearInterval(scrapingInterval);
            scrapingInterval = null;
            currentLogId = null;
            if (data.status === 'completed') {
              showSuccess('Proses scraping berhasil diselesaikan!');
              setTimeout(() => {
                if (confirm('Proses selesai! Muat ulang halaman untuk melihat data terbaru?')) {
                  location.reload();
                } else {
                  resetButton();
                }
              }, 1000);
            } else if (data.status === 'cancelled') {
              showSuccess('Proses scraping telah dibatalkan.');
              setTimeout(resetButton, 2000);
            } else {
              showError('Proses scraping gagal: ' + (data.error_message || 'Unknown error'));
            }
          }
        } else {
          throw new Error(data.message);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error monitoring progress:', error);
        iziToast.error({
          title: 'Error Monitoring',
          message: error,
          position: 'topCenter'
        });
        clearInterval(scrapingInterval);
        resetButton();
      }
    });
  }, 3000);
}

function checkRunningProcess() {
  // Menggunakan AJAX untuk memeriksa proses yang berjalan
  $.ajax({
    url: 'check_scraping_progress.php?log_id=0',
    type: 'GET',
    dataType: 'json',
    success: function(data) {
      if (data.success && data.status === 'running') {
        currentLogId = data.log_id;
        showProcessingState();
        monitorProgress(data.log_id);
        iziToast.info({
          title: 'Monitoring Dimulai',
          message: 'Memantau proses yang sedang berjalan...',
          position: 'topCenter'
        });
      } else {
        iziToast.warning({
          title: 'Tidak Ada Proses',
          message: 'Tidak ada proses scraping yang sedang berjalan.',
          position: 'topCenter'
        });
        resetButton();
      }
    },
    error: function(xhr, status, error) {
      showError('Error checking process: ' + error);
    }
  });
}

function updateProgress(data) {
  const percentage = data.progress_percentage || 0;
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  const statusText = document.getElementById('statusText');
  if (progressBar && progressText && statusText) {
    progressBar.style.width = `${percentage}%`;
    progressBar.setAttribute('aria-valuenow', percentage);
    progressText.textContent = `${percentage}%`;
    let statusMessage = `Memproses: ${data.processed_urls} dari ${data.total_urls}`;
    if (data.success_count > 0) statusMessage += ` | Sukses: ${data.success_count}`;
    if (data.failed_count > 0) statusMessage += ` | Gagal: ${data.failed_count}`;
    statusText.textContent = statusMessage;
  }
}

function showProcessingState() {
  $('#btnUpdateData').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
  $('#btnCancel').show().prop('disabled', false).html('<i class="fas fa-stop"></i> Batal');
  $('#progressContainer').show();
}

function showSuccess(message) {
  $('#btnUpdateData').html('<i class="fas fa-check"></i> Selesai').removeClass('btn-warning').addClass('btn-success');
  iziToast.success({
    title: 'Sukses',
    message: message,
    position: 'topCenter'
  });
}

function showError(message) {
  resetButton();
  iziToast.error({
    title: 'Error',
    message: message,
    position: 'topCenter'
  });
}

function resetButton() {
  $('#btnUpdateData').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Update Data').removeClass('btn-success').addClass('btn-warning');
  $('#btnCancel').hide();
  $('#progressContainer').hide();
  updateProgress({
    progress_percentage: 0,
    processed_urls: 0,
    total_urls: 0,
    success_count: 0,
    failed_count: 0
  });
}

// Fungsi untuk memindahkan elemen DataTable ke container yang tepat
function moveDataTableElements() {
  // Pastikan container datatable-controls ada
  const controlsContainer = $('.datatable-controls');
  if (!controlsContainer.length) {
    console.error('Container .datatable-controls tidak ditemukan!');
    return;
  }
  
  // Kosongkan container terlebih dahulu
  controlsContainer.find('.row:first-child .col-md-6:first-child').empty();
  controlsContainer.find('.row:first-child .col-md-6:last-child').empty();
  controlsContainer.find('.row:last-child .col-12').empty();
  
  // Tampilkan dan pindahkan length control
  const lengthElement = $('#table-1_length');
  if (lengthElement.length) {
    lengthElement.show().detach().appendTo('.datatable-controls .row:first-child .col-md-6:first-child');
  }
  
  // Tampilkan dan pindahkan filter control  
  const filterElement = $('#table-1_filter');
  if (filterElement.length) {
    filterElement.show().detach().appendTo('.datatable-controls .row:first-child .col-md-6:last-child');
  }
  
  // Tampilkan dan pindahkan buttons
  const buttonsElement = $('.dt-buttons');
  if (buttonsElement.length) {
    buttonsElement.detach().appendTo('.datatable-controls .row:last-child .col-12');
  }
  
  // Tampilkan dan pindahkan info
  const infoElement = $('#table-1_info');
  if (infoElement.length) {
    infoElement.show().detach().appendTo('.datatable-footer .col-md-6:first-child');
  }
  
  // Tampilkan dan pindahkan pagination
  const paginateElement = $('#table-1_paginate');
  if (paginateElement.length) {
    paginateElement.show().detach().appendTo('.datatable-footer .col-md-6:last-child');
  }
}

$(document).ready(function() {
  // Inisialisasi tooltip Bootstrap
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });

  // Inisialisasi DataTables dengan konfigurasi custom
  dataTable = $('#table-1').DataTable({
    responsive: false, // Disable responsive karena kita handle manual
    scrollX: false, // Disable scrollX karena kita handle dengan CSS
    language: {
      url: "../../vendor/datatables/i18n/Indonesian.json"
    },
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
    columnDefs: [
      { 
        orderable: false, 
        targets: [0, -1] // No urut dan kolom aksi tidak bisa di-sort
      },
      {
        className: "text-center",
        targets: [0, -1] // No urut dan kolom aksi di tengah
      }
    ],
    order: [[2, 'asc']], // Default sort by nama sekolah
    // Konfigurasi DOM untuk menampilkan semua elemen
    dom: '<"top"Blf>rt<"bottom"ip><"clear">',
    buttons: [
      {
        extend: 'copy',
        text: '<i class="fas fa-copy"></i> Salin',
        className: 'btn btn-sm btn-secondary',
        exportOptions: {
          columns: ':visible:not(.not-export)'
        }
      },
      {
        extend: 'excel',
        text: '<i class="fas fa-file-excel"></i> Excel',
        className: 'btn btn-sm btn-success',
        exportOptions: {
          columns: ':visible:not(.not-export)'
        },
        title: 'Data Sekolah - ' + new Date().toLocaleDateString('id-ID')
      },
      {
        extend: 'pdf',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        className: 'btn btn-sm btn-danger',
        exportOptions: {
          columns: ':visible:not(.not-export)'
        },
        orientation: 'landscape',
        pageSize: 'A4',
        title: 'Data Sekolah - ' + new Date().toLocaleDateString('id-ID')
      },
      {
        extend: 'print',
        text: '<i class="fas fa-print"></i> Cetak',
        className: 'btn btn-sm btn-info',
        exportOptions: {
          columns: ':visible:not(.not-export)'
        },
        title: 'Data Sekolah - ' + new Date().toLocaleDateString('id-ID')
      },
      {
        extend: 'colvis',
        text: '<i class="fas fa-eye"></i> Kolom',
        className: 'btn btn-sm btn-primary',
        columns: ':not(.not-colvis)'
      }
    ],
    drawCallback: function() {
      // Re-initialize tooltips setelah draw
      $('[data-toggle="tooltip"]').tooltip();
      
      // Update nomor urut setelah pagination/search
      this.api().column(0, {page:'current'}).nodes().each(function(cell, i) {
        const info = dataTable.page.info();
        cell.innerHTML = info.start + i + 1;
      });
    },
    initComplete: function() {
      // Pindahkan elemen setelah inisialisasi dengan delay
      setTimeout(() => {
        moveDataTableElements();
        
        // Tambahkan class ke elemen
        $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari data sekolah...');
        $('.dataTables_length select').addClass('form-control');
        
        // Custom styling untuk pagination
        $('.dataTables_paginate .paginate_button').addClass('page-item').find('a').addClass('page-link');
        $('.dataTables_paginate .paginate_button.current').addClass('active');
        $('.dataTables_paginate .paginate_button.disabled').addClass('disabled');
      }, 500); // Tambahkan delay untuk memastikan elemen sudah ada
    }
  });

  // Event handler untuk modal scraping
  $('#scrapingModal').on('show.bs.modal', function() {
    $('#checkAllUrls').prop('checked', false).prop('indeterminate', false);
    loadUrlsIntoModal();
  });

  $('#scrapingModal').on('hidden.bs.modal', function() {
    $('.modal-backdrop').remove();
  });

  // Event handler untuk pencarian URL
  $('#searchUrlInput').on('input', function() {
    searchUrls($(this).val());
  });

  // Event handler untuk checkbox
  $(document).on('change', '#checkAllUrls', function() {
    $('.url-checkbox:visible').prop('checked', this.checked).trigger('change');
  });

  $(document).on('change', '.url-checkbox', function() {
    updateSelectedCount();
  });

  // Re-apply tooltips setelah DataTable redraw
  $('#table-1').on('draw.dt', function() {
    $('[data-toggle="tooltip"]').tooltip();
  });

  // Handle window resize untuk responsive
  $(window).on('resize', function() {
    if (dataTable) {
      dataTable.columns.adjust();
    }
  });
});

// Cleanup saat window ditutup
window.addEventListener('beforeunload', () => {
  if (scrapingInterval) clearInterval(scrapingInterval);
});