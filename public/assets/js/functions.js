console.log("Hey There Partner! Want a hire?");

function ValidateEpicU() {
    var str = document.getElementById("username").value;
    var res = str.match(/^[a-zA-Z\d]{3,20}$/);
    if (str === res?.[0]) {
        document.getElementById("username").classList.remove("border-red-500");
        document.getElementById("username").classList.add("border-green-500");
        console.log("Border Changed to Green - Will Check IF in EC DB Soon");
    } else {
        document.getElementById("username").classList.remove("border-green-500");
        document.getElementById("username").classList.add("border-red-500");
        console.log("Border Changed to Red - Wrong Syntax");
    }
}

function GoToM() {
    window.location = '/messages';
}

function goBack() {
    window.history.back();
}

var gear = 0;
function Gear() {
    if (gear === 1) {
        document.getElementById("backDrop").style.display = 'none';
        document.getElementById("gearBox").style.display = 'none';
        gear -= 1;
    } else {
        document.getElementById("backDrop").style.display = 'block';
        document.getElementById("gearBox").style.display = 'block';
        gear++;
    }
}