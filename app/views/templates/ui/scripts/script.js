//function $(id){return document.getElementById(id)}
function log(d){window.console.log(d)}
function getForm(name){return document.forms[name]}
var isIE = (navigator.userAgent.indexOf('MSIE')>-1)? true  :false;

function removeAllOptions(ele){
 	var i;
 	ele = document.getElementById(ele);
	for(i=ele.options.length-1;i>=0;i--){ ele.remove(i);}
}

function appendOption(ele,txt,val){
  var o = document.createElement('option');
  var ele = document.getElementById(ele);
  o.text = txt;
  o.value = val;
  try {
  	ele.add(o, null); // standards compliant; doesn't work in IE
  } catch(ex){
  	ele.add(o); // IE only
  }
}

function update_skins(ele){
	var e = document.getElementById(ele);
	var theme = e.options[e.selectedIndex].value;  
	var theme_skin =skins[theme];
	removeAllOptions('skin_sel');
	for(i=0;i<theme_skin.length;i++){
		appendOption('skin_sel',theme_skin[i],theme_skin[i]);
	}
}

function redirectOnApproval(msg,url){
	if (confirm (unescape(msg))){
		location.href = url;
	}
}
window.onload = function(){
	if(isIE){
		inputs =[];
		i1= document.getElementsByTagName('input');
		for(i=0;i<i1.length;i++)inputs.push(i1[i]);
		for(i=0;i<inputs.length;i++){
			if( inputs[i].type =='text' || inputs[i].type =='password'){
				inputs[i].className= 'textbox';
			}
			if( inputs[i].type =='button' || inputs[i].type =='submit'){
				inputs[i].className= 'button';
			}
		}
	}
}