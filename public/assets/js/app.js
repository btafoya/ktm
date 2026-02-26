$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    function showToast(message, type = 'success') {
        const toastContainer = $('#toastContainer');
        if (!toastContainer.length) {
            $('body').append('<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
        }

        const bgClass = type === 'success' ? 'text-bg-success' :
                        type === 'danger' ? 'text-bg-danger' :
                        type === 'warning' ? 'text-bg-warning' : 'text-bg-info';

        const toastHtml = `
            <div class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        const toastElement = $(toastHtml);
        $('#toastContainer').append(toastElement);
        const toast = new bootstrap.Toast(toastElement[0], { delay: 5000 });
        toast.show();

        toastElement.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }

    function showAlert(message, type = 'success') {
        showToast(message, type);
    }

    window.showAlert = showAlert;
    window.showToast = showToast;
});