
const localeId = {
    days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
    daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
    daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
    months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
    monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
    today: 'Hari Ini',
    clear: 'Bersihkan',
    dateFormat: 'yyyy-MM-dd',
    timeFormat: 'hh:ii aa',
    firstDay: 0
}
// new AirDatepicker('#human-friendly-picker1', {
//     range: true,
//     multipleDatesSeparator: ' - ',
//     locale: localeId,
// });

function singleDatePicker(elementId, selectDate = null) {
    return new AirDatepicker(elementId, {
        locale: localeId,
        selectedDates: selectDate ? [selectDate] : [],
        autoClose: true,
    });
}

function rangeDatePicker(elementId, startDate = null, endDate = null) {
    return new AirDatepicker(elementId, {
        range: true,
        multipleDatesSeparator: ' - ',
        locale: localeId,
        selectedDates: startDate && endDate ? [startDate, endDate] : [],
        autoClose: true,
    });
}

function monthPicker(elementId, selectDate) {
    return new AirDatepicker(elementId, {
        view: 'months',
        minView: 'months',
        dateFormat: 'yyyy-MM',
        locale: localeId,
        selectedDates: selectDate ? [selectDate] : [],
        autoClose: true,
    });
}
