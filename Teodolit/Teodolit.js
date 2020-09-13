function Go(){
			
			var alpha = document.getElementById("alpha").value;
			
  var c = document.getElementById("c").value;
  
  var Addition = parseInt(document.getElementById("Addition").value)
  
  var Berechnen = Math.tan(alpha * (Math.PI / 180))* c + (Addition / 100);
  
  var Round = Math.round(Berechnen *10);
				
				var Ergebnis = Round /10;
					document.getElementById("output").innerHTML = Ergebnis + " m";
				};
