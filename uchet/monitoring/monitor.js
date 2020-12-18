function displayNon(x) {
            document.getElementById("comp"+x).style.display="none";
}

function StatusView(id, status, dt) {
    if (status==1) alert('Задача решена'); else alert('Обратить внимание');
    $.ajax({
        type:'POST',
        url:urli,
        data:{
            'dt':dt,
            'idcomp':id,
            'status': status,
            'operation':'show_masters'
        },
        success:function(html){
            document.getElementById('search-to-daten').value=parserDate(getMondayX(dt))+" - "+dt;
            $('#ajax').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}


function UpdatePosition(id, strstyle) {
    $.ajax({
        type:'POST',
        url:urli,
        data:{
            'idcomp':id,
            'strstyle':strstyle,
            'operation':'updatePos'
        },
        success:function(html){
            $('#ajax').html(html);
        },
        error:function(html){
            $('body').css('cursor','default');
            alert('Ошибка подключения!');
        },
    });
}


function ddrop(elid, posr, id) {
    var comp = document.getElementById(elid);
    var but=document.getElementById(posr);
    comp.style.cursor = 'default';
    but.style.color = "red";
    comp.onmousedown = function(e) {
        var coords = getCoords(comp);
        var shiftX = e.pageX - coords.left;
        var shiftY = e.pageY - coords.top;
        comp.style.position = 'absolute';
        //document.body.appendChild(comp);
        document.getElementById("conteinerxxx").append(comp);
        moveAt(e);
        comp.style.zIndex = 200;
        document.onmousemove = function(e) {
            moveAt(e);
        };
        function moveAt(e) {
            comp.style.left = e.pageX - shiftX + 'px';
            comp.style.top = e.pageY - shiftY + 'px';
        };

        comp.onmouseup = function() {
            but.style.color = "black";
            document.onmousemove = null;
            comp.onmouseup = null;
            comp.onmousedown=null;
            //comp.style.position = 'relative';
            //comp.style.left=0+'px';
            //comp.style.top=0+'px';
            comp.style.zIndex=100;
            var strstyle='left: '+comp.style.left+'; top: '+comp.style.top+"; ";
            UpdatePosition(id, strstyle)
        };

        comp.ondragstart = function() {
            return false;
        };

}}

function getCoords(elem) {   // кроме IE8-
    var box = elem.getBoundingClientRect();
    return {
        top: box.top + pageYOffset,
        left: box.left + pageXOffset
    }
}
