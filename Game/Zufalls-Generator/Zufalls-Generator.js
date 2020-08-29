function Go(){
				var text = [
	document.getElementById("input1").value,
 document.getElementById("input2").value,
 document.getElementById("input3").value,
 document.getElementById("input4").value,
 document.getElementById("input5").value,
 document.getElementById("input6").value,
 document.getElementById("input7").value,
 document.getElementById("input8").value,
 document.getElementById("input9").value,
 document.getElementById("input10").value,
 document.getElementById("input11").value,
 document.getElementById("input12").value,
 document.getElementById("input13").value,
 document.getElementById("input14").value,
 document.getElementById("input15").value,
				];
				var rand1 = Math.round(Math.random() * (15 - 0)) + 0;
  random1 = text[rand1]

  var rand2 = Math.round(Math.random() * (15 - 0)) + 0;
  random2 = text[rand2]

  var rand3 = Math.round(Math.random() * (15 - 0)) + 0;
  random3 = text[rand3]

  var rand4 = Math.round(Math.random() * (15 - 0)) + 0;
  random4 = text[rand4]
				}


function action1(){
   document.getElementById("button1").innerHTML= random1
				}

function action2(){
				document.getElementById("button2").innerHTML= random2
				}

function action3(){
				document.getElementById("button3").innerHTML= random3
				}

function action4(){
				document.getElementById("button4").innerHTML= random4
				}
