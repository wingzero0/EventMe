// model
;(function ($){
	var app = this.app || (this.app = {});
	app.model = {};
	app.model.sourceSite = "";
	app.model.fileName = "";

	// app.model.insertDB = insertDB;
	// app.model.queryICAM = queryICAM;
	// app.model.getPlainText = getPlainText;

}).call(this,jQuery);

// view
;(function($){
	var app = this.app || (this.app = {});
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
	var resetInputField = function(){
		$("#name").val("");
		$("#category").val("");
		$("#description").val("");
		$("#applyStartDate").val("");
		$("#applyEndDate").val("");
		$("#timeSlotBlock").html("");
		$("#repeatTimeSlotBlock").html("");
		$("#hostName").val("");
		$("#people").val("");
		$("#location").val("");
		$("#geoLocationLongitude").val("");
		$("#geoLocationLatitude").val("");
		$("#fee").val("");
		$("#website").val("");
		$("#poster").val("");
		$("#tel").val("");
		$( "#numTimeSlot" ).val("0");
		$("#numRepeatTimeSlot").val("0");
	}
	var updateOriginalText = function(data){
		// $("#originalText").html(data);
		$("#originalText").html(data.content);
	}

	var updateInputField = function(data){
		resetInputField();
		var e = jQuery.Event( "click" );
		// $.each(data, function (index,value){
		// 	 console.log(index + ":" + value);
		// });
		var decoded = $("<div/>").html(data.name).text();
		$("#name").val(decoded);
		decoded = $("<div/>").html(data.description).text();
		$("#description").val(decoded);
		addTimeSlot(e); // simulate event
		$("#datepickerStart1").val(data.startDate);
		$("#datepickerEnd1").val(data.endDate);
		$("#tel").val(data.tel);
		decoded = $("<div/>").html(data.poster).text();
		$("#poster").val(decoded);
	}
	var updateTextMenu = function(textList, queryHookCallBack){
		// set menu list
		var html = "";
		$.each(textList, function(index,value){
			//html = html + "<li id="+value+"><a href='#1'>" + value + "</a></li>";
			html = html + "<li><a href='#1' id=text"+index+">" + value + "</a></li>";
		});
		$( "#textMenu" ).menu('destroy');
		$( "#textMenu" ).html(html);
		$( "#textMenu" ).menu();

		resetInputField();
		updateOriginalText({content:""}); // same as reset

		// set click event for each entry
		$.each(textList, function(index,value){
			var id = "#text" + index;
			//console.log(id);
			$(id).click(function(){
				queryHookCallBack(value);
			});
		});
	}
	var updateTips = function(message){
		var tips = $( "#tips" );
        tips.text( message );
        tips.addClass( "ui-state-highlight" );
        // console.log(message);
        setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
                tips.html("&nbsp;");
        }, 5000 );
	}
	var getInputFieldValue = function(){
		var name = $("#name").val();
		var category = $("#category").val();
		var description = $("#description").val();
		var applyStartDate = $("#applyStartDate").val();
		var applyEndDate = $("#applyEndDate").val();
		var hostName = $("#hostName").val();
		var people = $("#people").val();
		var location = $("#location").val();
		var geoLocationLongitude = $("#geoLocationLongitude").val();
		var geoLocationLatitude = $("#geoLocationLatitude").val();
		var fee = $("#fee").val();
		var website = $("#website").val();
		var poster = $("#poster").val();
		var tel = $("#tel").val();
		var numTimeSlot = parseInt($("#numTimeSlot").val());
		var numRepeatTimeSlot = parseInt($("#numRepeatTimeSlot").val());

		var uploadData = {
			"submit" : "submit",
			"name" : name,
			"category" : category,
			"description" : description,
			"applyStartDate" : applyStartDate,
			"applyEndDate" : applyEndDate,
			"hostName" : hostName,
			"people" : people,
			"location" : location,
			"geoLocationLongitude" : geoLocationLongitude,
			"geoLocationLatitude" : geoLocationLatitude,
			"fee" : fee,
			"website" : website,
			"poster" : poster,
			"tel" : tel,
			"numTimeSlot" : numTimeSlot
		};
		for (var i = 1;i<= numTimeSlot;i++){
			uploadData["datepickerStart" + i] = $("#datepickerStart" + i).val();
			uploadData["datepickerEnd" + i] = $("#datepickerEnd" + i).val();
		}
		return uploadData;
	}

	$( "#textMenu" ).menu();
    $( "#sourceSite" ).buttonset();
    
	$("#applyStartDate").datetimepicker({
		timeFormat: "HH:mm:ss",
		dateFormat: "yy-mm-dd"
	});

	$("#applyEndDate").datetimepicker({
		timeFormat: "HH:mm:ss",
		dateFormat: "yy-mm-dd"
	});	

	app.view = {};
	app.view.addTimeSlot = addTimeSlot;
	app.view.addRepeatTimeSlot = addRepeatTimeSlot;
	app.view.resetInputField = resetInputField;
	app.view.updateTextMenu = updateTextMenu;
	app.view.updateOriginalText = updateOriginalText;
	app.view.updateInputField = updateInputField;
	app.view.getInputFieldValue = getInputFieldValue;
	app.view.updateTips = updateTips;
}).call(this, jQuery);

