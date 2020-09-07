function Ask(){
				var input = document.getElementById("input").value;
				var user1 = "James";
				var user2 = "Werner";
				var user3 = "Clemens";
                                var user4 = "Tyson"
				var userGast = "Gast"
				if (input === user1 || input === user2 || input === user3 || input === userGast || input === user4){
							 window.location="Home.html";
							 
								}
					else {
									alert("Dieser User existiert leider nicht"); 
									}
				}

var input2 = document.getElementById("input").value;

function put(){
				
				document.getElementById("user").value = document.getElementById("input").value
};
