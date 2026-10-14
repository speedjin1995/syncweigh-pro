function formatDate(date) {
    const day = ('0' + date.getDate()).slice(-2);
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const year = date.getFullYear();
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);
    const seconds = ('0' + date.getSeconds()).slice(-2);
    const ampm = date.getHours() >= 12 ? 'PM' : 'AM';

    //return `${day}/${month}/${year} ${hours}:${minutes}:${seconds} ${ampm}`;
    return `${year}-${month}-${day}`;
}

function formatDate2(date) {
    const day = ('0' + date.getDate()).slice(-2);
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const year = date.getFullYear();
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);
    const seconds = ('0' + date.getSeconds()).slice(-2);
    const ampm = date.getHours() >= 12 ? 'PM' : 'AM';

    //return `${day}/${month}/${year} ${hours}:${minutes}:${seconds} ${ampm}`;
    return `${day}-${month}-${year}`;
}

function formatDate3(date) {
    const day = ('0' + date.getDate()).slice(-2);
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const year = date.getFullYear();
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);
    const seconds = ('0' + date.getSeconds()).slice(-2);
    const ampm = date.getHours() >= 12 ? 'PM' : 'AM';

    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds} ${ampm}`;
}

function formatDate4(date) {
    const day = ('0' + date.getDate()).slice(-2);
    const month = ('0' + (date.getMonth() + 1)).slice(-2);
    const year = date.getFullYear();
    const hours = ('0' + date.getHours()).slice(-2);
    const minutes = ('0' + date.getMinutes()).slice(-2);

    return `${day}-${month}-${year} ${hours}:${minutes}`;
}

// Get current date/time in Malaysia timezone
function getMalaysiaDate() {
    return new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Kuala_Lumpur' }));
}

// Builds the "Columns" show/hide button for a DataTable and places it in #weightColumnsBtn
function initColumnsButton(dt) {
    new $.fn.dataTable.Buttons(dt, {
        dom: { button: { className: 'btn' } },
        buttons: [
            {
                extend: 'colvis',
                text: '<i class="ri-layout-column-line align-middle me-1"></i>Columns',
                className: 'btn-grey waves-effect waves-light',
                columns: ':not(.action-button, .select-checkbox)'
            }
        ]
    });
    dt.buttons().container().appendTo($('#weightColumnsBtn').empty());
}
