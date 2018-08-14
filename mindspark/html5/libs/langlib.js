(function() {

var LangLib = function(options) {
  this.initialize(options);
}
var p = LangLib.prototype = {}; // inherit from Container




p.initialize = function(options) {
p.selectedlang = options.selectedlang;
p.langJson = options.langJson;

} 
p.getStringForLang = function(args)
{
	//args.name
	//args.category
	//arg.elementaname
	var langobjEnglish = this.langJson.application['english'];
	var langobj = this.langJson.application[this.selectedlang];
	if(!langobj)
		langobj = this.langJson.application['english'];
	var catgoryarray=[];
	//args.category = args.category + '_asArray';
	args.elementaname = args.elementaname + '_asArray';
	if(langobj[args.category])
	{
		 catgoryarray = langobj[args.category][args.elementaname];
		if(!catgoryarray )
			catgoryarray = langobjEnglish[args.category][args.elementaname];
	}
	else if(langobjEnglish[args.category])
	{
		
			catgoryarray = langobjEnglish[args.category][args.elementaname];
	}
	if(catgoryarray.length==0)
	{
		return "";
	}
	else

	{
		var retStr;
		for (var i = 0; i < catgoryarray.length; i++) {
			if(catgoryarray[i]._name === args.name)
			{
				retStr= catgoryarray[i]._text;
			}
		};
		if(!retStr)
		{
			if(langobjEnglish[args.category])
			{
				
					catgoryarray = langobjEnglish[args.category][args.elementaname];
			}
			if(catgoryarray.length==0)
			{
				return "";
			}
			for (var i = 0; i < catgoryarray.length; i++) {
			if(catgoryarray[i]._name === args.name)
			{
				retStr= catgoryarray[i]._text;
			}
			};
			if(!retStr)
				return "";
			return retStr;
			
		}
		return retStr;

	}



}


window.LangLib = LangLib;
}());
