function $(x){
    switch (x[0]){
        case "#":
            var k=x.substr(1);
            return document.getElementById(k);
        case ".":
            var k=x.substr(1);
            return document.getElementsByClassName(k);
        default:
           return document.getElementsByTagName(x);
    }
}


//enough words to guess.
var rand=[
     "test",
     "ringe",
     "einfach",
     "tusinean",
     "upvote",
     "magie",
     "könig",
     "königin",
     "turm",
     "schach",
     "taschenrechner",
     "prüfungen",
     "hochschule",
     "denken",
     "süss",
     "dusche",
     "diamant",
     "gold",
     "teuer",
     "papier",
     "schere",
     "garten",
     "schule",
     "brieftasche",
     "glücklich",
     "wenig",
     "süßigkeiten",
     "schlafzimmer",
     "küche",
     "fußboden",
     "badezimmer",
     "universum",
     "sterne",
     "planeten",
     "galaxis",
     "biologie",
     "chemie",
     "physik",
     "informatik",
     "magenta",
     "lila",
     "gelb",
     "blau",
     "orange",
     "rumänien",
     "intensität",
     "clever",
     "kaktus",
     "schrecklich",
     "tolle",
     "großartig",
     "karten",
     "gewehr",
     "dinosaurier",
     "ziere",
     "elefant",
     "schlange",
     "gefängnis",
     "drachen",
     "tabelle",
     "kühlschrank",
     "eisen",
     "holz",
     "teppich",
     "banane",
     "codes",
     "programmierer",
     "wunderschönen",
     "schöpfer",
     "grusel",
     "wirklichkeit",
     "ananas",
     "donuts",
     "pizza",
     "schokolade",
     "kartoffeln",
     "hase",
     "karotte",
     "tomate",
     "blume",
     "haus",
     "lehrer",
     "hausaufgaben",
     "notizbuch",
     "komisch",
     "samstag",
     "sonntag",
     "montag",
     "freitag",
     "phantasie",
     "glauben",
     "erschrocken",
     "menschen",
     "kissen",
     "medizin",
     "bär",
     "konsole",
     "funktion",
     "ohrringe",
     "kaiser",
     "reich",
     "kaiserreich",
     "land",
     "wolf",
     "verwirrtheit",
     "geschicklichkeit",
     "welt",
     "weiß",
     "schwarz",
     "taube",
     "schwimmbad",
     "schwimmen",
     "brot",
     "türen",
     "berg",
     "ozean",
     "flaggen",
     "hubschrauber",
     "flugzeug",
     "fallschirm",
     "soldat",
     "armee",
     "treiber",
     "finsternis",
     "löwe",
     "tiger",
     "papier",
     "maus",
     "tastatur",
     "stuhl",
     "geräusche",
     "licht",
     "dunkelheit",
     "fee",
     "elf",
     "fernbedienung",
     "fenster",
     "computer",
     "ironman",
     "hulk",
     "rächer",
     "benutzerbild",
     "kapitän",
     "kette",
     "polizist",
     "feuerwehrmann",
     "müll",
     "carity",
     "sabotage",
     "filme",
     "nacht",
     "legende",
     "spiele",
     "erde",
     "sand",
     "wasser",
     "frau",
     "kind",
     "oberherr",
     "land",
     "wüste",
     "youtube",
     "facebook",
     "instagram",
     "snapchat",
     "google",
     "zombie",
     "skelett",
     "minecraft",
     "freunde",
     "liebe",
     "anime",
     "panter",
     "kleid",
     "hemd",
     "schwert",
     "kunst",
     "spitzhacke",
     "tränke",
     "schild",
     "agenten",
     "würste",
     "finden",
     "erklären",
     "fleisch",
     "zug",
     "fisch",
     "telefon",
     "flamme",
     "präsident",
     "erdbeere",
     "blaubeere",
     "schnee",
     "boot",
     "ziegel",
     "gemüse",
     "einheit",
     "rost",
     "stahl",
     "herz",
     "leistung",
     "fiktion",
     "natur"
    ];
//new words every hour.

var word=rand[Math.floor(Math.random()*rand.length)];

