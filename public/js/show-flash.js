window.showFlash = function (type, message) {
    const container = document.getElementById('ajax-alert-container');
    if (!container) {
        console.error("Alert container missing! Add <div id='ajax-alert-container'></div> to your layout.");
        return;
    }

    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check' : 'fa-ban';

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow-sm">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas ${icon} mr-1"></i> ${message}
        </div>
    `;

    container.innerHTML = alertHtml;

    setTimeout(function () {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (el) {
            el.style.transition = 'opacity 0.5s, height 0.5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        });
    }, 4000);
};
