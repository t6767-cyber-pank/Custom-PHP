/**Парсер дат**/
function parserDate(d, z='.')
{
    var month = d.getMonth()+1;
    var day = d.getDate();
    var year = d.getFullYear();
    if (z=='-') { var output = year + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day; }
    else { var output = (day<10 ? '0' : '')+day+"."+(month<10 ? '0' : '')+month+"."+year; }
    return output;
}

/**Взять понедельник**/
function getMonday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + 1; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

function getMondayX(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - 6; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять вторник**/
function getThusday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + 2; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять среду**/
function getWensday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + 3; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять четверг**/
function getThersday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + 4; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять пятницу**/
function getFriday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + 5; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять суботу**/
function getSaturday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + 6; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять воскресение**/
function getSunday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day+7; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

/**Взять прошлое воскресение**/
function getSundayLastWeek(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day; // adjust when day is sunday
    return new Date(d.setDate(diff));
}

