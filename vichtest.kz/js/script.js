function answerCL(idQ) //, idA, ReqQuestion, ball) {
{
    if (Xchanger==1) {
        idA = XidA;
        ReqQuestion = XReqQuestion;
        ball = Xball;
        resultbal = resultbal + ball;
        document.getElementById("q" + idQ).style.display = "none";
        if (ReqQuestion > 0) {
            document.getElementById("q" + ReqQuestion).style.display = "block";
        } else {
            document.getElementById("qres").style.display = "block";
            let div = document.createElement('div');
            div.className = "red";
            //div.append(document.getElementById("qget"+idQ).innerText);
            //div.append(": ");
            //div.append(document.getElementById("a"+idA).value);
            div.append(resultbal);
            document.getElementById("qresinscontener").append(div);

        }
        Xchanger = 0;
    } else alert("Выберите один из ответов!");
}

function answerSelect(idQ, idA, ReqQuestion, ball) {
    XidQ=idQ;
    XidA=idA;
    XReqQuestion=ReqQuestion;
    Xball=ball;
    Xchanger=1;
}

function startOpros(idQ) {
    let str="q"+idQ;
    document.getElementById(str).style.display="block";
}