// controller
;(function(){
	var app = this.app;

	var queryHookCallBack = function(fileName){ // trick by clicking on a menu entry
		app.model.fileName = fileName;
		var getPlainText = function(fileName){ // should be rename to "getWholeFile"
			$.get("fileHandler.php", {"op":"getPlainText", "source": app.model.sourceSite, "text": fileName}, app.view.updateOriginalText, "json");
		}
		var getEventContainer = function(fileName){
			$.get("eventParserHandler.php", {"op":"parseXML", "source": app.model.sourceSite, "text": fileName}, app.view.updateInputField, "json");
		}
		getPlainText(fileName);
		getEventContainer(fileName);
	}

	var querySource = function(sourceSite){
		app.model.sourceSite = sourceSite; // save sourceSite at the begining of each query.
		$.get("fileHandler.php", {"op":"getSourceList", "source": sourceSite}, function (data) {
			app.view.updateTextMenu(data, queryHookCallBack);
		}, "json");
	}
	var queryICAM = function(){
		//query server fold ICAM - 民政總署
		querySource("ICAM");
	}
	var clearDoc = function(){
		$.get("fileHandler.php",{"op": "clearDoc", "source": app.model.sourceSite, "text": app.model.fileName}, function(data){
			if (parseInt(data.ret) == 1){
				querySource(app.model.sourceSite);
			}else{
				app.view.updateTips(data.error);
				console.log(data);
			}
		}, "json");
	}
	var skipDoc = function(){
		$.get("fileHandler.php",{"op": "skipDoc", "source": app.model.sourceSite, "text": app.model.fileName}, function(data){
			if (parseInt(data.ret) == 1){
				querySource(app.model.sourceSite);
			}else{
				app.view.updateTips(data.error);
				console.log(data);
			}
		}, "json");
	}
	var insertDB = function(event){
		event.preventDefault();
		var uploadData = app.view.getInputFieldValue();
		$.post('insertActivityHandler.php', uploadData, function(data){
			if (parseInt(data.ret)==1){
				app.view.updateTips("Insert Database successful");
				clearDoc();
			}else{
				console.log(data);
				app.view.updateTips(data.error);
			}
		},'json');
	}



	$(function() {
	    // controller should controll the click event.
		$("#addTimeSlot").button().click(app.view.addTimeSlot);
		// $("#addRepeatTimeSlot").button().click(this.addRepeatTimeSlot);
		$("#insertDB").button().click(insertDB);
		$("#skipDoc").button().click(function(event){
			event.preventDefault();
			skipDoc();
		});
		$( "#radio1").click(queryICAM);
	    queryICAM(); // default query

	});
}).call(this, jQuery);