;(function (){
	var addMenu = function(){
		//event.preventDefault();
		$( "#textMenu" ).menu('destroy');
		//var inner = $( "#textMenu" ).html();
		//$( "#textMenu" ).html(inner + '<li id="menu1"><a href="#1">4e5c2c95-f2e0-4229-8373-5345e7813576.xml</a></li><li id="menu2"><a href="#2">9eacbf2b-8e11-47b8-979f-1bff288a4496.xml</a></li>');
		$( "#textMenu" ).html('<li id="menu1"><a href="#1">4e5c2c95-f2e0-4229-8373-5345e7813576.xml</a></li><li id="menu2"><a href="#2">9eacbf2b-8e11-47b8-979f-1bff288a4496.xml</a></li>');
		$( "#textMenu" ).menu();
	}
	var querySource = function(sourceSite){
		var updateTextMenu = function(textList){
			// set menu list
			var html = "";
			$.each(textList, function(index,value){
				//html = html + "<li id="+value+"><a href='#1'>" + value + "</a></li>";
				html = html + "<li><a href='#1' id=text"+index+">" + value + "</a></li>";
			});
			$( "#textMenu" ).menu('destroy');
			$( "#textMenu" ).html(html);
			$( "#textMenu" ).menu();

			// set menu click event

			var updateOriginalText = function(data){
				$("#originalText").html(data);
				/*
				$.each(data, function(index, value){
					$("#originalText").html(value);
				});
				*/
			}
			var getPlainText = function(fileName){
				$.get("fileHandler.php", {"op":"getPlainText", "source": sourceSite, "text": fileName}, updateOriginalText, "json");
			}

			$.each(textList, function(index,value){
				var id = "#text" + index;
				//console.log(id);
				$(id).click(function(){
					getPlainText(value);
				});
			});
		}
		$.get("fileHandler.php", {"op":"getSourceList", "source": sourceSite},updateTextMenu, "json");
	}
	var queryICAM = function(){
		//query server fold ICAM - 民政總署
		// demo change
		//addMenu();
		querySource("ICAM");
		/*
		$("#menu1").click(function(){
			$("#originalText").html('<p>\
				    主頁 > 活動訊息 > 活動快訊\
				    “十月初五的藝墟”二零一四年上半年攤位招募\
				    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 由民政總署主辦的“十月初五的藝墟”二零一四年上半年攤位招募現正開始，歡迎擁有個人原創產品的藝術愛好者報名參加，截止日期為二零一三年十二月一日。\
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 今次招募共有五期，包括：二○一四年一月十一及十二日、二月八及九日、三月八及九日、四月十二及十三日、六月十四及十五日，下午三時至晚上八時於康公廟前地舉行。\
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 參加者可以團體或個人名義報名，費用全免。出售及展示的產品必須原創，包括售賣手工藝、設計產品或創意飲食、即場示範創作、文化藝術課程或出版品推廣等，主辦單位將以產品或創作的獨特性作為篩選主要條件，成功獲選的參加者將由專人通知。\
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 報名表可於辦公時間到南灣大馬路五百一十七號南通商業大廈十九樓民政總署文化康體部索取，或於民政總署網頁www.iacm.gov.mo下載。參加者可將填妥的報名表，連同產品簡介、照片的紙本及電子檔，於辦公時間送交上述地點，亦可電郵至cschiu@iacm.gov.mo或knwong@iacm.gov.mo。查詢電話：8988 4100。&nbsp;開始日期 : 2013/11/20結束日期 : 2013/12/012014十月初五的藝墟報名表 </p>');
		});
		$("#menu2").click(function(){
			$("#originalText").html('<p>活動二</p>');
		});
		*/
	}
	$("#addMenu").click(addMenu);
		
	var addRepeatTimeSlot = function(event){
		event.preventDefault();
		var num = parseInt($("#numRepeatTimeSlot").val());
		num += 1;
		$( "#numRepeatTimeSlot" ).val(num);
		var newRepeatStart = $( "<input/>", {id:"repeatStart" + num, name:"repeatStart" + num, type:"text"});
		$("#repeatTimeSlotBlock").append("開始時間");
		$("#repeatTimeSlotBlock").append(newRepeatStart);

		var newRepeatEnd = $( "<input/>", {id:"repeatEnd" + num, name:"repeatEnd" + num, type:"text"});
		$("#repeatTimeSlotBlock").append("結束時間");
		$("#repeatTimeSlotBlock").append(newRepeatEnd);
		
		var newRepeatWeek = $( "<input/>", {id:"repeatWeek" + num, name:"repeatWeek" + num, type:"text", value:2});
		$("#repeatTimeSlotBlock").append("重覆週數");
		$("#repeatTimeSlotBlock").append(newRepeatWeek);
		
		
		var i;
		for (i=1;i<=7;i++){
			var checkBox = $( "<input/>", {name:"checkBox" + num + i, type:"checkBox", value:i});
			$("#repeatTimeSlotBlock").append(i);
			$("#repeatTimeSlotBlock").append(checkBox);
		}

		$("#repeatTimeSlotBlock").append("<br>");

		$("#repeatStart" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#repeatEnd" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});
	}
	var addTimeSlot = function (event){
		event.preventDefault();
		var num = parseInt($( "#numTimeSlot" ).val());
		num +=1;
		var newStart = $( "<input/>", {id:"datepickerStart" + num, name:"datepickerStart" + num  });
		var newEnd = $( "<input/>", {id:"datepickerEnd" + num, name:"datepickerEnd" + num });
		$("#timeSlotBlock").append("活動時間" + num);
		$("#timeSlotBlock").append(newStart);
		$("#timeSlotBlock").append("至");
		$("#timeSlotBlock").append(newEnd);
		$("#timeSlotBlock").append("<br>");
		$( "#numTimeSlot" ).val(num);

		$("#datepickerStart" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#datepickerEnd" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});
	}
	
	$(function() {
	    $( "#textMenu" ).menu();
	    $( "#sourceSite" ).buttonset();
	    $( "#radio1").click(queryICAM);
	    queryICAM(); // default query
		
		$("#applyStartDate").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#applyEndDate").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});	
		$("#addTimeSlot").button().click(addTimeSlot);
		$("#addRepeatTimeSlot").button().click(addRepeatTimeSlot);
	});

})();