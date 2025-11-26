const commonAlertOptions = {
    customClass: {
        confirmButton: 'btn btn-primary',
        denyButton: 'btn btn-warning',
        cancelButton: 'btn btn-secondary'
    }
};

function successAlert(message, callback = null) {
    return Swal.fire({
        title: "Success!",
        text: message,
        icon: "success",
        ...commonAlertOptions
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

function errorAlert(message, callback = null) {
    return Swal.fire({
        title: "Error!",
        text: message,
        icon: "error",
        ...commonAlertOptions
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

function infoAlert(message) {
    return Swal.fire({
        title: "Info",
        text: message,
        icon: "info",
        ...commonAlertOptions
    });
}

function warningAlert(message) {
    return Swal.fire({
        title: "Warning!",
        text: message,
        icon: "warning",
        ...commonAlertOptions
    });
}

function confirmAlert(title, text, icon = "warning", callback = null) {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: "Ya, Lanjutkan",
        cancelButtonText: "Tidak, Batalkan",
        ...commonAlertOptions
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}
