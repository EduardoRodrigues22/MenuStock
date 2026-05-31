(function () {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        window.setTimeout(function () {
            alert.style.transition = 'opacity 500ms ease';
            alert.style.opacity = '0';
            window.setTimeout(function () {
                alert.style.display = 'none';
            }, 500);
        }, 2500);
    });

    var reservaData = document.querySelector('input[type="date"][name="data"]');
    if (reservaData && !reservaData.value) {
        var today = new Date();
        today.setMinutes(today.getMinutes() - today.getTimezoneOffset());
        reservaData.value = today.toISOString().slice(0, 10);
    }
})();
