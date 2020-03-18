var pages=1; //sign page number
var data=new Array(); //store passages into an array
var departments="%";

function loadJson(pages){ //get json file from php
	$.ajax({
	 type: "get",
	 url: "http://47.88.225.51/api/v1/getNews.php?limit=20&page="+pages+"&department="+departments,
	 dataType: "json",
	 async: false,
	 success: function(json){
	     for (let i = 0; i <json.data.length; i++) {
	         data[i]=json.data[i];
	     }
	 }
	});
}
loadJson(1); //get first page information

window.onload=function(){
	//get nodes
    var sections=document.getElementsByClassName("section");
    var floatlayer=document.getElementById("fl");
    var closeSign=document.getElementById("ct");
    var passageContent=document.getElementById("pc");
    var sBottoms=document.getElementsByClassName("s-bottom");
    var pre=document.getElementsByClassName("previous");
    var next=document.getElementsByClassName("next");
    var select=document.getElementsByClassName("category-select");
    var selectList=document.getElementsByClassName("select-list");
    var list=document.getElementById("select-list");
    var categoryCurrent=document.getElementById("category-current");
    //store all kinds of information into different arraies
    // var category=new Array();
    var title=new Array();
    var date=new Array();
    var content=new Array();
    var department=new Array();
    var content=new Array();

    var colors=[ //section background colors
        "background: #3b579d;",
        "background: #35465c;",
        "background: #8cc251;",
        "background: #0099ff;",
        "background: #85144b;",
        "background: #121212;",
        "background: #666666;",
        "background: #ee4433;",
        "background: #FFDC00;",
        "background-image: linear-gradient(to top, #a3bded 0%, #6991c7 100%);",
        "background: #1d9a59;",
        "background: linear-gradient(to bottom, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0.15) 100%), radial-gradient(at top center, rgba(255,255,255,0.40) 0%, rgba(0,0,0,0.40) 120%) #989898; background-blend-mode: multiply,multiply;",
        "background-image: linear-gradient( 135deg, #97ABFF 10%, #123597 100%);",
        "background-image: linear-gradient( 135deg, #F97794 10%, #623AA2 100%);",
        "background: #e4405f;",
        "background-image: linear-gradient(to top, #37ecba 0%, #72afd3 100%);",
        "background: #f48120;",
        "background: #04afdc;",
        "background-image: linear-gradient(60deg, #29323c 0%, #485563 100%);",
        "background: #2e294e;"
    ];

    function loadPage(){ //load DOM structure of page
    	var pageTop=document.getElementById("top");
    	window.scrollTo(0,pageTop.offsetTop);
    	//initialize page button
    	pre[0].style.cssText+="width: 50%; background: #7FDBFF;";
    	next[0].style.cssText+="width: 50%; background: #39CCCC;";

	    for (var j = 0; j <20; j++) {
	        content[j]=document.createElement("article"); //create "article" section to store passage
	        sections[j].style.cssText+=colors[j];

	        title[j]=document.createElement("h1"); //title
	        title[j].style.cssText+="font-size: 55px; margin: 50px;"
	        title[j].innerHTML=data[j].title;
	        sections[j].appendChild(title[j]);

	        department[j]=document.createElement("p"); //department
	        department[j].innerHTML=data[j].department;
	        sBottoms[j].appendChild(department[j]);

	        date[j]=document.createElement("p"); //date
	        date[j].innerHTML=data[j].date;
	        sBottoms[j].appendChild(date[j]);

	        content[j].innerHTML+=data[j].content; //passage content
	        sections[j].appendChild(content[j]);
	    }

	    //reset all passage content style
	    var art=document.getElementsByTagName("article");
	    for (var i = 0; i < art.length; i++) {
	        var iterator=document.createNodeIterator(art[i],NodeFilter.SHOW_ELEMENT,null,false);
	        var node=iterator.nextNode();
	        while(node!=null){
	            node.style.cssText="font-size: 40px; margin: 15px;";
	            node=iterator.nextNode();
	        }
	    }

	    for(var i=0;i<art.length;i++) art[i].style.cssText="display: none"; //default do not show passage content

	    var isOpen=false; //passage state
	    for (var i = 0; i <20; i++) {
	    	(function(i){
		    	sections[i].onclick=function(){
		    		if(!isOpen){ //show content
		    			if(data[i].content==null){}
		    			else{
			    			sBottoms[i].style.cssText+="display: none;";
			    			this.style.cssText+="height: 1000px; overflow-y: scroll;";
			                this.lastChild.style.cssText="display: block;";
		            	}
		    			isOpen=true;
		    		}
		    		else{ // hide content
		    			sBottoms[i].style.cssText+="display: flex;";
		    			this.style.cssText+="height: 400px; overflow-y: hidden;";
		                this.lastChild.style.cssText="display: none;";
		    			isOpen=false;
		    		}
		        }
	    	})(i)
	    }
	}
    loadPage(); //load page

    function clearPage(){ //clear content, title, department, date of sections
    	for (var j = 0; j <20; j++) {
    		title[j].innerHTML="";
    		sBottoms[j].innerHTML="";
    		content[j].innerHTML="";
    	}
    }

    var isOpen=false;
    select[0].onclick=function(){
    	if(isOpen==false){
            list.style.cssText+="height: 800px; overflow: scroll";
    		this.style.cssText+="height: 800px; overflow: scroll";
    		isOpen=true;
    	}else{
            list.style.cssText+="height: 90px; overflow: hidden";
    		this.style.cssText+="height: 90px; overflow: hidden";
    		isOpen=false;
    	}
    }

    selectList[0].onclick=function(){
        categoryCurrent.innerHTML=this.innerHTML;
        departments="%";
        setTimeout(function(){
            loadJson(pages);
            clearPage();
            loadPage();
        },1000);
    }

    for(var i=1;i<selectList.length;i++){
        selectList[i].onclick=function(){
            categoryCurrent.innerHTML=this.innerHTML;
            departments=categoryCurrent.innerHTML;
            setTimeout(function(){
                loadJson(pages);
                clearPage();
                loadPage();
            },1000);
        }
    }

    //previous page button
    pre[0].ontouchstart=function(){
		this.style.cssText="background: #4ccdff;";
	}
	pre[0].ontouchend=function(){
		this.style.cssText="background: #7FDBFF;";
	}
    pre[0].onclick=function(){
    	this.style.cssText+="background: #4ccdff; width: 100%;";
    	setTimeout(function(){
    		pages--;
    		loadJson(pages);
    		clearPage();
    		loadPage();
    	},500);
    	
    }
    //next page button
    next[0].ontouchstart=function(){
		this.style.cssText+="background: #2ba7a7;";
	}
	next[0].ontouchend=function(){
		this.style.cssText+="background: #39CCCC;";
	}
    next[0].onclick=function(){
    	this.style.cssText+="background: #2ba7a7; width: 100%;";
    	setTimeout(function(){
    		pages++;
    		loadJson(pages);
    		clearPage();
    		loadPage();
    	},500);
    }
}