let statusChoice = document.getElementById('status-choice');
if (statusChoice) {
    const statusChoice = new Choices('#status-choice', {
        placeholderValue: 'Select options',
        searchPlaceholderValue: 'Search...',
        removeItemButton: true,
        itemSelectText: 'Press to select',
    });
}


function singleChoiceSelect(selector, placeholder = 'Pilih salah satu') {
    return new Choices(selector, {
        placeholderValue: placeholder,
        searchPlaceholderValue: 'Cari ...',
        removeItemButton: true,
        itemSelectText: 'Klik untuk memilih',
    });
}

function multipleChoiceSelect(selector, placeholder = 'Pilih beberapa opsi') {
    return new Choices(selector, {
        placeholderValue: placeholder,
        searchPlaceholderValue: 'Cari ...',
        removeItemButton: true,
        itemSelectText: 'Klik untuk memilih',
    });
}
