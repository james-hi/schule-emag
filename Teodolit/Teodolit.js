function Go(){
			
			var alpha = document.getElementById("alpha").value;
			
  var c = document.getElementById("c").value;
  
  var Addition = parseInt( document.getElementById("Addition").value)
  
  var Ergebnis = Math.round(Math.tan(alpha * (Math.PI / 180)) *c + Addition);
				
					document.getElementById("output").innerHTML = Ergebnis;
				};
