function showEg(correctStr, userStr) {
	$("#cAns").val(correctStr);
	$("#uAns").val(userStr);
	$("#div2").html("");
	$("#div1").html("");
	$("#p").hide();
}

function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function commonErrors(cAnswer, uAnswer) {

	//private variables
	this.TempCorrectAns = cAnswer;
	this.TempUserAns = uAnswer;
	this.OriginalUserAns = uAnswer;
	this.OriginalCorrectAns = cAnswer;
	this.userAlert = "";
	this.log = "";
	this.errorCode = 0;

	//member functions

	this.TempUserAnsModified = function(str)
		{
			if((String)(this.OriginalUserAns) != (String)(this.TempUserAns))
			{
				this.log += "- "+str+"<br/>";
				this.OriginalUserAns = this.TempUserAns;
				return 1;
			}
			return 0;
		}
	this.TempCorrectAnsModified = function(str)
	{
		if((String)(this.OriginalCorrectAns) != (String)(this.TempCorrectAns))
		{
			this.log += "- "+str+"<br/>";
			this.OriginalCorrectAns = this.TempCorrectAns;
			return 1;
		}
		return 0;
	}

	this.SetUserAlert = function(str, errorCode)
	{
		this.userAlert += str;
		this.errorCode = errorCode;
	}

	this.checkIfEqual = function(str, errorCode)
	{	
		try
		{
			if(nParser.parse(this.OriginalCorrectAns) == nParser.parse(this.TempUserAns))
			{
				//this.OriginalUserAns = (String)(this.TempUserAns);
				if( str !== undefined)
				{
					this.SetUserAlert(str, errorCode);
				}
				else if( str === undefined)
				{
					this.OriginalUserAns = nParser.parse(this.TempUserAns);
				}
				return 1;
			}
			else
			{
				this.TempUserAns = (String)(this.OriginalUserAns);
				return 0;
			}
		}
		catch(e)
		{
			if(this.OriginalCorrectAns == this.TempUserAns)
			{
				//this.OriginalUserAns = (String)(this.TempUserAns);
				if( str !== undefined)
					this.SetUserAlert(str, errorCode);
				return 1;
			}
			else
			{
				this.TempUserAns = (String)(this.OriginalUserAns);
				return 0;
			}
		}
	}

	this.extraCharsTrim = function() {

			this.TempUserAns = this.TempUserAns.replace(/`+/i,"");
			this.TempUserAnsModified("Found, ignore: Extra ` symbol(s) in the user Answer.");

			this.TempUserAns = this.TempUserAns.replace(/'+/i,"");
			this.TempUserAnsModified("Found, ignore: Extra ' symbol(s) in the user Answer.");

			this.TempUserAns = this.TempUserAns.replace(/\.+$/i,"");
			this.TempUserAnsModified("Found, ignore: Full-stop(s) at the end of the user Answer.");

			this.TempUserAns = this.TempUserAns.replace(/\.+/i,".");
			this.TempUserAnsModified("Found, ignore: Multiple consecutive full-stops in the user Answer. (treated as a single full-stop)");

			this.TempUserAns = this.TempUserAns.replace(/\\+$/i,"");
			this.TempUserAnsModified("Found, ignore: Extra \\ symbol(s) at the end of the user Answer.");

			this.TempUserAns = this.TempUserAns.replace(/\\+$/i,"\\");
			this.TempUserAnsModified("Found, ingnore: Multiple consecutive \ in the user Answer.");

			this.TempUserAns = this.TempUserAns.replace(/\]+$/i,"");
			this.TempUserAnsModified("Found, ignore: Extra \] symbol(s) at the end of the user Answer.");

			this.TempCorrectAns = this.TempCorrectAns.replace(/\.+$/i,"");
			this.TempCorrectAnsModified("Found, ignore: Extra full-stops at the end of the system's Answer.");

			return this.checkIfEqual();
	}

	this.backSlashForForwardSlash = function() {

			this.TempUserAns = this.TempUserAns.replace(/\\/i,"/");
			this.TempUserAnsModified("Found, ignore: '\\' used instead of '/' symbol in the user Answer.");

			return this.checkIfEqual();

	}

	this.commaForDot = function(){
		if(this.TempUserAns.indexOf(",") > -1)
		{
			this.TempUserAns = this.TempUserAns.substr(0,this.TempUserAns.lastIndexOf(",")) + "." + this.TempUserAns.substr(this.TempUserAns.lastIndexOf(",")+1);
			return this.checkIfEqual("You seem to have used ',' (comma) instead of decimal point.", 1);
		}
		return 0;
	}

	this.charReplaceInNum = function()
	{
		if( isNumber(this.OriginalCorrectAns) && !isNumber(this.OriginalUserAns) )
		{
			var charArr = new Array('o','O','l','!','I','S','_');
			var replaceCharArr = new Array('0','0','1','1','1','5','-');
			for(var i = 0; i < 6; i++)
			{
				this.TempUserAns = this.TempUserAns.replace(RegExp(charArr[i],"g"),replaceCharArr[i]);
				if(this.checkIfEqual("You seem to have used the character '"+charArr[i]+"' instead of the number "+replaceCharArr[i]+"!", 2))
					return 1;
			}
			//for underscore
			var i = 6;
			this.TempUserAns = this.TempUserAns.replace(RegExp(charArr[i],"g"),replaceCharArr[i]);
			if(this.checkIfEqual("You seem to have used '_' (underscore) instead of '-' (minus symbol)!", 3))
				return 1;				
		}
		return 0;			 
	} //end of this.charReplaceInNum

	this.ratioSymbol = function()
	{
		if((this.OriginalCorrectAns).indexOf(":") > -1)
		{
			 this.TempUserAns = (String)(this.TempUserAns).replace(/;/i,":");
			 return this.checkIfEqual("Please see if you have used the correct symbol for ratio.", 4);
		}
		return 0;
	} //end of this.ratioSymbol

	this.commonMisspellings = function()
	{
		var local_TempUserAns = this.TempUserAns;
		var correctSpellings_array = ["hundred","ninety","thousand","eighths","nineteen","fourth",""];
		var incorrectSpellings_array = ["h(a|e|u)?nd(a|e|i)?r(a|e|i)?d","ninty","thou?s(a|e)nd","eights","ninteen","forth"];
		var regex, errorUnitsList = '';

		for(var i = 0; i < correctSpellings_array.length; i++)
		 	{
		 		regex = new RegExp(incorrectSpellings_array[i],"gi");
		 		this.TempUserAns = (local_TempUserAns).replace(regex,correctSpellings_array[i]);
		 		if(local_TempUserAns != this.TempUserAns)
		 			errorUnitsList += ", '"+correctSpellings_array[i]+"'";
		 		local_TempUserAns = this.TempUserAns;
		 	}
		return this.checkIfEqual("Please check if you have spelt "+errorUnitsList.substr(2)+" correctly!", 5);
	}//end of this.commonMispellings

	this.noUnits = function()
	{
		try
		{
			var index = (this.OriginalCorrectAns).indexOf("^(");
			if((index > -1) && isNumber(this.TempUserAns))
			{
				this.SetUserAlert("Please specify the units in your answer!", 6);
				return 1;
			}
		}
		catch(e)
		{
			return 0;
		}
	}//end of this.noUnits

this.unitsMisspellings = function()
	{
		var local_TempUserAns = this.TempUserAns;
		var regex, errorUnitsList = "";
		var units_array = ["m","kg","s","K","radian"];
		var correctForms_array = [["km","m","cm","mm","mL","l","kl"],["kg","g","cg","mg"],["s","minutes","hours","years"]];
		var incorrectForms_array = [["(k(ilo)?s?(\s)?(me?te?re?s?))|k\\.?m\\.?s|k\\.m\\.?","(me?te?re?s?)|m\\.?s","(centis?)(\s)?(me?te?re?s?)|c\\.?m\\.?s|c\\.m\\.?","(mill?i)s?(\s)?(me?te?re?s?)|m\\.?m\\.?s|m\\.m\\.?","(mill?i)s?(\s)?(li?te?re?s?)|m\\.?l\\.?s|m\\.l\\.?","li?t(e?r)s?|l\\.?s","(k(ilo)?s?(\s)?(li?te?re?s?))|k\\.?l\\.?s|k\\.l\\.?"], 	["(k(ilo)s?(\s)?(gra?ms?)?)|(k(ilo)?s?(\s)?(gra?ms?))|k\\.?g\\.?s|k\\.g\\.?","g\\.?m\\.?s?|g\\.?m?\\.?s|grms?","c(enti)?(\s)?(gra?ms?)?|c\\.?g\\.?s|c\\.g\\.?","(mill?i(\s)?(gra?ms?)?)|(m\\.?g\\.?s)|(m\\.g\\.?)"], ["secs?","mins","hr\.?s","yr\.?s"]];

		for(var i = 0; i < correctForms_array.length; i++)
		{	
			if((this.OriginalCorrectAns).indexOf(units_array[i]+"^(") > -1)
			{
				for(var j = 0; j < correctForms_array[i].length; j++)
				{
			 		regex = new RegExp(incorrectForms_array[i][j],"gi");
			 		this.TempUserAns = (local_TempUserAns).replace(regex,correctForms_array[i][j]);
			 		if(local_TempUserAns != this.TempUserAns)
			 			errorUnitsList += ", '" + correctForms_array[i][j] + "'";
			 		local_TempUserAns = this.TempUserAns;
			 	}
			 	return this.checkIfEqual("Please check if you have written the unit '"+errorUnitsList.substr(3)+"' correctly.", 7);	
			}
		}
		return 0;
	} //end of this.unitsMisspellings

	this.extraUnits = function()
	{
		if(isNumber(this.OriginalCorrectAns))
		{
			if((this.TempUserAns).search(new RegExp("^"+this.OriginalCorrectAns + '(\\s)[a-z]+(\\^\\()', "gi"))  > -1)
			{
				this.SetUserAlert("You seem to have added extra unit at the end of your answer.", 8);
				return 1;
			}

			else if((this.TempUserAns).search(new RegExp("^"+this.OriginalCorrectAns + '(\\s)*[a-z]+$', "gi"))  > -1)
			{
				this.SetUserAlert("You seem to have added extra unit / text at the end of your answer.", 9);
				return 1;
			}
		}
		return 0;
	} //end of this.extraUnits


	this.multipleAns = function()
	{
		var splitUserAns_array = [];
		if((this.OriginalUserAns).indexOf(" or ") > -1)
		{
			splitUserAns_array = (this.OriginalUserAns).split(" or ");
			for(var i = 0; i < splitUserAns_array.length; i++)
			{
				this.TempUserAns = splitUserAns_array[i];
				if(this.checkIfEqual("Please do not use 'or' to write multiple answers in the same blank.", 10))
					return 1;
			}
		}
		else if((this.OriginalUserAns).indexOf("=") > -1)
		{
			splitUserAns_array = (this.OriginalUserAns).split("=");
			for(var i = 0; i < splitUserAns_array.length; i++)
			{
				this.TempUserAns = splitUserAns_array[i];
				if(this.checkIfEqual("Please do not use '=' to write multiple answers in the same blank.", 11))
					return 1;
			}
		return 0;
		}
	}//end of this.multipleAns



	this.parse = function() {
		//this.trim();
		if(this.extraCharsTrim()) {}
		else if(this.backSlashForForwardSlash()) {}
		else if(this.commaForDot()) {}
		else if(this.charReplaceInNum()) {}
		else if(this.ratioSymbol()) {}
		else if(this.commonMisspellings()) {}
		else if(this.noUnits()) {}
		else if(this.unitsMisspellings()) {}
		else if(this.extraUnits()) {}
		else if(this.multipleAns()) {}

		return (this.OriginalUserAns == this.OriginalCorrectAns);
	}
}