var end = word.split("");

var life= 9;

//function to check if word includes letter.
function tryIt(x){
    if (life >= 0){
        if (end.includes(x)){
                   let letter = $("#word");
            for (let i = 0; i < letter.children.length; i++) {
                if (word[i] == x) {
                    letter.children[i].innerText = x;
               }
            }
        }//if end does not include x.
        else{
            life--;
        }
    }//if life <= 0.
    else{
        win("Falsch!!!.\n\nTippe es nochmal zu versuchen","rgba(255 , 0 , 0 , 0.8)",0);
    }
    if (word == $("#word").innerText){
        win("Richtig 👍👍👍!\n\nTippe um nochmal zu spielen","rgba(0 , 255 , 0 , 0.9)",1)
    }
    man();
}

var place=["#543","green","gold","#666"]
window.onload=function(){
    $("#back").style.background=`linear-gradient(to bottom, white 5%, aqua 10%,aqua 50%, ${place[Math.floor(Math.random()*place.length)]} 50%)`;
    document.querySelectorAll('td').forEach(addEvent);
    //chosing the random word.
    theWord();
    setTimeout(load,3000);
}


function addEvent(td) {
    td.addEventListener('click', function(event){
        var tdClicked = event.target.innerText;
        tryIt(tdClicked);
        td.style.visibility="hidden";
    })
}


//function to add the letters of the word.
function theWord(){
    for (k=0; k< end.length; k++){
        const sp = document.createElement("span");
        $("#word").appendChild(sp);
        sp.innerText="_ ";
        //sp.innerText=end[k]
    }
}


//for replay
function replay(){
window.location="hangman.html";
}


//results
function win(y,z,q){
    $("#bar").style.top="10vh";
    $("#bar").innerText=y+"\n\n"+"Das Wort ist: "+ word
    $("#bar").style.background=z;
    if (q==0){
        $("#manHead").style.backgroundColor="red";
        $("#manArms").style.backgroundColor="red";
        $("#manBody").style.backgroundColor="red";
        $("#manLegs").style.backgroundColor="red"; 
    }
    else{
        $("#manHead").style.backgroundColor="black";
        $("#manArms").style.backgroundColor="black";
        $("#manBody").style.backgroundColor="black";
        $("#manLegs").style.backgroundColor="black"; 
    }
}


//function to show man.
function man(){
    switch (life){
        case 7:
            $("#rope").style.visibility="visible";
            break;
        case 6:
            $("#manHead").style.visibility="visible";
            break;
        case 5:
            $("#manBody").style.visibility="visible";
            break;
        case 4:
            $("#manArms").style.visibility="visible";
            break;
        case 3:
            $("#manLegs").style.visibility="visible";
            break;
        case 2:
            $("#fire2").style.visibility="visible";
            break;
        case 1:
            $("#fire").style.visibility="visible";
            break;
        case 0:
            $("#fire").style.transform="scale(1)";
            $("#fire2").style.transform="scale(1)";
            break;
    }
}

//function for loader
function load(){
    $("#loader").getElementsByTagName("p")[0].innerHTML="<span style=color:green;font-size:110%;>H</span><span style=color:red;>a</span><span style=color:blue;font-size:110%;>n</span><span style=color:purple;font-size:92%;>g</span><span style=color:green;>m</span><span style=color:red;font-size:113%;>a</span><span style=color:blue;font-size:105%;>n</span> <span style=color:gold;font-size:110%;>U</span><span style=color:blue;>n</span><span style=font-size:90%;color:magenta;>l</span><span style=color:gold;font-size:115%;>i</span><span style=color:green;>m</span><span style=color:gold;font-size:112%;>i</span><span style=color:red;font-size:115%;>t</span><span style=color:purple;>e</span><span style=color:aqua;>d</span><br><br>Loaded!";
    setTimeout(function(){
        $("#loader").style.opacity="0";
    },1000);
    setTimeout(function(){
        $("#loader").style.display="none";
    },2000);
}

//Created by Alex Tusinean

function back(){
				window.location.assign("https://james-hi.github.io/schule-emag/Game/Home.html")
				}
