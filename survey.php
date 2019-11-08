<HTML>
	<head>
		<script>
			var masterClassList={
				"9th":[
					{"name":"English","teachers":["Groveman","Rothstein"]},
					{"name":"Math","teachers":["Konzack","Nagar","Borenstein"]},
					{"name": "Navi", "teachers":["Moskovich","Frankel"]},
					{"names":["Engineering","ASL","Spanish"],"teachers":["Horstman","Karoll","Hugueno"]}
				],
				"10th":[
					{"name":"English","teachers":["Groveman","Rothstein"]},
					{"name":"Math","teachers":["Konzack","Nagar","Borenstein"]},
					{"name": "Navi", "teachers":["Moskovich","Frankel"]},
					{"names":["Engineering","ASL","Spanish"],"teachers":["Horstman","Karoll","Hugueno"]}
				],
				"11th":[
					{"name":"English","teachers":["Groveman","Rothstein"]},
					{"name":"Math","teachers":["Konzack","Nagar","Borenstein"]},
					{"name": "Navi", "teachers":["Moskovich","Frankel"]},
					{"names":["Engineering","ASL","Spanish"],"teachers":["Horstman","Karoll","Hugueno"]}
				],
				"12th":[
					{"name":"English","teachers":["Groveman","Rothstein"]},
					{"name":"Math","teachers":["Konzack","Nagar","Borenstein"]},
					{"name": "Navi", "teachers":["Moskovich","Frankel"]},
					{"names":["Engineering","ASL","Spanish"],"teachers":["Horstman","Karoll","Hugueno"]}
				]
			};
			var remainingPercentage=100;
			var firstName="";
			var lastName="";
			var gradeLevel="";
			function validateStudentInfo(){
				firstName=document.getElementById("firstName").value;
				lastName=document.getElementById("lastName").value;
				gradeLevel=document.getElementById("gradeLevel").value;
				document.getElementById("firstName").style.border="";
				document.getElementById("lastName").style.border="";
				document.getElementById("gradeLevel").style.border="";
				if(firstName===""||lastName===""||gradeLevel===""){
					if(firstName===""){
						document.getElementById("firstName").style.border="2px solid red";
					}
					if(lastName===""){
						document.getElementById("lastName").style.border="2px solid red";
					}
					if(gradeLevel===""){
						document.getElementById("gradeLevel").style.border="2px solid red";
					}
				}
				else{
					var grade = document.getElementById("gradeLevel").options[document.getElementById("gradeLevel").selectedIndex].value;
					lockInStudentInfo();
					generateClasses(grade);
				}
			}
			function lockInStudentInfo(){
				document.getElementById("studentInfoSubmission").hidden=true;
				document.getElementById("firstName").readOnly=true;
				document.getElementById("lastName").readOnly=true;
				document.getElementById("gradeLevel").disabled=true;
			}
			function generateClasses(grade){
				var classes=masterClassList[grade];
				for(var i= 0; i < classes.length;i++){
					var isElective = classes[i].name!==undefined ? false : true;
					if(isElective)
						addClassSelector(true,classes[i].names,classes[i].teachers);
					else
						addClassSelector(false,classes[i].name,classes[i].teachers);
				}
				document.getElementById("classInfo").style.display="block";
				//document.getElementById("electiveSelector").oninput=function(){updatePartner(this);};
			}
			function addClassSelector(isElective,className,teachers){
				var classesParentList=document.getElementById("classesList");
				var classesList=document.createElement("div");
				classesParentList.appendChild(classesList);
				if(isElective){
					var classSelector = document.createElement("select");
					classesList.appendChild(classSelector);
					classSelector.id="electiveSelector";
					classSelector.oninput=function(){updatePartner(this);unhideSiblings(this);};
					createElement("option","",false,classSelector);
					for(var i = 0; i < className.length; i++){
						createElement("option",className[i],false,classSelector);
					}
				}
				else {
					createElement("input",className,"readOnly",classesList);
				}
				var teacherSelector = document.createElement("select");
				if(isElective)teacherSelector.oninput=function(){updatePartner(this); unhideSiblings(this);};
				else teacherSelector.oninput=function(){unhideSiblings(this);};
				classesList.appendChild(teacherSelector);
				createElement("option","",false,teacherSelector);
				for(var j = 0; j < teachers.length; j++){
					createElement("option",teachers[j],false,teacherSelector);
				}

				var percent = createElement("input",0,"readOnly",classesList);
				percent.size=2;
				percent.id="percent";
				percent.hidden=true;
				var percentSign = createElement("span","%",false,classesList);
				percentSign.innerHTML="%";
				percentSign.hidden=true;

				var plusButton = document.createElement("button");
				classesList.appendChild(plusButton);
				plusButton.innerHTML="+";
				plusButton.onclick=function(){addPercentage(this);};
				plusButton.hidden=true;

				var minusButton = document.createElement("button");
				classesList.appendChild(minusButton);
				minusButton.innerHTML="-";
				minusButton.onclick=function(){subtractPercentage(this);};
				minusButton.hidden=true;

				var linebreak = document.createElement("br");
				classesList.appendChild(linebreak);
				var linebreak2 = document.createElement("br");
				classesList.appendChild(linebreak2);
			}
			function createElement(_type,_value,_readOnly,_parent){
				var element = document.createElement(_type);
				element.size=10;
				element.value=_value;
				if(_type==="option")element.innerHTML=_value;
				if(_readOnly==="readOnly")element.readOnly=true;
				_parent.appendChild(element);
				return element;
			}
			function updatePartner(sender){
				var selection = sender.selectedIndex;
				var parent = sender.parentNode;
				if(parent===undefined) return;
				var peers = parent.childNodes;
				for(var i = 0; i < peers.length; i++)
					peers[i].selectedIndex=selection;
			}
			function unhideSiblings(sender){
				var parent = sender.parentNode;
				if(parent===undefined) return;
				var peers = parent.childNodes;
				for(var i = 0; i < peers.length; i++)
					peers[i].hidden=false;	
			}
			function addPercentage(sender){
				if(remainingPercentage<=0)return;
				var siblings = sender.parentNode.childNodes;
				var percentCounter;
				for(var i = 0; i < siblings.length; i++){
					if(siblings[i].id==="percent"){
						percentCounter = siblings[i];
						break;
					}
				}
				var currentPercentage = parseInt(percentCounter.value);
				if(currentPercentage>=100)return;
				currentPercentage+=5;
				percentCounter.value=currentPercentage;
				remainingPercentage-=5;
				document.getElementById("counter").innerHTML="Remaining: "+remainingPercentage+"%";
				if(remainingPercentage===0){
					showFinalSubmitButton();
				}
			}
			function subtractPercentage(sender){
				if(remainingPercentage>=100)return;
				var siblings = sender.parentNode.childNodes;
				var percentCounter;
				for(var i = 0; i < siblings.length; i++){
					if(siblings[i].id==="percent"){
						percentCounter = siblings[i];
						break;
					}
				}
				var currentPercentage = parseInt(percentCounter.value);
				if(currentPercentage<=0)return;
				currentPercentage-=5;
				percentCounter.value=currentPercentage;
				remainingPercentage+=5;
				document.getElementById("counter").innerHTML="Remaining: "+remainingPercentage+"%";
				hideFinalSubmitButton();
			}
			function showFinalSubmitButton(){
				document.getElementById("surveyDone").hidden=false;
			}
			function hideFinalSubmitButton(){
				document.getElementById("surveyDone").hidden=true;
			}
			function submitSurvey(){
				alert("Info submitted!");
			}
		</script>
	</head>
	<body>
		<h2>I: Your info</h2>
		<div id="studentInfo">
			<label for="firstName">First name:</label>
			<input id="firstName">
		<br>
			<label for="lastName">Last name:</label>
			<input id="lastName">
		<br>
			<label for="gradeLevel">Grade:</label>
			<select id="gradeLevel">
				<option value=""></option>
				<option value="9th">9th</option>
				<option value="10th">10th</option>
				<option value="11th">11th</option>
				<option value="12th">12th</option>
			</select>
		<br>
			<input type="submit" id="studentInfoSubmission" onclick="validateStudentInfo()">
		</div>
		<div id="classInfo" style="display: none;">
			<h2>II: Your classes</h2>
			<p>What percentage of your work comes from each of your classes?</p>
			<b><div id ="counter">Remaining: 100%<br></div></b>
			<div id ="classesList"></div>
			<input type="submit" id="surveyDone" onclick="submitSurvey()" hidden>
		</div>
	</body>
</HTML>