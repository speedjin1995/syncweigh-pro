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

function parseDate(dateStr) {
    let parts = dateStr.split(" ");
    let datePart = parts[0].split("/"); // [19, 03, 2025]
    let timePart = parts[1].split(":"); // [21, 21, 15]
    
    let formattedDate = `${datePart[2]}-${datePart[1]}-${datePart[0]}T${parts[1]}`; // YYYY-MM-DDTHH:mm:ss
    return new Date(formattedDate);
}

function formatDateStr(dateStr) {
    let dateObj = new Date(dateStr);
    if (isNaN(dateObj)) return null;

    let day = dateObj.getDate().toString().padStart(2, '0');
    let month = (dateObj.getMonth() + 1).toString().padStart(2, '0');
    let year = dateObj.getFullYear();

    let hours = dateObj.getHours().toString().padStart(2, '0'); // keep 24-hour format
    let minutes = dateObj.getMinutes().toString().padStart(2, '0');
    let seconds = dateObj.getSeconds().toString().padStart(2, '0');

    // AM or PM based on 24-hour hours
    let ampm = (dateObj.getHours() >= 12) ? 'PM' : 'AM';

    return `${day}/${month}/${year} ${hours}:${minutes}:${seconds} ${ampm}`;
}