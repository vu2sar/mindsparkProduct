var singleDigit = Array("zero","one","two","three","four","five","six","seven","eight","nine");
var doubleDigit = Array("eleven","twelve","thirteen","fourteen","fifteen","sixteen","seventeen","eighteen","nineteen");
var tenDigit = Array("ten", "twenty","thirty","forty","fifty","sixty","seventy","eighty", "ninety");
var fracSingularSingleDigit = Array("first","half","third","fourth","fifth","sixth","seventh","eighth","ninth");
var fracSingularDoubleDigit = Array("eleventh","twelfth","thirteenth","fourteenth","fifteenth","sixteenth","seventeenth","eighteenth","nineteenth");
var fracSingularTenDigit = Array("tenth","twentieth","thirtieth","fortieth","fiftieth","sixtieth","seventieth","eightieth", "ninetieth");
var fracPluralSingleDigit = Array("firsts","halves","thirds","fourths","fifths","sixths","sevenths","eighths","ninths");
var fracPluralDoubleDigit = Array("elevenths","twelfths","thirteenths","fourteenths","fifteenths","sixteenths","seventeenths","eighteenths","nineteenths");
var fracPluralTenDigit = Array("tenths","twentieths","thirtieths","fortieths","fiftieths","sixtieths","seventieths","eightieths", "ninetieths");


var nParser = (function(){
var nParser = {trace: function trace(){},
yy: {},
symbols_: {"error":2,"expressions":3,"e":4,".":5,"EOF":6,"units_final":7,"c_units":8,"other_units_final":9,"money":10,"+":11,"-":12,"*":13,"/":14,"^":15,"NUMBER":16,"!":17,"(":18,")":19,"MFRAC":20,"w":21,"integer":22,"magnitude":23,"E":24,"PI":25,"PERCENT":26,"SQRT":27,"INUMBER":28,"FNUMBER":29,"crore":30,"crores":31,"billion":32,"billions":33,"fracWords":34,"AND":35,",":36,"millions":37,"BILLIONS":38,"million":39,"BILLION":40,"MILLIONS":41,"thousand":42,"thousands":43,"MILLION":44,"lakhs":45,"CRORES":46,"lakh":47,"CRORE":48,"LAKHS":49,"LAKH":50,"hundreds":51,"THOUSANDS":52,"hundred":53,"THOUSAND":54,"tens":55,"HUNDREDS":56,"ten":57,"HUNDRED":58,"one":59,"TEN":60,"DOUBLE_DIGIT":61,"TEN_DIGIT":62,"HYPHEN_DIGIT":63,"ones":64,"TENS":65,"SINGLE_DIGIT":66,"ONES":67,"oneths":68,"oneth":69,"tenth":70,"tenthsGroup":71,"tenDigith":72,"tenDigiths":73,"quarter":74,"hundredths":75,"thousandths":76,"FRAC_HYPHEN_DIGIT":77,"FRAC_SINGULAR_TEN_DIGIT":78,"FRAC_PLURAL_TEN_DIGIT":79,"FRAC_SINGULAR_DOUBLE_DIGIT":80,"FRAC_PLURAL_DOUBLE_DIGIT":81,"FRAC_SINGULAR_SINGLE_DIGIT":82,"FRAC_PLURAL_SINGLE_DIGIT":83,"QUARTER":84,"HUNDREDTH":85,"THOUSANDTH":86,"units":87,"METRE":88,"KILOGRAM":89,"SECOND":90,"KILOMETRE":91,"MILLIMETRE":92,"CENTIMETRE":93,"LITRE":94,"KILOLITRE":95,"MILLILITRE":96,"CENTILITRE":97,"MILE":98,"YARD":99,"FOOT":100,"INCH":101,"GRAM":102,"CENTIGRAM":103,"MILLIGRAM":104,"MICROGRAM":105,"POUND":106,"OUNCE":107,"TON":108,"NANOSECOND":109,"MICROSECOND":110,"MILLISECOND":111,"MINUTE":112,"HOUR":113,"DAY":114,"WEEK":115,"MONTH":116,"YEAR":117,"KELVIN":118,"other_units":119,"CELCIUS":120,"FAHRENHEIT":121,"DEGREE":122,"RADIAN":123,"SQUARE":124,"CUBIC":125,"RUPEE":126,"RUPEES":127,"$accept":0,"$end":1},
terminals_: {2:"error",5:".",6:"EOF",7:"units_final",11:"+",12:"-",13:"*",14:"/",15:"^",16:"NUMBER",17:"!",18:"(",19:")",20:"MFRAC",24:"E",25:"PI",26:"PERCENT",27:"SQRT",28:"INUMBER",29:"FNUMBER",35:"AND",36:",",38:"BILLIONS",40:"BILLION",41:"MILLIONS",44:"MILLION",46:"CRORES",48:"CRORE",49:"LAKHS",50:"LAKH",52:"THOUSANDS",54:"THOUSAND",56:"HUNDREDS",58:"HUNDRED",60:"TEN",61:"DOUBLE_DIGIT",62:"TEN_DIGIT",63:"HYPHEN_DIGIT",65:"TENS",66:"SINGLE_DIGIT",67:"ONES",77:"FRAC_HYPHEN_DIGIT",78:"FRAC_SINGULAR_TEN_DIGIT",79:"FRAC_PLURAL_TEN_DIGIT",80:"FRAC_SINGULAR_DOUBLE_DIGIT",81:"FRAC_PLURAL_DOUBLE_DIGIT",82:"FRAC_SINGULAR_SINGLE_DIGIT",83:"FRAC_PLURAL_SINGLE_DIGIT",84:"QUARTER",85:"HUNDREDTH",86:"THOUSANDTH",88:"METRE",89:"KILOGRAM",90:"SECOND",91:"KILOMETRE",92:"MILLIMETRE",93:"CENTIMETRE",94:"LITRE",95:"KILOLITRE",96:"MILLILITRE",97:"CENTILITRE",98:"MILE",99:"YARD",100:"FOOT",101:"INCH",102:"GRAM",103:"CENTIGRAM",104:"MILLIGRAM",105:"MICROGRAM",106:"POUND",107:"OUNCE",108:"TON",109:"NANOSECOND",110:"MICROSECOND",111:"MILLISECOND",112:"MINUTE",113:"HOUR",114:"DAY",115:"WEEK",116:"MONTH",117:"YEAR",118:"KELVIN",120:"CELCIUS",121:"FAHRENHEIT",122:"DEGREE",123:"RADIAN",124:"SQUARE",125:"CUBIC",126:"RUPEE",127:"RUPEES"},
productions_: [0,[3,3],[3,2],[3,3],[3,2],[3,3],[3,2],[3,2],[3,3],[4,3],[4,3],[4,3],[4,3],[4,3],[4,2],[4,3],[4,4],[4,1],[4,1],[4,1],[22,2],[22,2],[22,1],[23,1],[23,1],[23,1],[23,2],[23,2],[23,4],[23,1],[23,1],[21,1],[21,1],[21,1],[21,1],[21,1],[21,3],[21,3],[33,1],[33,2],[33,2],[33,3],[32,1],[32,1],[32,2],[32,2],[32,2],[32,3],[32,3],[37,2],[37,2],[37,3],[39,1],[39,2],[39,2],[39,2],[39,3],[39,3],[31,1],[31,2],[31,2],[31,3],[30,1],[30,1],[30,2],[30,2],[30,2],[30,3],[30,3],[45,1],[45,2],[45,2],[45,3],[47,1],[47,1],[47,2],[47,2],[47,2],[47,3],[47,3],[43,1],[43,2],[43,2],[43,3],[42,1],[42,1],[42,2],[42,2],[42,2],[42,3],[42,3],[51,1],[51,2],[51,2],[51,3],[53,1],[53,1],[53,2],[53,2],[53,2],[53,3],[53,3],[57,1],[57,1],[57,1],[57,1],[57,2],[57,1],[55,1],[55,2],[55,2],[55,2],[55,3],[59,1],[64,2],[64,2],[64,2],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[72,1],[72,2],[72,2],[72,2],[72,2],[72,2],[73,1],[73,2],[73,2],[73,2],[73,2],[73,2],[70,1],[70,2],[70,2],[70,2],[70,2],[70,2],[71,1],[71,2],[71,2],[71,2],[71,2],[71,2],[69,1],[69,2],[69,2],[69,2],[69,2],[69,2],[68,1],[68,2],[68,2],[68,2],[68,2],[68,2],[74,1],[74,2],[74,2],[74,2],[74,2],[74,2],[74,2],[75,1],[75,2],[75,2],[75,2],[75,2],[75,2],[75,2],[76,1],[76,2],[76,2],[76,2],[76,2],[76,2],[76,2],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,3],[119,4],[119,4],[119,4],[119,4],[119,4],[8,1],[8,2],[8,2],[8,2],[8,4],[8,3],[8,3],[8,5],[8,3],[8,3],[8,5],[8,3],[8,5],[8,3],[8,2],[8,2],[8,3],[8,3],[9,1],[9,3],[10,2],[10,2],[10,2],[10,2]],
performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate /* action[1] */, $$ /* vstack */, _$ /* lstack */
/**/) {
/* this == yyval */

var $0 = $$.length - 1;
switch (yystate) {
case 1:return ($$[$0-2].toString());
break;
case 2:return ($$[$0-1].toString());
break;
case 3: 
    var input_arr = $$[$0-2];
    var return_value = $$[$0-2][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 4: 
    var input_arr = $$[$0-1];
    var return_value = $$[$0-1][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 5: 
    var input_arr = $$[$0-2];
    var return_value = $$[$0-2][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 6: 
    var input_arr = $$[$0-1];
    var return_value = $$[$0-1][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 7:return $$[$0-1];
break;
case 8:return $$[$0-2];
break;
case 9:this.$ = $$[$0-2]+$$[$0];
break;
case 10:this.$ = $$[$0-2]-$$[$0];
break;
case 11:this.$ = $$[$0-2]*$$[$0];
break;
case 12:this.$ = $$[$0-2]/$$[$0];
break;
case 13:this.$ = Math.pow($$[$0-2], $$[$0]);
break;
case 14:
           if($$[$0-1] % 1 != 0 || $$[$0-1] < 0) throw new Error("error");    
        this.$ = 1;   
        for(var i = $$[$0-1]; i>1; i--)   
        {   
          this.$ = this.$*i;    
        }   
    
        
    
break;
case 15:this.$ = $$[$0-1];
break;
case 16:this.$ = (-1)*Number($$[$0-1]);
break;
case 17:
     var temp = yyleng; 
     var val = 0;
     var val1 = 0;
     var val2 = 0;
     var pos = 0;
     for(; pos < temp; pos++)
      {
      if(yytext[pos] == " ")
        break;
      val = 10*val + Number(yytext[pos]);
      }
     pos++;
     for(; pos <temp; pos++)
     {
       if(yytext[pos] == "/" || yytext[pos] == "\\")
        break;
      val1 = 10*val1 + Number(yytext[pos]);
     }
     pos++;
     for(; pos <temp; pos++)
     {
      val2 = 10*val2 + Number(yytext[pos]);
     }
     this.$ = val + (val1/val2);
    
break;
case 18:this.$ = $$[$0];
break;
case 19:this.$ = $$[$0];
break;
case 20:this.$ = Number($$[$0]);
break;
case 21:this.$ = (-1)*Number($$[$0]);
break;
case 22:this.$ = $$[$0];
break;
case 23:this.$ = Number(yytext);
break;
case 24:this.$ = Math.E;
break;
case 25:this.$ = Math.PI;
break;
case 26:this.$ = Number($$[$0-1])*Math.PI;
break;
case 27:this.$ = Number($$[$0-1])*0.01;
break;
case 28:this.$ = Math.sqrt($$[$0-1]);
break;
case 29:
        var temp = yyleng;
        var val = 0;
        for(var i = 0; i < temp; i++)
        {
        if(yytext[i] == ",")
          continue;
        val = 10*val + Number(yytext[i]);
        }  
        this.$ = val;
       
break;
case 30:
        var temp = yyleng;
        var val = 0;
        for(var i = 0; i < temp; i++)
        {
        if(yytext[i] == ",")
          continue;
        val = 10*val + Number(yytext[i]);
        }  
        this.$ = val;
       
break;
case 31:this.$ = $$[$0];
break;
case 35:this.$ = $$[$0];
break;
case 36:if($$[$0-2] < $$[$0]) throw new Error("Invalid number in words.");
     this.$ = $$[$0-2] + $$[$0];
break;
case 37:if($$[$0-2] < $$[$0]) throw new Error("Invalid number in words.");
     this.$ = $$[$0-2] + $$[$0];
break;
case 39:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000;
       
break;
case 40:
         this.$ = $$[$0-1]*1000000000;
      
break;
case 41:
         this.$ = $$[$0-2]*1000000000 + $$[$0];
      
break;
case 43:this.$ = 100000000;
break;
case 44:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000;
      
break;
case 45:this.$ = $$[$0-1]*1000000000;
break;
case 46:this.$ = 1000000000 + $$[$0];
break;
case 47:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000 + $$[$0];
      
break;
case 48:this.$ = $$[$0-2]*1000000000 + $$[$0];
break;
case 49:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000;
       
break;
case 50:
         this.$ = $$[$0-1]*1000000;
      
break;
case 51:
         this.$ = $$[$0-2]*1000000 + $$[$0];
      
break;
case 52:this.$ = 100000;
break;
case 53:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000;
      
break;
case 54:this.$ = $$[$0-1]*1000000;
break;
case 55:this.$ = 1000000 + $$[$0];
break;
case 56:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000 + $$[$0];
      
break;
case 57:this.$ = $$[$0-2]*1000000 + $$[$0];
break;
case 58:this.$ = $$[$0];
break;
case 59:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000;
       
break;
case 60:
         this.$ = $$[$0-1]*10000000;
      
break;
case 61:
         this.$ = $$[$0-2]*10000000 + $$[$0];
      
break;
case 62:this.$ = $$[$0];
break;
case 63:this.$ = 10000000;
break;
case 64:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000;
      
break;
case 65:this.$ = $$[$0-1]*100000;
break;
case 66:this.$ = 10000000 + $$[$0];
break;
case 67:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000 + $$[$0];
      
break;
case 68:this.$ = $$[$0-2]*10000000 + $$[$0];
break;
case 69:this.$ = $$[$0];
break;
case 70:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000;
       
break;
case 71:
         this.$ = $$[$0-1]*100000;
      
break;
case 72:
         this.$ = $$[$0-2]*100000 + $$[$0];
      
break;
case 73:this.$ = $$[$0];
break;
case 74:this.$ = 100000;
break;
case 75:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000;
      
break;
case 76:this.$ = $$[$0-1]*100000;
break;
case 77:this.$ = 100000 + $$[$0];
break;
case 78:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000 + $$[$0];
      
break;
case 79:this.$ = $$[$0-2]*100000 + $$[$0];
break;
case 80:this.$ = $$[$0];
break;
case 81:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000;
       
break;
case 82:
         this.$ = $$[$0-1]*1000;
      
break;
case 83:
         this.$ = $$[$0-2]*1000 + $$[$0];
      
break;
case 84:this.$ = $$[$0];
break;
case 85:this.$ = 1000;
break;
case 86:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000;
       
break;
case 87:this.$ = $$[$0-1]*1000;
break;
case 88:this.$ = 1000 + $$[$0];
break;
case 89:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000 + $$[$0];
       
break;
case 90:this.$ = $$[$0-2]*1000+$$[$0];
break;
case 91:this.$ = $$[$0];
break;
case 92:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100;
       
break;
case 93:
         this.$ = $$[$0-1]*100;
      
break;
case 94:
         this.$ = $$[$0-2]*100 + $$[$0];
      
break;
case 95:this.$ = $$[$0];
break;
case 96:this.$ = 100;
break;
case 97:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100;
       
break;
case 98:this.$ = $$[$0-1]*100;
break;
case 99:this.$ = 100+$$[$0];
break;
case 100:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100 + $$[$0];
       
break;
case 101:this.$ = $$[$0-2]*100+$$[$0];
break;
case 102:this.$ = $$[$0];
break;
case 103:this.$ = 10;
break;
case 104:
     var str = $$[$0];
     str = str.toLowerCase();
     this.$ = doubleDigit.indexOf(str)+11;
     
break;
case 105:var str = $$[$0];    
   str = str.toLowerCase();   
  this.$ = (tenDigit.indexOf(str)+1)*10;
     
break;
case 106:
    var str = $$[$0-1];   
  str = str.toLowerCase();    
  this.$ = (tenDigit.indexOf(str)+1)*10 + $$[$0];
     
break;
case 107:
    var str = $$[$0];
    str = str.toLowerCase();    
    var n = str.split("-");   
    this.$ = (tenDigit.indexOf(n[0])+1)*10 + singleDigit.indexOf(n[1]);
     
break;
case 108:this.$ = $$[$0];
break;
case 109:
      var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num*10;
   
break;
case 110:
      var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num*10;
   
break;
case 111:
     this.$ = $$[$0-1]*10;
    
break;
case 112:
     this.$ = $$[$0-2]*10 + $$[$0];
    
break;
case 113: var str = $$[$0];   
   str = str.toLowerCase();
     this.$ = singleDigit.indexOf(str);
break;
case 114:
     var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num;
    
break;
case 115: if($$[$0] != 1) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]);
break;
case 116: this.$ = $$[$0-1];       
break;
case 126:var str = $$[$0];
       str = str.toLowerCase();
       var n = str.split("-");
       var index = -1;
       var numerator = 1;
       var denominator = 1;
       var loopCount=0;
       while(index == -1)
       {
       loopCount++;
        index = singleDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  index;
         break;
         }
        index = doubleDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  index+11;
         break;
         }
         index = tenDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  (index+1)*10;
         break;
         }
         if(loopCount==20)
        break;
       }
       if(n[1] == "quarters"|"quarter")
       denominator = 4;
       else if(n[1] == "hundredths"|"hundredth")
       denominator = 100;
       else if(n[1] == "thousandths"|"thousandth")
       denominator = 1000;
       index = -1;
       loopCount=0;
       while(index == -1 && denominator == 1)
      {
        loopCount++;
        index = fracSingularSingleDigit.indexOf(n[1]);
        if(index != -1)
        {
          denominator = (index+1);
          break;
        }
        index = fracSingularDoubleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  index+11;
         break;
         }
         index = fracSingularTenDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  (index+1)*10;
         break;
         }
        index = fracPluralSingleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator = (index+1);
         break;
         }
        index = fracPluralDoubleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  index+11;
         break;
         }
         index = fracPluralTenDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  (index+1)*10;
         break;
         }
         if(loopCount==20)
        break;
       } 
       //if(numerator >= denominator) throw new Error("Invalid entry in words.");       
       this.$ = Number(numerator/denominator);
      
break;
case 127:
      var str = $$[$0];   
    str = str.toLowerCase();
      this.$ = Number(0.1/(fracSingularTenDigit.indexOf(str)+1));
       
break;
case 128: var str = $$[$0];   
     str = str.toLowerCase();
       var index = fracSingularTenDigit.indexOf(str);
       if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
       this.$ = Number($$[$0-1]*0.1/(index+1));
       
break;
case 129: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/((index+1)*10));
     
break;
case 130: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100/((index+1)*10));
     
break;
case 131: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(1000/((index+1)*10));
     
break;
case 132: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100000/((index+1)*10));
     
break;
case 133:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(0.1/(fracPluralTenDigit.indexOf(str)+1)); 
break;
case 134: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.1/(index+1));
     
break;
case 135: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/((index+1)*10));
     
break;
case 136: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(100/((index+1)*10));
     
break;
case 137: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 138: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(100000/((index+1)*10));
     
break;
case 139:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracSingularDoubleDigit.indexOf(str)+11)); 
break;
case 140: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 141: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 142: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 143: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 144: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 145:var str = $$[$0];    
    str = str.toLowerCase();
     this.$ = Number(1/(fracPluralDoubleDigit.indexOf(str)+11)); 
break;
case 146: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 147: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 148: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 149: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 150: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 151:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracSingularSingleDigit.indexOf(str)+1)); 
break;
case 152: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 153: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 154: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 155: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 156: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 157:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracPluralSingleDigit.indexOf(str)+1)); 
break;
case 158: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 159: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 160: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 161: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 162: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 163: this.$ = Number(1/4); 
break;
case 164: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/4);
     
break;
case 165:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/4);
     
break;
case 166: 
     this.$ = Number(25);
     
break;
case 167: 
      this.$ = Number(250);
     
break;
case 168:  this.$ = Number(25000);
break;
case 169:  this.$ = Number(2500000);
break;
case 170: this.$ = Number(0.01); 
break;
case 171: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.01);
     
break;
case 172:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.01);
     
break;
case 173: 
     this.$ = Number(1);
     
break;
case 174: 
      this.$ = Number(10);
     
break;
case 175:  this.$ = Number(1000);
break;
case 176:  this.$ = Number(100000);
break;
case 177: this.$ = Number(0.001); 
break;
case 178: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.001);
     
break;
case 179:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.001);
     
break;
case 180: 
     this.$ = Number(0.1);
     
break;
case 181: 
      this.$ = Number(1);
     
break;
case 182:  this.$ = Number(100);
break;
case 183:  this.$ = Number(10000);
break;
case 184:this.$ = [1, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 185:this.$ = [1, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 186:this.$ = [1, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 187:this.$ = [1e3, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 188:this.$ = [1e-3, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 189:this.$ = [1e-2, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 190:this.$ = [1e-3, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 191:this.$ = [1, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 192:this.$ = [1e-6, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 193:this.$ = [1e-5, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 194:this.$ = [1609.34, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 195:this.$ = [0.9144, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 196:this.$ = [0.3048, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 197:this.$ = [0.0254, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 198:this.$ = [1e-3, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 199:this.$ = [1e-5, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 200:this.$ = [1e-6, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 201:this.$ = [1e-9, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 202:this.$ = [0.45, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 203:this.$ = [0.028, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 204:this.$ = [1e3, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 205:this.$ = [1e-9, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 206:this.$ = [1e-6, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 207:this.$ = [1e-3, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 208:this.$ = [60, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 209:this.$ = [36e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 210:this.$ = [864e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 211:this.$ = [6048e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 212:this.$ = [2.63e+6, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 213:this.$ = [3.156e+7, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 214:this.$ = [1, 0, 0, 0, 0, 1, 0, 0, 0];
break;
case 215:
          var magnitude = (Number($$[$0-1])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 216:
          var magnitude = ((-1)*Number($$[$0-1])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 217:
          var magnitude = Number($$[$0-1]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 218:
          var magnitude = (-1)*Number($$[$0-2]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 219:
          this.$ = [(Number($$[$0-1])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 220:
          this.$ = [((-1)*Number($$[$0-1])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 221:
          this.$ = [Number($$[$0-1]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 222:
          this.$ = [(-1)*Number($$[$0-1]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 223:
          this.$ = [Number(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 224:
          this.$ = [(-1)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 225:
          this.$ = [(Math.PI)*Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 226:
          this.$ = [(-1)*(Math.PI)*Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 227:
          var magnitude = (Number($$[$0-2])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 228:
          var magnitude = Number($$[$0-2]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 229:
          this.$ = [(Number($$[$0-2])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 230:
          this.$ = [Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 231:
      this.$ = $$[$0];
     
break;
case 232:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 233:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 234:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 235:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-2])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 236:
      var arr1 = $$[$0-2];
      var arr2 = $$[$0];
      var res_arr = [(arr1[0]*arr2[0])];
      for(var i = 1; i < 9; i++)
      {
         res_arr[i] = arr1[i] + arr2[i];
      }
      this.$ = res_arr;
     
break;
case 237:
      var res_arr = $$[$0-2];
      var factor = Number($$[$0]);
      res_arr[0] = res_arr[0]*factor;
      this.$ = res_arr;
     
break;
case 238:
      var res_arr = $$[$0-4];
      var factor = Number($$[$0-1]);
      res_arr[0] = res_arr[0]*factor;
      this.$ = res_arr;
     
break;
case 239:
      var arr1 = $$[$0-2];
      var arr2 = $$[$0];
      var res_arr = [(arr1[0]/arr2[0])];
      for(var i = 1; i < 9; i++)
      {
         res_arr[i] = arr1[i] - arr2[i];
      }
      this.$ = res_arr;
    
break;
case 240:
      var res_arr = $$[$0-2];
      var factor = Number($$[$0]);
      if(factor == 0)
        throw new Error("Division by 0!");
      res_arr[0] = res_arr[0]/factor;
      this.$ = res_arr;
     
break;
case 241:
      var res_arr = $$[$0-4];
      var factor = Number($$[$0-1]);
      if(factor == 0)
        throw new Error("Division by 0!");
      res_arr[0] = res_arr[0]/factor;
      this.$ = res_arr;
     
break;
case 242:
      var arr1 = $$[$0-2];

      var power = Number($$[$0]);
      var res_arr = [Math.pow(arr1[0],power)];;
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
            res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
      console.log("fe"+res_arr);
     
break;
case 243:
      var arr1 = $$[$0-4];
      var power = Number($$[$0-1]);
      var res_arr = [Math.pow(arr1[0],power)];;
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
            res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
      console.log("fe"+res_arr);
     
break;
case 244:
      this.$ = $$[$0-1];
    
break;
case 245:
      var arr1 = $$[$0];
      var power = Number(2);
      var res_arr = [Math.pow(arr1[0],power)];
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
          res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
    
break;
case 246:
      var arr1 = $$[$0];
      var power = Number(3);
      var res_arr = [Math.pow(arr1[0],power)];
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
          res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
    
break;
case 247:
         var arr1 = $$[$0-2];
         var arr2 = $$[$0];
         for(var i = 1; i < 9; i++)
         {
          if(arr1[i] != arr2[i])
            throw new Error("Addition cannot be performed on different units!");
         }
         this.$ = arr1;
         this.$[0] = arr1[0] + arr2[0];
       
break;
case 248:
       var arr1 = $$[$0-2];
       var arr2 = $$[$0];
       for(var i = 1; i < 9; i++)
       {
        if(arr1[i] != arr2[i])
          throw new Error("Subtraction cannot be performed on different units!");
       }
       this.$ = arr1;
       this.$[0] = arr1[0] - arr2[0];
     
break;
case 249:
            this.$ = $$[$0];
          
break;
case 250:
            this.$ = $$[$0-1];
          
break;
case 251:
          if(Number($$[$0]) == 1)
            this.$ = "Re. "+($$[$0]).toString();
          else
            throw new Error("");
          
break;
case 252:this.$ = "Rs. "+($$[$0]).toString();
break;
case 253:
          if(Number($$[$0-1]) == 1)
            this.$ = "Re. "+ ($$[$0-1]).toString();
          else
            throw new Error("");
          
break;
case 254:this.$ = "Rs. "+ ($$[$0-1]).toString();
break;
}
},
table: [{3:1,4:2,7:[1,3],8:4,9:5,10:6,11:[1,27],12:[1,9],16:[1,7],18:[1,8],20:[1,10],21:11,22:12,23:21,24:[1,60],25:[1,59],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],119:18,124:[1,16],125:[1,17],126:[1,19],127:[1,20]},{1:[3]},{5:[1,104],6:[1,105],11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110]},{5:[1,111]},{5:[1,113],6:[1,112],11:[1,117],12:[1,118],13:[1,114],14:[1,115],15:[1,116]},{6:[1,119]},{5:[1,121],6:[1,120]},{5:[2,23],6:[2,23],8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],17:[1,122],18:[1,158],19:[2,23],25:[1,128],26:[1,129],28:[1,156],29:[1,157],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],120:[1,124],121:[1,125],122:[1,126],123:[1,127],124:[1,16],125:[1,17],126:[2,23],127:[2,23]},{4:159,8:160,9:161,11:[1,27],12:[1,9],16:[1,7],18:[1,8],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,59],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],119:18,124:[1,16],125:[1,17]},{16:[1,165],18:[1,163],23:164,24:[1,60],25:[1,166],27:[1,61],28:[1,167],29:[1,168]},{5:[2,17],6:[2,17],11:[2,17],12:[2,17],13:[2,17],14:[2,17],15:[2,17],19:[2,17]},{5:[2,18],6:[2,18],11:[2,18],12:[2,18],13:[2,18],14:[2,18],15:[2,18],19:[2,18],35:[1,169],36:[1,170]},{5:[2,19],6:[2,19],11:[2,19],12:[2,19],13:[2,19],14:[2,19],15:[2,19],19:[2,19]},{5:[2,231],6:[2,231],11:[2,231],12:[2,231],13:[2,231],14:[2,231],15:[2,231],19:[2,231]},{5:[2,29],6:[2,29],8:171,11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],16:[1,155],18:[1,158],19:[2,29],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17],126:[2,29],127:[2,29]},{5:[2,30],6:[2,30],8:172,11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],16:[1,155],18:[1,158],19:[2,30],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17],126:[2,30],127:[2,30]},{8:173,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:174,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{6:[2,249],19:[2,249]},{16:[1,176],23:175,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{16:[1,176],23:178,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,22],6:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],126:[1,179],127:[1,180]},{5:[2,31],6:[2,31],11:[2,31],12:[2,31],13:[2,31],14:[2,31],15:[2,31],19:[2,31],35:[2,31],36:[2,31],46:[1,181],49:[1,182],52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{5:[2,32],6:[2,32],11:[2,32],12:[2,32],13:[2,32],14:[2,32],15:[2,32],19:[2,32],35:[2,32],36:[2,32]},{5:[2,33],6:[2,33],11:[2,33],12:[2,33],13:[2,33],14:[2,33],15:[2,33],19:[2,33],35:[2,33],36:[2,33]},{5:[2,34],6:[2,34],11:[2,34],12:[2,34],13:[2,34],14:[2,34],15:[2,34],19:[2,34],35:[2,34],36:[2,34]},{5:[2,35],6:[2,35],11:[2,35],12:[2,35],13:[2,35],14:[2,35],15:[2,35],19:[2,35],35:[2,35],36:[2,35]},{16:[1,176],23:187,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,184],6:[2,184],11:[2,184],12:[2,184],13:[2,184],14:[2,184],15:[2,184],19:[2,184]},{5:[2,185],6:[2,185],11:[2,185],12:[2,185],13:[2,185],14:[2,185],15:[2,185],19:[2,185]},{5:[2,186],6:[2,186],11:[2,186],12:[2,186],13:[2,186],14:[2,186],15:[2,186],19:[2,186]},{5:[2,187],6:[2,187],11:[2,187],12:[2,187],13:[2,187],14:[2,187],15:[2,187],19:[2,187]},{5:[2,188],6:[2,188],11:[2,188],12:[2,188],13:[2,188],14:[2,188],15:[2,188],19:[2,188]},{5:[2,189],6:[2,189],11:[2,189],12:[2,189],13:[2,189],14:[2,189],15:[2,189],19:[2,189]},{5:[2,190],6:[2,190],11:[2,190],12:[2,190],13:[2,190],14:[2,190],15:[2,190],19:[2,190]},{5:[2,191],6:[2,191],11:[2,191],12:[2,191],13:[2,191],14:[2,191],15:[2,191],19:[2,191]},{5:[2,192],6:[2,192],11:[2,192],12:[2,192],13:[2,192],14:[2,192],15:[2,192],19:[2,192]},{5:[2,193],6:[2,193],11:[2,193],12:[2,193],13:[2,193],14:[2,193],15:[2,193],19:[2,193]},{5:[2,194],6:[2,194],11:[2,194],12:[2,194],13:[2,194],14:[2,194],15:[2,194],19:[2,194]},{5:[2,195],6:[2,195],11:[2,195],12:[2,195],13:[2,195],14:[2,195],15:[2,195],19:[2,195]},{5:[2,196],6:[2,196],11:[2,196],12:[2,196],13:[2,196],14:[2,196],15:[2,196],19:[2,196]},{5:[2,197],6:[2,197],11:[2,197],12:[2,197],13:[2,197],14:[2,197],15:[2,197],19:[2,197]},{5:[2,198],6:[2,198],11:[2,198],12:[2,198],13:[2,198],14:[2,198],15:[2,198],19:[2,198]},{5:[2,199],6:[2,199],11:[2,199],12:[2,199],13:[2,199],14:[2,199],15:[2,199],19:[2,199]},{5:[2,200],6:[2,200],11:[2,200],12:[2,200],13:[2,200],14:[2,200],15:[2,200],19:[2,200]},{5:[2,201],6:[2,201],11:[2,201],12:[2,201],13:[2,201],14:[2,201],15:[2,201],19:[2,201]},{5:[2,202],6:[2,202],11:[2,202],12:[2,202],13:[2,202],14:[2,202],15:[2,202],19:[2,202]},{5:[2,203],6:[2,203],11:[2,203],12:[2,203],13:[2,203],14:[2,203],15:[2,203],19:[2,203]},{5:[2,204],6:[2,204],11:[2,204],12:[2,204],13:[2,204],14:[2,204],15:[2,204],19:[2,204]},{5:[2,205],6:[2,205],11:[2,205],12:[2,205],13:[2,205],14:[2,205],15:[2,205],19:[2,205]},{5:[2,206],6:[2,206],11:[2,206],12:[2,206],13:[2,206],14:[2,206],15:[2,206],19:[2,206]},{5:[2,207],6:[2,207],11:[2,207],12:[2,207],13:[2,207],14:[2,207],15:[2,207],19:[2,207]},{5:[2,208],6:[2,208],11:[2,208],12:[2,208],13:[2,208],14:[2,208],15:[2,208],19:[2,208]},{5:[2,209],6:[2,209],11:[2,209],12:[2,209],13:[2,209],14:[2,209],15:[2,209],19:[2,209]},{5:[2,210],6:[2,210],11:[2,210],12:[2,210],13:[2,210],14:[2,210],15:[2,210],19:[2,210]},{5:[2,211],6:[2,211],11:[2,211],12:[2,211],13:[2,211],14:[2,211],15:[2,211],19:[2,211]},{5:[2,212],6:[2,212],11:[2,212],12:[2,212],13:[2,212],14:[2,212],15:[2,212],19:[2,212]},{5:[2,213],6:[2,213],11:[2,213],12:[2,213],13:[2,213],14:[2,213],15:[2,213],19:[2,213]},{5:[2,214],6:[2,214],11:[2,214],12:[2,214],13:[2,214],14:[2,214],15:[2,214],19:[2,214]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25],123:[1,188],126:[2,25],127:[2,25]},{5:[2,24],6:[2,24],11:[2,24],12:[2,24],13:[2,24],14:[2,24],15:[2,24],19:[2,24],126:[2,24],127:[2,24]},{18:[1,189]},{5:[2,62],6:[2,62],11:[2,62],12:[2,62],13:[2,62],14:[2,62],15:[2,62],19:[2,62],35:[2,62],36:[2,62],46:[2,62],48:[1,190],49:[2,62],52:[2,62],56:[2,62],65:[2,62],67:[2,62]},{5:[2,63],6:[2,63],11:[2,63],12:[2,63],13:[2,63],14:[2,63],15:[2,63],16:[1,197],19:[2,63],35:[2,63],36:[2,63],42:195,46:[2,63],47:191,49:[2,63],50:[1,196],52:[2,63],53:94,54:[1,198],56:[2,63],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,63],66:[1,102],67:[2,63],84:[1,192],85:[1,193],86:[1,194]},{5:[2,58],6:[2,58],11:[2,58],12:[2,58],13:[2,58],14:[2,58],15:[2,58],19:[2,58],35:[2,58],36:[2,58]},{5:[2,42],6:[2,42],11:[2,42],12:[2,42],13:[2,42],14:[2,42],15:[2,42],19:[2,42],35:[2,42],36:[2,42],38:[1,202],40:[1,201]},{5:[2,43],6:[2,43],11:[2,43],12:[2,43],13:[2,43],14:[2,43],15:[2,43],16:[1,204],19:[2,43],35:[2,43],36:[2,43],39:203,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,38],6:[2,38],11:[2,38],12:[2,38],13:[2,38],14:[2,38],15:[2,38],19:[2,38],35:[2,38],36:[2,38]},{5:[2,117],6:[2,117],11:[2,117],12:[2,117],13:[2,117],14:[2,117],15:[2,117],19:[2,117],35:[2,117],36:[2,117]},{5:[2,118],6:[2,118],11:[2,118],12:[2,118],13:[2,118],14:[2,118],15:[2,118],19:[2,118],35:[2,118],36:[2,118]},{5:[2,119],6:[2,119],11:[2,119],12:[2,119],13:[2,119],14:[2,119],15:[2,119],19:[2,119],35:[2,119],36:[2,119]},{5:[2,120],6:[2,120],11:[2,120],12:[2,120],13:[2,120],14:[2,120],15:[2,120],19:[2,120],35:[2,120],36:[2,120]},{5:[2,121],6:[2,121],11:[2,121],12:[2,121],13:[2,121],14:[2,121],15:[2,121],19:[2,121],35:[2,121],36:[2,121]},{5:[2,122],6:[2,122],11:[2,122],12:[2,122],13:[2,122],14:[2,122],15:[2,122],19:[2,122],35:[2,122],36:[2,122]},{5:[2,123],6:[2,123],11:[2,123],12:[2,123],13:[2,123],14:[2,123],15:[2,123],19:[2,123],35:[2,123],36:[2,123]},{5:[2,124],6:[2,124],11:[2,124],12:[2,124],13:[2,124],14:[2,124],15:[2,124],19:[2,124],35:[2,124],36:[2,124]},{5:[2,125],6:[2,125],11:[2,125],12:[2,125],13:[2,125],14:[2,125],15:[2,125],19:[2,125],35:[2,125],36:[2,125]},{5:[2,126],6:[2,126],11:[2,126],12:[2,126],13:[2,126],14:[2,126],15:[2,126],19:[2,126],35:[2,126],36:[2,126]},{5:[2,73],6:[2,73],11:[2,73],12:[2,73],13:[2,73],14:[2,73],15:[2,73],19:[2,73],35:[2,73],36:[2,73],41:[1,208],44:[1,207],46:[2,73],48:[2,73],49:[2,73],50:[1,206],52:[2,73],56:[2,73],65:[2,73],67:[2,73]},{5:[2,74],6:[2,74],11:[2,74],12:[2,74],13:[2,74],14:[2,74],15:[2,74],16:[1,219],19:[2,74],35:[2,74],36:[2,74],42:209,46:[2,74],48:[2,74],49:[2,74],52:[2,74],53:94,54:[1,198],56:[2,74],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,74],66:[1,102],67:[2,74],78:[1,214],79:[1,215],80:[1,212],81:[1,213],82:[1,211],83:[1,210],84:[1,216],85:[1,217],86:[1,218]},{5:[2,69],6:[2,69],11:[2,69],12:[2,69],13:[2,69],14:[2,69],15:[2,69],19:[2,69],35:[2,69],36:[2,69]},{5:[2,52],6:[2,52],11:[2,52],12:[2,52],13:[2,52],14:[2,52],15:[2,52],16:[1,219],19:[2,52],35:[2,52],36:[2,52],38:[2,52],40:[2,52],42:220,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,157],6:[2,157],11:[2,157],12:[2,157],13:[2,157],14:[2,157],15:[2,157],19:[2,157],35:[2,157],36:[2,157]},{5:[2,95],6:[2,95],11:[2,95],12:[2,95],13:[2,95],14:[2,95],15:[2,95],19:[2,95],35:[2,95],36:[2,95],41:[2,95],44:[2,95],46:[2,95],48:[2,95],49:[2,95],50:[2,95],52:[2,95],54:[2,95],56:[2,95],58:[1,230],65:[2,95],67:[2,95],78:[1,225],79:[1,226],80:[1,223],81:[1,224],82:[1,222],83:[1,221],84:[1,227],85:[1,228],86:[1,229]},{5:[2,96],6:[2,96],11:[2,96],12:[2,96],13:[2,96],14:[2,96],15:[2,96],19:[2,96],35:[2,96],36:[2,96],41:[2,96],44:[2,96],46:[2,96],48:[2,96],49:[2,96],50:[2,96],52:[2,96],54:[2,96],56:[2,96],57:240,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,96],66:[1,102],67:[2,96],78:[1,235],79:[1,236],80:[1,233],81:[1,234],82:[1,232],83:[1,231],84:[1,237],85:[1,238],86:[1,239]},{5:[2,85],6:[2,85],11:[2,85],12:[2,85],13:[2,85],14:[2,85],15:[2,85],16:[1,251],19:[2,85],35:[2,85],36:[2,85],41:[2,85],44:[2,85],46:[2,85],48:[2,85],49:[2,85],50:[2,85],52:[2,85],53:250,56:[2,85],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,85],66:[1,102],67:[2,85],78:[1,245],79:[1,246],80:[1,243],81:[1,244],82:[1,242],83:[1,241],84:[1,247],85:[1,248],86:[1,249]},{5:[2,151],6:[2,151],11:[2,151],12:[2,151],13:[2,151],14:[2,151],15:[2,151],19:[2,151],35:[2,151],36:[2,151]},{5:[2,139],6:[2,139],11:[2,139],12:[2,139],13:[2,139],14:[2,139],15:[2,139],19:[2,139],35:[2,139],36:[2,139]},{5:[2,145],6:[2,145],11:[2,145],12:[2,145],13:[2,145],14:[2,145],15:[2,145],19:[2,145],35:[2,145],36:[2,145]},{5:[2,127],6:[2,127],11:[2,127],12:[2,127],13:[2,127],14:[2,127],15:[2,127],19:[2,127],35:[2,127],36:[2,127]},{5:[2,133],6:[2,133],11:[2,133],12:[2,133],13:[2,133],14:[2,133],15:[2,133],19:[2,133],35:[2,133],36:[2,133]},{5:[2,163],6:[2,163],11:[2,163],12:[2,163],13:[2,163],14:[2,163],15:[2,163],19:[2,163],35:[2,163],36:[2,163]},{5:[2,170],6:[2,170],11:[2,170],12:[2,170],13:[2,170],14:[2,170],15:[2,170],19:[2,170],35:[2,170],36:[2,170]},{5:[2,177],6:[2,177],11:[2,177],12:[2,177],13:[2,177],14:[2,177],15:[2,177],19:[2,177],35:[2,177],36:[2,177]},{5:[2,84],6:[2,84],11:[2,84],12:[2,84],13:[2,84],14:[2,84],15:[2,84],19:[2,84],35:[2,84],36:[2,84],38:[2,84],40:[2,84],41:[2,84],44:[2,84],46:[2,84],48:[2,84],49:[2,84],50:[2,84],52:[2,84],54:[1,252],56:[2,84],65:[2,84],67:[2,84]},{5:[2,80],6:[2,80],11:[2,80],12:[2,80],13:[2,80],14:[2,80],15:[2,80],19:[2,80],35:[2,80],36:[2,80]},{5:[2,102],6:[2,102],11:[2,102],12:[2,102],13:[2,102],14:[2,102],15:[2,102],19:[2,102],35:[2,102],36:[2,102],38:[2,102],40:[2,102],41:[2,102],44:[2,102],46:[2,102],48:[2,102],49:[2,102],50:[2,102],52:[2,102],54:[2,102],56:[2,102],58:[2,102],65:[2,102],67:[2,102],78:[2,102],79:[2,102],80:[2,102],81:[2,102],82:[2,102],83:[2,102],84:[2,102],85:[2,102],86:[2,102]},{5:[2,103],6:[2,103],11:[2,103],12:[2,103],13:[2,103],14:[2,103],15:[2,103],19:[2,103],35:[2,103],36:[2,103],38:[2,103],40:[2,103],41:[2,103],44:[2,103],46:[2,103],48:[2,103],49:[2,103],50:[2,103],52:[2,103],54:[2,103],56:[2,103],58:[2,103],65:[2,103],67:[2,103],78:[2,103],79:[2,103],80:[2,103],81:[2,103],82:[2,103],83:[2,103],84:[2,103],85:[2,103],86:[2,103]},{5:[2,104],6:[2,104],11:[2,104],12:[2,104],13:[2,104],14:[2,104],15:[2,104],19:[2,104],35:[2,104],36:[2,104],38:[2,104],40:[2,104],41:[2,104],44:[2,104],46:[2,104],48:[2,104],49:[2,104],50:[2,104],52:[2,104],54:[2,104],56:[2,104],58:[2,104],65:[2,104],67:[2,104],78:[2,104],79:[2,104],80:[2,104],81:[2,104],82:[2,104],83:[2,104],84:[2,104],85:[2,104],86:[2,104]},{5:[2,105],6:[2,105],11:[2,105],12:[2,105],13:[2,105],14:[2,105],15:[2,105],19:[2,105],35:[2,105],36:[2,105],38:[2,105],40:[2,105],41:[2,105],44:[2,105],46:[2,105],48:[2,105],49:[2,105],50:[2,105],52:[2,105],54:[2,105],56:[2,105],58:[2,105],59:253,65:[2,105],66:[1,102],67:[2,105],78:[2,105],79:[2,105],80:[2,105],81:[2,105],82:[2,105],83:[2,105],84:[2,105],85:[2,105],86:[2,105]},{5:[2,107],6:[2,107],11:[2,107],12:[2,107],13:[2,107],14:[2,107],15:[2,107],19:[2,107],35:[2,107],36:[2,107],38:[2,107],40:[2,107],41:[2,107],44:[2,107],46:[2,107],48:[2,107],49:[2,107],50:[2,107],52:[2,107],54:[2,107],56:[2,107],58:[2,107],65:[2,107],67:[2,107],78:[2,107],79:[2,107],80:[2,107],81:[2,107],82:[2,107],83:[2,107],84:[2,107],85:[2,107],86:[2,107]},{5:[2,91],6:[2,91],11:[2,91],12:[2,91],13:[2,91],14:[2,91],15:[2,91],19:[2,91],35:[2,91],36:[2,91]},{5:[2,113],6:[2,113],11:[2,113],12:[2,113],13:[2,113],14:[2,113],15:[2,113],19:[2,113],35:[2,113],36:[2,113],38:[2,113],40:[2,113],41:[2,113],44:[2,113],46:[2,113],48:[2,113],49:[2,113],50:[2,113],52:[2,113],54:[2,113],56:[2,113],58:[2,113],65:[2,113],67:[2,113],78:[2,113],79:[2,113],80:[2,113],81:[2,113],82:[2,113],83:[2,113],84:[2,113],85:[2,113],86:[2,113]},{5:[2,108],6:[2,108],11:[2,108],12:[2,108],13:[2,108],14:[2,108],15:[2,108],19:[2,108],35:[2,108],36:[2,108]},{6:[1,254]},{1:[2,2]},{4:255,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:259,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:260,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:261,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:262,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{6:[1,263]},{1:[2,4]},{6:[1,264]},{8:265,11:[1,27],12:[1,269],16:[1,268],18:[1,267],22:266,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:270,11:[1,27],12:[1,269],16:[1,268],18:[1,272],22:271,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{11:[1,27],12:[1,269],16:[1,176],18:[1,274],22:273,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{8:275,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:276,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{1:[2,6]},{1:[2,7]},{6:[1,277]},{5:[2,14],6:[2,14],11:[2,14],12:[2,14],13:[2,14],14:[2,14],15:[2,14],19:[2,14]},{5:[2,232],6:[2,232],11:[2,232],12:[2,232],13:[2,232],14:[2,232],15:[1,116],19:[2,232]},{6:[2,215],19:[2,215]},{6:[2,217],19:[2,217]},{6:[2,219],19:[2,219]},{6:[2,221],19:[2,221]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26],123:[1,278],126:[2,26],127:[2,26]},{5:[2,27],6:[2,27],11:[2,27],12:[2,27],13:[2,27],14:[2,27],15:[2,27],19:[2,27],126:[2,27],127:[2,27]},{5:[2,64],6:[2,64],11:[2,64],12:[2,64],13:[2,64],14:[2,64],15:[2,64],16:[1,197],19:[2,64],35:[2,64],36:[2,64],42:195,46:[2,64],47:279,49:[2,64],50:[1,196],52:[2,64],53:94,54:[1,198],56:[2,64],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,64],66:[1,102],67:[2,64]},{5:[2,59],6:[2,59],11:[2,59],12:[2,59],13:[2,59],14:[2,59],15:[2,59],19:[2,59],35:[2,59],36:[2,59]},{5:[2,44],6:[2,44],11:[2,44],12:[2,44],13:[2,44],14:[2,44],15:[2,44],16:[1,204],19:[2,44],35:[2,44],36:[2,44],39:280,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,39],6:[2,39],11:[2,39],12:[2,39],13:[2,39],14:[2,39],15:[2,39],19:[2,39],35:[2,39],36:[2,39]},{5:[2,75],6:[2,75],11:[2,75],12:[2,75],13:[2,75],14:[2,75],15:[2,75],16:[1,219],19:[2,75],35:[2,75],36:[2,75],42:281,46:[2,75],48:[2,75],49:[2,75],52:[2,75],53:94,54:[1,198],56:[2,75],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,75],66:[1,102],67:[2,75]},{5:[2,70],6:[2,70],11:[2,70],12:[2,70],13:[2,70],14:[2,70],15:[2,70],19:[2,70],35:[2,70],36:[2,70]},{5:[2,53],6:[2,53],11:[2,53],12:[2,53],13:[2,53],14:[2,53],15:[2,53],16:[1,219],19:[2,53],35:[2,53],36:[2,53],38:[2,53],40:[2,53],42:282,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,49],6:[2,49],11:[2,49],12:[2,49],13:[2,49],14:[2,49],15:[2,49],19:[2,49],35:[2,49],36:[2,49]},{5:[2,158],6:[2,158],11:[2,158],12:[2,158],13:[2,158],14:[2,158],15:[2,158],19:[2,158],35:[2,158],36:[2,158]},{5:[2,152],6:[2,152],11:[2,152],12:[2,152],13:[2,152],14:[2,152],15:[2,152],19:[2,152],35:[2,152],36:[2,152]},{5:[2,140],6:[2,140],11:[2,140],12:[2,140],13:[2,140],14:[2,140],15:[2,140],19:[2,140],35:[2,140],36:[2,140]},{5:[2,146],6:[2,146],11:[2,146],12:[2,146],13:[2,146],14:[2,146],15:[2,146],19:[2,146],35:[2,146],36:[2,146]},{5:[2,128],6:[2,128],11:[2,128],12:[2,128],13:[2,128],14:[2,128],15:[2,128],19:[2,128],35:[2,128],36:[2,128]},{5:[2,134],6:[2,134],11:[2,134],12:[2,134],13:[2,134],14:[2,134],15:[2,134],19:[2,134],35:[2,134],36:[2,134]},{5:[2,164],6:[2,164],11:[2,164],12:[2,164],13:[2,164],14:[2,164],15:[2,164],19:[2,164],35:[2,164],36:[2,164]},{5:[2,171],6:[2,171],11:[2,171],12:[2,171],13:[2,171],14:[2,171],15:[2,171],19:[2,171],35:[2,171],36:[2,171]},{5:[2,178],6:[2,178],11:[2,178],12:[2,178],13:[2,178],14:[2,178],15:[2,178],19:[2,178],35:[2,178],36:[2,178]},{5:[2,86],6:[2,86],11:[2,86],12:[2,86],13:[2,86],14:[2,86],15:[2,86],16:[1,251],19:[2,86],35:[2,86],36:[2,86],38:[2,86],40:[2,86],41:[2,86],44:[2,86],46:[2,86],48:[2,86],49:[2,86],50:[2,86],52:[2,86],53:283,56:[2,86],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,86],66:[1,102],67:[2,86]},{5:[2,81],6:[2,81],11:[2,81],12:[2,81],13:[2,81],14:[2,81],15:[2,81],19:[2,81],35:[2,81],36:[2,81]},{5:[2,97],6:[2,97],11:[2,97],12:[2,97],13:[2,97],14:[2,97],15:[2,97],19:[2,97],35:[2,97],36:[2,97],38:[2,97],40:[2,97],41:[2,97],44:[2,97],46:[2,97],48:[2,97],49:[2,97],50:[2,97],52:[2,97],54:[2,97],56:[2,97],57:284,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,97],66:[1,102],67:[2,97]},{5:[2,92],6:[2,92],11:[2,92],12:[2,92],13:[2,92],14:[2,92],15:[2,92],19:[2,92],35:[2,92],36:[2,92]},{5:[2,109],6:[2,109],11:[2,109],12:[2,109],13:[2,109],14:[2,109],15:[2,109],19:[2,109],35:[2,109],36:[2,109]},{5:[2,110],6:[2,110],11:[2,110],12:[2,110],13:[2,110],14:[2,110],15:[2,110],19:[2,110],35:[2,110],36:[2,110]},{5:[2,114],6:[2,114],11:[2,114],12:[2,114],13:[2,114],14:[2,114],15:[2,114],19:[2,114],35:[2,114],36:[2,114]},{5:[2,115],6:[2,115],11:[2,115],12:[2,115],13:[2,115],14:[2,115],15:[2,115],19:[2,115],35:[2,115],36:[2,115]},{8:123,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:171,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:172,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{4:285,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,288]},{11:[1,117],12:[1,118],13:[1,114],14:[1,115],15:[1,116],19:[1,289]},{19:[1,290]},{5:[2,22],6:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],19:[2,22]},{4:291,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,21],6:[2,21],11:[2,21],12:[2,21],13:[2,21],14:[2,21],15:[2,21],19:[2,21]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],19:[2,23],25:[1,296],26:[1,129],120:[1,292],121:[1,293],122:[1,294],123:[1,295]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25],123:[1,297]},{5:[2,29],6:[2,29],11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],19:[2,29]},{5:[2,30],6:[2,30],11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],19:[2,30]},{16:[1,299],21:298,30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{16:[1,299],21:300,30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,233],6:[2,233],11:[2,233],12:[2,233],13:[2,233],14:[2,233],15:[1,116],19:[2,233]},{5:[2,234],6:[2,234],11:[2,234],12:[2,234],13:[2,234],14:[2,234],15:[1,116],19:[2,234]},{5:[2,245],6:[2,245],11:[2,245],12:[2,245],13:[2,245],14:[2,245],15:[2,245],19:[2,245]},{5:[2,246],6:[2,246],11:[2,246],12:[2,246],13:[2,246],14:[2,246],15:[2,246],19:[2,246]},{5:[2,251],6:[2,251]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],19:[2,23],25:[1,301],26:[1,129]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25]},{5:[2,252],6:[2,252]},{5:[2,253],6:[2,253]},{5:[2,254],6:[2,254]},{5:[2,60],6:[2,60],11:[2,60],12:[2,60],13:[2,60],14:[2,60],15:[2,60],16:[1,303],19:[2,60],30:304,35:[2,60],36:[2,60],42:195,43:80,45:302,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,71],6:[2,71],11:[2,71],12:[2,71],13:[2,71],14:[2,71],15:[2,71],16:[1,307],19:[2,71],30:308,35:[2,71],36:[2,71],42:195,43:306,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,82],6:[2,82],11:[2,82],12:[2,82],13:[2,82],14:[2,82],15:[2,82],16:[1,310],19:[2,82],30:311,35:[2,82],36:[2,82],42:195,47:62,48:[1,305],50:[1,196],51:309,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,93],6:[2,93],11:[2,93],12:[2,93],13:[2,93],14:[2,93],15:[2,93],16:[1,313],19:[2,93],30:314,35:[2,93],36:[2,93],42:195,47:62,48:[1,305],50:[1,196],53:94,54:[1,198],55:312,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,111],6:[2,111],11:[2,111],12:[2,111],13:[2,111],14:[2,111],15:[2,111],16:[1,316],19:[2,111],30:317,35:[2,111],36:[2,111],42:195,47:62,48:[1,305],50:[1,196],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:315,66:[1,102]},{5:[2,116],6:[2,116],11:[2,116],12:[2,116],13:[2,116],14:[2,116],15:[2,116],19:[2,116],35:[2,116],36:[2,116]},{5:[2,20],6:[2,20],11:[2,20],12:[2,20],13:[2,20],14:[2,20],15:[2,20],19:[2,20]},{6:[2,223],19:[2,223]},{4:318,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,65],6:[2,65],11:[2,65],12:[2,65],13:[2,65],14:[2,65],15:[2,65],16:[1,197],19:[2,65],35:[2,65],36:[2,65],42:195,46:[2,65],47:319,49:[2,65],50:[1,196],52:[2,65],53:94,54:[1,198],56:[2,65],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,65],66:[1,102],67:[2,65]},{5:[2,66],6:[2,66],11:[2,66],12:[2,66],13:[2,66],14:[2,66],15:[2,66],19:[2,66],35:[2,66],36:[2,66],46:[2,66],49:[2,66],52:[2,66],56:[2,66],65:[2,66],67:[2,66]},{5:[2,169],6:[2,169],11:[2,169],12:[2,169],13:[2,169],14:[2,169],15:[2,169],19:[2,169],35:[2,169],36:[2,169]},{5:[2,176],6:[2,176],11:[2,176],12:[2,176],13:[2,176],14:[2,176],15:[2,176],19:[2,176],35:[2,176],36:[2,176]},{5:[2,183],6:[2,183],11:[2,183],12:[2,183],13:[2,183],14:[2,183],15:[2,183],19:[2,183],35:[2,183],36:[2,183]},{5:[2,73],6:[2,73],11:[2,73],12:[2,73],13:[2,73],14:[2,73],15:[2,73],19:[2,73],35:[2,73],36:[2,73],46:[2,73],48:[2,73],49:[2,73],50:[1,206],52:[2,73],56:[2,73],65:[2,73],67:[2,73]},{5:[2,74],6:[2,74],11:[2,74],12:[2,74],13:[2,74],14:[2,74],15:[2,74],16:[1,219],19:[2,74],35:[2,74],36:[2,74],42:209,46:[2,74],48:[2,74],49:[2,74],52:[2,74],53:94,54:[1,198],56:[2,74],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,74],66:[1,102],67:[2,74]},{50:[1,134],54:[1,147],58:[1,149]},{5:[2,85],6:[2,85],11:[2,85],12:[2,85],13:[2,85],14:[2,85],15:[2,85],16:[1,251],19:[2,85],35:[2,85],36:[2,85],38:[2,85],40:[2,85],41:[2,85],44:[2,85],46:[2,85],48:[2,85],49:[2,85],50:[2,85],52:[2,85],53:250,56:[2,85],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,85],66:[1,102],67:[2,85]},{5:[2,95],6:[2,95],11:[2,95],12:[2,95],13:[2,95],14:[2,95],15:[2,95],19:[2,95],35:[2,95],36:[2,95],38:[2,95],40:[2,95],41:[2,95],44:[2,95],46:[2,95],48:[2,95],49:[2,95],50:[2,95],52:[2,95],54:[2,95],56:[2,95],58:[1,230],65:[2,95],67:[2,95]},{5:[2,96],6:[2,96],11:[2,96],12:[2,96],13:[2,96],14:[2,96],15:[2,96],19:[2,96],35:[2,96],36:[2,96],38:[2,96],40:[2,96],41:[2,96],44:[2,96],46:[2,96],48:[2,96],49:[2,96],50:[2,96],52:[2,96],54:[2,96],56:[2,96],57:240,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,96],66:[1,102],67:[2,96]},{5:[2,45],6:[2,45],11:[2,45],12:[2,45],13:[2,45],14:[2,45],15:[2,45],16:[1,204],19:[2,45],35:[2,45],36:[2,45],39:320,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,40],6:[2,40],11:[2,40],12:[2,40],13:[2,40],14:[2,40],15:[2,40],16:[1,322],19:[2,40],35:[2,40],36:[2,40],37:321,42:323,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,46],6:[2,46],11:[2,46],12:[2,46],13:[2,46],14:[2,46],15:[2,46],19:[2,46],35:[2,46],36:[2,46]},{44:[1,136],54:[1,147],58:[1,149]},{44:[1,207]},{5:[2,76],6:[2,76],11:[2,76],12:[2,76],13:[2,76],14:[2,76],15:[2,76],16:[1,219],19:[2,76],35:[2,76],36:[2,76],42:324,46:[2,76],48:[2,76],49:[2,76],52:[2,76],53:94,54:[1,198],56:[2,76],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,76],66:[1,102],67:[2,76]},{5:[2,54],6:[2,54],11:[2,54],12:[2,54],13:[2,54],14:[2,54],15:[2,54],16:[1,219],19:[2,54],35:[2,54],36:[2,54],38:[2,54],40:[2,54],42:325,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,50],6:[2,50],11:[2,50],12:[2,50],13:[2,50],14:[2,50],15:[2,50],16:[1,307],19:[2,50],30:308,35:[2,50],36:[2,50],42:195,43:326,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,77],6:[2,77],11:[2,77],12:[2,77],13:[2,77],14:[2,77],15:[2,77],19:[2,77],35:[2,77],36:[2,77],46:[2,77],48:[2,77],49:[2,77],52:[2,77],56:[2,77],65:[2,77],67:[2,77]},{5:[2,162],6:[2,162],11:[2,162],12:[2,162],13:[2,162],14:[2,162],15:[2,162],19:[2,162],35:[2,162],36:[2,162]},{5:[2,156],6:[2,156],11:[2,156],12:[2,156],13:[2,156],14:[2,156],15:[2,156],19:[2,156],35:[2,156],36:[2,156]},{5:[2,144],6:[2,144],11:[2,144],12:[2,144],13:[2,144],14:[2,144],15:[2,144],19:[2,144],35:[2,144],36:[2,144]},{5:[2,150],6:[2,150],11:[2,150],12:[2,150],13:[2,150],14:[2,150],15:[2,150],19:[2,150],35:[2,150],36:[2,150]},{5:[2,132],6:[2,132],11:[2,132],12:[2,132],13:[2,132],14:[2,132],15:[2,132],19:[2,132],35:[2,132],36:[2,132]},{5:[2,138],6:[2,138],11:[2,138],12:[2,138],13:[2,138],14:[2,138],15:[2,138],19:[2,138],35:[2,138],36:[2,138]},{5:[2,168],6:[2,168],11:[2,168],12:[2,168],13:[2,168],14:[2,168],15:[2,168],19:[2,168],35:[2,168],36:[2,168]},{5:[2,175],6:[2,175],11:[2,175],12:[2,175],13:[2,175],14:[2,175],15:[2,175],19:[2,175],35:[2,175],36:[2,175]},{5:[2,182],6:[2,182],11:[2,182],12:[2,182],13:[2,182],14:[2,182],15:[2,182],19:[2,182],35:[2,182],36:[2,182]},{54:[1,147],58:[1,149]},{5:[2,55],6:[2,55],11:[2,55],12:[2,55],13:[2,55],14:[2,55],15:[2,55],19:[2,55],35:[2,55],36:[2,55],38:[2,55],40:[2,55]},{5:[2,159],6:[2,159],11:[2,159],12:[2,159],13:[2,159],14:[2,159],15:[2,159],19:[2,159],35:[2,159],36:[2,159]},{5:[2,153],6:[2,153],11:[2,153],12:[2,153],13:[2,153],14:[2,153],15:[2,153],19:[2,153],35:[2,153],36:[2,153]},{5:[2,141],6:[2,141],11:[2,141],12:[2,141],13:[2,141],14:[2,141],15:[2,141],19:[2,141],35:[2,141],36:[2,141]},{5:[2,147],6:[2,147],11:[2,147],12:[2,147],13:[2,147],14:[2,147],15:[2,147],19:[2,147],35:[2,147],36:[2,147]},{5:[2,129],6:[2,129],11:[2,129],12:[2,129],13:[2,129],14:[2,129],15:[2,129],19:[2,129],35:[2,129],36:[2,129]},{5:[2,135],6:[2,135],11:[2,135],12:[2,135],13:[2,135],14:[2,135],15:[2,135],19:[2,135],35:[2,135],36:[2,135]},{5:[2,165],6:[2,165],11:[2,165],12:[2,165],13:[2,165],14:[2,165],15:[2,165],19:[2,165],35:[2,165],36:[2,165]},{5:[2,172],6:[2,172],11:[2,172],12:[2,172],13:[2,172],14:[2,172],15:[2,172],19:[2,172],35:[2,172],36:[2,172]},{5:[2,179],6:[2,179],11:[2,179],12:[2,179],13:[2,179],14:[2,179],15:[2,179],19:[2,179],35:[2,179],36:[2,179]},{5:[2,98],6:[2,98],11:[2,98],12:[2,98],13:[2,98],14:[2,98],15:[2,98],19:[2,98],35:[2,98],36:[2,98],38:[2,98],40:[2,98],41:[2,98],44:[2,98],46:[2,98],48:[2,98],49:[2,98],50:[2,98],52:[2,98],54:[2,98],56:[2,98],57:327,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,98],66:[1,102],67:[2,98]},{5:[2,160],6:[2,160],11:[2,160],12:[2,160],13:[2,160],14:[2,160],15:[2,160],19:[2,160],35:[2,160],36:[2,160]},{5:[2,154],6:[2,154],11:[2,154],12:[2,154],13:[2,154],14:[2,154],15:[2,154],19:[2,154],35:[2,154],36:[2,154]},{5:[2,142],6:[2,142],11:[2,142],12:[2,142],13:[2,142],14:[2,142],15:[2,142],19:[2,142],35:[2,142],36:[2,142]},{5:[2,148],6:[2,148],11:[2,148],12:[2,148],13:[2,148],14:[2,148],15:[2,148],19:[2,148],35:[2,148],36:[2,148]},{5:[2,130],6:[2,130],11:[2,130],12:[2,130],13:[2,130],14:[2,130],15:[2,130],19:[2,130],35:[2,130],36:[2,130]},{5:[2,136],6:[2,136],11:[2,136],12:[2,136],13:[2,136],14:[2,136],15:[2,136],19:[2,136],35:[2,136],36:[2,136]},{5:[2,166],6:[2,166],11:[2,166],12:[2,166],13:[2,166],14:[2,166],15:[2,166],19:[2,166],35:[2,166],36:[2,166]},{5:[2,173],6:[2,173],11:[2,173],12:[2,173],13:[2,173],14:[2,173],15:[2,173],19:[2,173],35:[2,173],36:[2,173]},{5:[2,180],6:[2,180],11:[2,180],12:[2,180],13:[2,180],14:[2,180],15:[2,180],19:[2,180],35:[2,180],36:[2,180]},{5:[2,99],6:[2,99],11:[2,99],12:[2,99],13:[2,99],14:[2,99],15:[2,99],19:[2,99],35:[2,99],36:[2,99],38:[2,99],40:[2,99],41:[2,99],44:[2,99],46:[2,99],48:[2,99],49:[2,99],50:[2,99],52:[2,99],54:[2,99],56:[2,99],65:[2,99],67:[2,99]},{5:[2,161],6:[2,161],11:[2,161],12:[2,161],13:[2,161],14:[2,161],15:[2,161],19:[2,161],35:[2,161],36:[2,161]},{5:[2,155],6:[2,155],11:[2,155],12:[2,155],13:[2,155],14:[2,155],15:[2,155],19:[2,155],35:[2,155],36:[2,155]},{5:[2,143],6:[2,143],11:[2,143],12:[2,143],13:[2,143],14:[2,143],15:[2,143],19:[2,143],35:[2,143],36:[2,143]},{5:[2,149],6:[2,149],11:[2,149],12:[2,149],13:[2,149],14:[2,149],15:[2,149],19:[2,149],35:[2,149],36:[2,149]},{5:[2,131],6:[2,131],11:[2,131],12:[2,131],13:[2,131],14:[2,131],15:[2,131],19:[2,131],35:[2,131],36:[2,131]},{5:[2,137],6:[2,137],11:[2,137],12:[2,137],13:[2,137],14:[2,137],15:[2,137],19:[2,137],35:[2,137],36:[2,137]},{5:[2,167],6:[2,167],11:[2,167],12:[2,167],13:[2,167],14:[2,167],15:[2,167],19:[2,167],35:[2,167],36:[2,167]},{5:[2,174],6:[2,174],11:[2,174],12:[2,174],13:[2,174],14:[2,174],15:[2,174],19:[2,174],35:[2,174],36:[2,174]},{5:[2,181],6:[2,181],11:[2,181],12:[2,181],13:[2,181],14:[2,181],15:[2,181],19:[2,181],35:[2,181],36:[2,181]},{5:[2,88],6:[2,88],11:[2,88],12:[2,88],13:[2,88],14:[2,88],15:[2,88],19:[2,88],35:[2,88],36:[2,88],38:[2,88],40:[2,88],41:[2,88],44:[2,88],46:[2,88],48:[2,88],49:[2,88],50:[2,88],52:[2,88],56:[2,88],65:[2,88],67:[2,88]},{58:[1,149]},{5:[2,87],6:[2,87],11:[2,87],12:[2,87],13:[2,87],14:[2,87],15:[2,87],16:[1,251],19:[2,87],35:[2,87],36:[2,87],38:[2,87],40:[2,87],41:[2,87],44:[2,87],46:[2,87],48:[2,87],49:[2,87],50:[2,87],52:[2,87],53:328,56:[2,87],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,87],66:[1,102],67:[2,87]},{5:[2,106],6:[2,106],11:[2,106],12:[2,106],13:[2,106],14:[2,106],15:[2,106],19:[2,106],35:[2,106],36:[2,106],38:[2,106],40:[2,106],41:[2,106],44:[2,106],46:[2,106],48:[2,106],49:[2,106],50:[2,106],52:[2,106],54:[2,106],56:[2,106],58:[2,106],65:[2,106],67:[2,106],78:[2,106],79:[2,106],80:[2,106],81:[2,106],82:[2,106],83:[2,106],84:[2,106],85:[2,106],86:[2,106]},{1:[2,1]},{5:[2,9],6:[2,9],11:[2,9],12:[2,9],13:[1,108],14:[1,109],15:[1,110],19:[2,9]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],17:[1,122],19:[2,23],25:[1,301],26:[1,129],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146]},{4:329,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{16:[1,176],18:[1,163],23:164,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,10],6:[2,10],11:[2,10],12:[2,10],13:[1,108],14:[1,109],15:[1,110],19:[2,10]},{5:[2,11],6:[2,11],11:[2,11],12:[2,11],13:[2,11],14:[2,11],15:[1,110],19:[2,11]},{5:[2,12],6:[2,12],11:[2,12],12:[2,12],13:[2,12],14:[2,12],15:[1,110],19:[2,12]},{5:[2,13],6:[2,13],11:[2,13],12:[2,13],13:[2,13],14:[2,13],15:[2,13],19:[2,13]},{1:[2,3]},{1:[2,5]},{5:[2,236],6:[2,236],11:[2,236],12:[2,236],13:[2,236],14:[2,236],15:[1,116],19:[2,236]},{5:[2,237],6:[2,237],11:[2,237],12:[2,237],13:[2,237],14:[2,237],15:[2,237],19:[2,237]},{4:330,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,23],6:[2,23],8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],18:[1,158],19:[2,23],25:[1,301],26:[1,129],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{16:[1,176],23:164,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,239],6:[2,239],11:[2,239],12:[2,239],13:[2,239],14:[2,239],15:[1,116],19:[2,239]},{5:[2,240],6:[2,240],11:[2,240],12:[2,240],13:[2,240],14:[2,240],15:[2,240],19:[2,240]},{4:331,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,242],6:[2,242],11:[2,242],12:[2,242],13:[2,242],14:[2,242],15:[2,242],19:[2,242]},{4:332,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,247],6:[2,247],11:[2,247],12:[2,247],13:[1,114],14:[1,115],15:[1,116],19:[2,247]},{5:[2,248],6:[2,248],11:[2,248],12:[2,248],13:[1,114],14:[1,115],15:[1,116],19:[2,248]},{1:[2,8]},{6:[2,225],19:[2,225]},{5:[2,67],6:[2,67],11:[2,67],12:[2,67],13:[2,67],14:[2,67],15:[2,67],19:[2,67],35:[2,67],36:[2,67],46:[2,67],49:[2,67],52:[2,67],56:[2,67],65:[2,67],67:[2,67]},{5:[2,47],6:[2,47],11:[2,47],12:[2,47],13:[2,47],14:[2,47],15:[2,47],19:[2,47],35:[2,47],36:[2,47]},{5:[2,78],6:[2,78],11:[2,78],12:[2,78],13:[2,78],14:[2,78],15:[2,78],19:[2,78],35:[2,78],36:[2,78],46:[2,78],48:[2,78],49:[2,78],52:[2,78],56:[2,78],65:[2,78],67:[2,78]},{5:[2,56],6:[2,56],11:[2,56],12:[2,56],13:[2,56],14:[2,56],15:[2,56],19:[2,56],35:[2,56],36:[2,56],38:[2,56],40:[2,56]},{5:[2,89],6:[2,89],11:[2,89],12:[2,89],13:[2,89],14:[2,89],15:[2,89],19:[2,89],35:[2,89],36:[2,89],38:[2,89],40:[2,89],41:[2,89],44:[2,89],46:[2,89],48:[2,89],49:[2,89],50:[2,89],52:[2,89],56:[2,89],65:[2,89],67:[2,89]},{5:[2,100],6:[2,100],11:[2,100],12:[2,100],13:[2,100],14:[2,100],15:[2,100],19:[2,100],35:[2,100],36:[2,100],38:[2,100],40:[2,100],41:[2,100],44:[2,100],46:[2,100],48:[2,100],49:[2,100],50:[2,100],52:[2,100],54:[2,100],56:[2,100],65:[2,100],67:[2,100]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,333]},{8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],17:[1,122],18:[1,158],19:[2,23],25:[1,301],26:[1,129],28:[1,156],29:[1,157],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{4:334,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,15],6:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],120:[1,336],121:[1,337],122:[1,338],123:[1,339]},{5:[2,244],6:[2,244],11:[2,244],12:[2,244],13:[2,244],14:[2,244],15:[2,244],19:[2,244]},{6:[2,250],19:[2,250]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,340]},{6:[2,216],19:[2,216]},{6:[2,218],19:[2,218]},{6:[2,220],19:[2,220]},{6:[2,222],19:[2,222]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26],123:[1,341]},{6:[2,224],19:[2,224]},{5:[2,36],6:[2,36],11:[2,36],12:[2,36],13:[2,36],14:[2,36],15:[2,36],19:[2,36],35:[2,36],36:[2,36]},{38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146]},{5:[2,37],6:[2,37],11:[2,37],12:[2,37],13:[2,37],14:[2,37],15:[2,37],19:[2,37],35:[2,37],36:[2,37]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26]},{5:[2,61],6:[2,61],11:[2,61],12:[2,61],13:[2,61],14:[2,61],15:[2,61],19:[2,61],35:[2,61],36:[2,61]},{48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{49:[1,182],52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{16:[1,197],42:195,47:191,49:[2,63],50:[1,196],52:[2,63],53:94,54:[1,198],56:[2,63],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,63],66:[1,102],67:[2,63]},{5:[2,72],6:[2,72],11:[2,72],12:[2,72],13:[2,72],14:[2,72],15:[2,72],19:[2,72],35:[2,72],36:[2,72]},{48:[1,130],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{5:[2,83],6:[2,83],11:[2,83],12:[2,83],13:[2,83],14:[2,83],15:[2,83],19:[2,83],35:[2,83],36:[2,83]},{48:[1,130],50:[1,134],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{56:[1,184],65:[1,185],67:[1,186]},{5:[2,94],6:[2,94],11:[2,94],12:[2,94],13:[2,94],14:[2,94],15:[2,94],19:[2,94],35:[2,94],36:[2,94]},{48:[1,130],50:[1,134],54:[1,147],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{65:[1,185],67:[1,186]},{5:[2,112],6:[2,112],11:[2,112],12:[2,112],13:[2,112],14:[2,112],15:[2,112],19:[2,112],35:[2,112],36:[2,112]},{48:[1,130],50:[1,134],54:[1,147],58:[1,149],66:[1,154],67:[1,153]},{67:[1,186]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,342]},{5:[2,68],6:[2,68],11:[2,68],12:[2,68],13:[2,68],14:[2,68],15:[2,68],19:[2,68],35:[2,68],36:[2,68],46:[2,68],49:[2,68],52:[2,68],56:[2,68],65:[2,68],67:[2,68]},{5:[2,48],6:[2,48],11:[2,48],12:[2,48],13:[2,48],14:[2,48],15:[2,48],19:[2,48],35:[2,48],36:[2,48]},{5:[2,41],6:[2,41],11:[2,41],12:[2,41],13:[2,41],14:[2,41],15:[2,41],19:[2,41],35:[2,41],36:[2,41]},{41:[1,137],54:[1,147],58:[1,149]},{41:[1,208]},{5:[2,79],6:[2,79],11:[2,79],12:[2,79],13:[2,79],14:[2,79],15:[2,79],19:[2,79],35:[2,79],36:[2,79],46:[2,79],48:[2,79],49:[2,79],52:[2,79],56:[2,79],65:[2,79],67:[2,79]},{5:[2,57],6:[2,57],11:[2,57],12:[2,57],13:[2,57],14:[2,57],15:[2,57],19:[2,57],35:[2,57],36:[2,57],38:[2,57],40:[2,57]},{5:[2,51],6:[2,51],11:[2,51],12:[2,51],13:[2,51],14:[2,51],15:[2,51],19:[2,51],35:[2,51],36:[2,51]},{5:[2,101],6:[2,101],11:[2,101],12:[2,101],13:[2,101],14:[2,101],15:[2,101],19:[2,101],35:[2,101],36:[2,101],38:[2,101],40:[2,101],41:[2,101],44:[2,101],46:[2,101],48:[2,101],49:[2,101],50:[2,101],52:[2,101],54:[2,101],56:[2,101],65:[2,101],67:[2,101]},{5:[2,90],6:[2,90],11:[2,90],12:[2,90],13:[2,90],14:[2,90],15:[2,90],19:[2,90],35:[2,90],36:[2,90],38:[2,90],40:[2,90],41:[2,90],44:[2,90],46:[2,90],48:[2,90],49:[2,90],50:[2,90],52:[2,90],56:[2,90],65:[2,90],67:[2,90]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,343]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,344]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,345]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,346]},{87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,347]},{5:[2,235],6:[2,235],11:[2,235],12:[2,235],13:[2,235],14:[2,235],15:[2,235],19:[2,235]},{6:[2,227],19:[2,227]},{6:[2,228],19:[2,228]},{6:[2,229],19:[2,229]},{6:[2,230],19:[2,230]},{5:[2,16],6:[2,16],11:[2,16],12:[2,16],13:[2,16],14:[2,16],15:[2,16],19:[2,16]},{6:[2,226],19:[2,226]},{5:[2,28],6:[2,28],11:[2,28],12:[2,28],13:[2,28],14:[2,28],15:[2,28],19:[2,28],126:[2,28],127:[2,28]},{5:[2,15],6:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15]},{5:[2,238],6:[2,238],11:[2,238],12:[2,238],13:[2,238],14:[2,238],15:[2,238],19:[2,238],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{5:[2,241],6:[2,241],11:[2,241],12:[2,241],13:[2,241],14:[2,241],15:[2,241],19:[2,241],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{5:[2,243],6:[2,243],11:[2,243],12:[2,243],13:[2,243],14:[2,243],15:[2,243],19:[2,243]},{11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]}],
defaultActions: {105:[2,2],112:[2,4],119:[2,6],120:[2,7],254:[2,1],263:[2,3],264:[2,5],277:[2,8]},
parseError: function parseError(str,hash){if(hash.recoverable){this.trace(str)}else{throw new Error(str)}},
parse: function parse(input) {
    var self = this, stack = [0], vstack = [null], lstack = [], table = this.table, yytext = '', yylineno = 0, yyleng = 0, recovering = 0, TERROR = 2, EOF = 1;
    var args = lstack.slice.call(arguments, 1);
    this.lexer.setInput(input);
    this.lexer.yy = this.yy;
    this.yy.lexer = this.lexer;
    this.yy.nParser = this;
    if (typeof this.lexer.yylloc == 'undefined') {
        this.lexer.yylloc = {};
    }
    var yyloc = this.lexer.yylloc;
    lstack.push(yyloc);
    var ranges = this.lexer.options && this.lexer.options.ranges;
    if (typeof this.yy.parseError === 'function') {
        this.parseError = this.yy.parseError;
    } else {
        this.parseError = Object.getPrototypeOf(this).parseError;
    }
    function popStack(n) {
        stack.length = stack.length - 2 * n;
        vstack.length = vstack.length - n;
        lstack.length = lstack.length - n;
    }
    function lex() {
        var token;
        token = self.lexer.lex() || EOF;
        if (typeof token !== 'number') {
            token = self.symbols_[token] || token;
        }
        return token;
    }
    var symbol, preErrorSymbol, state, action, a, r, yyval = {}, p, len, newState, expected;
    while (true) {
        state = stack[stack.length - 1];
        if (this.defaultActions[state]) {
            action = this.defaultActions[state];
        } else {
            if (symbol === null || typeof symbol == 'undefined') {
                symbol = lex();
            }
            action = table[state] && table[state][symbol];
        }
                    if (typeof action === 'undefined' || !action.length || !action[0]) {
                var errStr = '';
                expected = [];
                for (p in table[state]) {
                    if (this.terminals_[p] && p > TERROR) {
                        expected.push('\'' + this.terminals_[p] + '\'');
                    }
                }
                if (this.lexer.showPosition) {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ':\n' + this.lexer.showPosition() + '\nExpecting ' + expected.join(', ') + ', got \'' + (this.terminals_[symbol] || symbol) + '\'';
                } else {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ': Unexpected ' + (symbol == EOF ? 'end of input' : '\'' + (this.terminals_[symbol] || symbol) + '\'');
                }
                this.parseError(errStr, {
                    text: this.lexer.match,
                    token: this.terminals_[symbol] || symbol,
                    line: this.lexer.yylineno,
                    loc: yyloc,
                    expected: expected
                });
            }
        if (action[0] instanceof Array && action.length > 1) {
            throw new Error('Parse Error: multiple actions possible at state: ' + state + ', token: ' + symbol);
        }
        switch (action[0]) {
        case 1:
            stack.push(symbol);
            vstack.push(this.lexer.yytext);
            lstack.push(this.lexer.yylloc);
            stack.push(action[1]);
            symbol = null;
            if (!preErrorSymbol) {
                yyleng = this.lexer.yyleng;
                yytext = this.lexer.yytext;
                yylineno = this.lexer.yylineno;
                yyloc = this.lexer.yylloc;
                if (recovering > 0) {
                    recovering--;
                }
            } else {
                symbol = preErrorSymbol;
                preErrorSymbol = null;
            }
            break;
        case 2:
            len = this.productions_[action[1]][1];
            yyval.$ = vstack[vstack.length - len];
            yyval._$ = {
                first_line: lstack[lstack.length - (len || 1)].first_line,
                last_line: lstack[lstack.length - 1].last_line,
                first_column: lstack[lstack.length - (len || 1)].first_column,
                last_column: lstack[lstack.length - 1].last_column
            };
            if (ranges) {
                yyval._$.range = [
                    lstack[lstack.length - (len || 1)].range[0],
                    lstack[lstack.length - 1].range[1]
                ];
            }
            r = this.performAction.apply(yyval, [
                yytext,
                yyleng,
                yylineno,
                this.yy,
                action[1],
                vstack,
                lstack
            ].concat(args));
            if (typeof r !== 'undefined') {
                return r;
            }
            if (len) {
                stack = stack.slice(0, -1 * len * 2);
                vstack = vstack.slice(0, -1 * len);
                lstack = lstack.slice(0, -1 * len);
            }
            stack.push(this.productions_[action[1]][0]);
            vstack.push(yyval.$);
            lstack.push(yyval._$);
            newState = table[stack[stack.length - 2]][stack[stack.length - 1]];
            stack.push(newState);
            break;
        case 3:
            return true;
        }
    }
    return true;
}};
/* generated by jison-lex 0.2.1 */
var lexer = (function(){
var lexer = {

EOF:1,

parseError:function parseError(str,hash){if(this.yy.nParser){this.yy.nParser.parseError(str,hash)}else{throw new Error(str)}},

// resets the lexer, sets new input
setInput:function (input){this._input=input;this._more=this._backtrack=this.done=false;this.yylineno=this.yyleng=0;this.yytext=this.matched=this.match="";this.conditionStack=["INITIAL"];this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0};if(this.options.ranges){this.yylloc.range=[0,0]}this.offset=0;return this},

// consumes and returns one char from the input
input:function (){var ch=this._input[0];this.yytext+=ch;this.yyleng++;this.offset++;this.match+=ch;this.matched+=ch;var lines=ch.match(/(?:\r\n?|\n).*/g);if(lines){this.yylineno++;this.yylloc.last_line++}else{this.yylloc.last_column++}if(this.options.ranges){this.yylloc.range[1]++}this._input=this._input.slice(1);return ch},

// unshifts one char (or a string) into the input
unput:function (ch){var len=ch.length;var lines=ch.split(/(?:\r\n?|\n)/g);this._input=ch+this._input;this.yytext=this.yytext.substr(0,this.yytext.length-len-1);this.offset-=len;var oldLines=this.match.split(/(?:\r\n?|\n)/g);this.match=this.match.substr(0,this.match.length-1);this.matched=this.matched.substr(0,this.matched.length-1);if(lines.length-1){this.yylineno-=lines.length-1}var r=this.yylloc.range;this.yylloc={first_line:this.yylloc.first_line,last_line:this.yylineno+1,first_column:this.yylloc.first_column,last_column:lines?(lines.length===oldLines.length?this.yylloc.first_column:0)+oldLines[oldLines.length-lines.length].length-lines[0].length:this.yylloc.first_column-len};if(this.options.ranges){this.yylloc.range=[r[0],r[0]+this.yyleng-len]}this.yyleng=this.yytext.length;return this},

// When called from action, caches matched text and appends it on next action
more:function (){this._more=true;return this},

// When called from action, signals the lexer that this rule fails to match the input, so the next matching rule (regex) should be tested instead.
reject:function (){if(this.options.backtrack_lexer){this._backtrack=true}else{return this.parseError("Lexical error on line "+(this.yylineno+1)+". You can only invoke reject() in the lexer when the lexer is of the backtracking persuasion (options.backtrack_lexer = true).\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})}return this},

// retain first n characters of the match
less:function (n){this.unput(this.match.slice(n))},

// displays already matched input, i.e. for error messages
pastInput:function (){var past=this.matched.substr(0,this.matched.length-this.match.length);return(past.length>20?"...":"")+past.substr(-20).replace(/\n/g,"")},

// displays upcoming input, i.e. for error messages
upcomingInput:function (){var next=this.match;if(next.length<20){next+=this._input.substr(0,20-next.length)}return(next.substr(0,20)+(next.length>20?"...":"")).replace(/\n/g,"")},

// displays the character position where the lexing error occurred, i.e. for error messages
showPosition:function (){var pre=this.pastInput();var c=new Array(pre.length+1).join("-");return pre+this.upcomingInput()+"\n"+c+"^"},

// test the lexed token: return FALSE when not a match, otherwise return token
test_match:function (match,indexed_rule){var token,lines,backup;if(this.options.backtrack_lexer){backup={yylineno:this.yylineno,yylloc:{first_line:this.yylloc.first_line,last_line:this.last_line,first_column:this.yylloc.first_column,last_column:this.yylloc.last_column},yytext:this.yytext,match:this.match,matches:this.matches,matched:this.matched,yyleng:this.yyleng,offset:this.offset,_more:this._more,_input:this._input,yy:this.yy,conditionStack:this.conditionStack.slice(0),done:this.done};if(this.options.ranges){backup.yylloc.range=this.yylloc.range.slice(0)}}lines=match[0].match(/(?:\r\n?|\n).*/g);if(lines){this.yylineno+=lines.length}this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:lines?lines[lines.length-1].length-lines[lines.length-1].match(/\r?\n?/)[0].length:this.yylloc.last_column+match[0].length};this.yytext+=match[0];this.match+=match[0];this.matches=match;this.yyleng=this.yytext.length;if(this.options.ranges){this.yylloc.range=[this.offset,this.offset+=this.yyleng]}this._more=false;this._backtrack=false;this._input=this._input.slice(match[0].length);this.matched+=match[0];token=this.performAction.call(this,this.yy,this,indexed_rule,this.conditionStack[this.conditionStack.length-1]);if(this.done&&this._input){this.done=false}if(token){return token}else if(this._backtrack){for(var k in backup){this[k]=backup[k]}return false}return false},

// return next match in input
next:function (){if(this.done){return this.EOF}if(!this._input){this.done=true}var token,match,tempMatch,index;if(!this._more){this.yytext="";this.match=""}var rules=this._currentRules();for(var i=0;i<rules.length;i++){tempMatch=this._input.match(this.rules[rules[i]]);if(tempMatch&&(!match||tempMatch[0].length>match[0].length)){match=tempMatch;index=i;if(this.options.backtrack_lexer){token=this.test_match(tempMatch,rules[i]);if(token!==false){return token}else if(this._backtrack){match=false;continue}else{return false}}else if(!this.options.flex){break}}}if(match){token=this.test_match(match,rules[index]);if(token!==false){return token}return false}if(this._input===""){return this.EOF}else{return this.parseError("Lexical error on line "+(this.yylineno+1)+". Unrecognized text.\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})}},

// return next match that has a token
lex:function lex(){var r=this.next();if(r){return r}else{return this.lex()}},

// activates a new lexer condition state (pushes the new lexer condition state onto the condition stack)
begin:function begin(condition){this.conditionStack.push(condition)},

// pop the previously active lexer condition state off the condition stack
popState:function popState(){var n=this.conditionStack.length-1;if(n>0){return this.conditionStack.pop()}else{return this.conditionStack[0]}},

// produce the lexer rule set which is active for the currently active lexer condition state
_currentRules:function _currentRules(){if(this.conditionStack.length&&this.conditionStack[this.conditionStack.length-1]){return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules}else{return this.conditions["INITIAL"].rules}},

// return the currently active lexer condition state; when an index argument is provided it produces the N-th previous condition state, if available
topState:function topState(n){n=this.conditionStack.length-1-Math.abs(n||0);if(n>=0){return this.conditionStack[n]}else{return"INITIAL"}},

// alias for begin(condition)
pushState:function pushState(condition){this.begin(condition)},

// return the number of states currently on the stack
stateStackSize:function stateStackSize(){return this.conditionStack.length},
options: {"flex":true,"case-insensitive":true},
performAction: function anonymous(yy,yy_,$avoiding_name_collisions,YY_START
/**/) {

var YYSTATE=YY_START;
switch($avoiding_name_collisions) {
case 0:/* skip whitespace */
break;
case 1:return 28
break;
case 2:return 29
break;
case 3:return 20
break;
case 4:return 16
break;
case 5:return 63
break;
case 6:return 77
break;
case 7:return 77
break;
case 8:return 66
break;
case 9:return 61
break;
case 10:return 62 
break;
case 11:return 67
break;
case 12:return 65                                                                                                                                     
break;
case 13:return 60
break;
case 14:return 56
break;
case 15:return 58
break;
case 16:return 54
break;
case 17:return 50
break;
case 18:return 48
break;
case 19:return 41
break;
case 20:return 44
break;
case 21:return 38
break;
case 22:return 40
break;
case 23:return 82
break;
case 24:return 83
break;
case 25:return 80
break;
case 26:return 81
break;
case 27:return 78
break;
case 28:return 79
break;
case 29:return 84
break;
case 30:return 85
break;
case 31:return 86
break;
case 32:return 88
break;
case 33:return 91
break;
case 34:return 92
break;
case 35:return 93
break;
case 36:return 94
break;
case 37:return 95
break;
case 38:return 96
break;
case 39:return 97
break;
case 40:return 98
break;
case 41:return 99
break;
case 42:return 100
break;
case 43:return 101
break;
case 44:return 102
break;
case 45:return 89
break;
case 46:return 104
break;
case 47:return 103
break;
case 48:return 105
break;
case 49:return 106
break;
case 50:return 107
break;
case 51:return 108
break;
case 52:return 90
break;
case 53:return 111
break;
case 54:return 110
break;
case 55:return 109
break;
case 56:return 112
break;
case 57:return 113
break;
case 58:return 114
break;
case 59:return 115
break;
case 60:return 116
break;
case 61:return 117
break;
case 62:return 122
break;
case 63:return 123
break;
case 64:return 118
break;
case 65:return 120
break;
case 66:return 121
break;
case 67:return 127
break;
case 68:return 126
break;
case 69:return 124
break;
case 70:return 125
break;
case 71:return 35
break;
case 72:return 14
break;
case 73:return 13
break;
case 74:return 12
break;
case 75:return 11
break;
case 76:return 15
break;
case 77:return 17
break;
case 78:return 18
break;
case 79:return 19
break;
case 80:return 12
break;
case 81:return 36
break;
case 82:return 5
break;
case 83:return 25
break;
case 84:return 24
break;
case 85:return 27
break;
case 86:return 26
break;
case 87:return 6
break;
case 88:return 'INVALID'
break;
case 89:console.log(yy_.yytext);
break;
}
},
rules: [/^(?:\s+)/i,/^(?:[1-9][0-9]{0,1}(,[0-9]{2})*(,[0-9]{3}))/i,/^(?:[1-9][0-9]{0,2}(,[0-9]{3})*(,[0-9]{3}))/i,/^(?:[1-9][0-9]*(\s)[0-9]+(\s)?(\/|\\)(\s)?[0-9]+)/i,/^(?:([0-9]+(\.[0-9]+)?)|(\.[0-9]+))/i,/^(?:(twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(one|ones|two|three|four|five|six|seven|eight|nine))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|ten|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths|half|third|fourth|fifth|sixth|seventh|eighth|ninth|elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths|eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth|tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths|tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth|quarters|quarter|hundredths|hundredth|thousandths|thousandth))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|ten|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths|half|third|fourth|fifth|sixth|seventh|eighth|ninth|elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths|eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth|tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths|tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth|quarters|quarter|hundredths|hundredth|thousandths|thousandth))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine))/i,/^(?:(eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen))/i,/^(?:(twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety))/i,/^(?:(ones))/i,/^(?:(tens))/i,/^(?:(ten))/i,/^(?:(hundreds))/i,/^(?:(hundred))/i,/^(?:(thousands|thousand))/i,/^(?:(lakhs|lakh))/i,/^(?:(crores|crore))/i,/^(?:(millions))/i,/^(?:(million))/i,/^(?:(billions))/i,/^(?:(billion))/i,/^(?:(first|half|third|fourth|fifth|sixth|seventh|eighth|ninth))/i,/^(?:(firsts|halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths))/i,/^(?:(eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth))/i,/^(?:(elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths))/i,/^(?:(tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth))/i,/^(?:(tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths))/i,/^(?:(quarters|quarter))/i,/^(?:(hundredths|hundredth))/i,/^(?:(thousandths|thousandth))/i,/^(?:(metres|metre|m))/i,/^(?:(kilometres|kilometre|km))/i,/^(?:(millimetres|millimetre|mm))/i,/^(?:(centimetres|centimetre|cm))/i,/^(?:(litres|litre|l))/i,/^(?:(kilolitres|kilolitre|kl))/i,/^(?:(millilitres|millimetre|ml))/i,/^(?:(centilitres|centilitre|cl))/i,/^(?:(miles|mile|mi))/i,/^(?:(yards|yard|yd))/i,/^(?:(feet|foot|ft))/i,/^(?:(inches|inch|in))/i,/^(?:(grams|gram|g))/i,/^(?:(kilograms|kilogram|kg))/i,/^(?:(milligrams|milligram|mg))/i,/^(?:(centigrams|centigram|cg))/i,/^(?:(micrograms|microgram|mcg|μg))/i,/^(?:(pounds|pound|lb))/i,/^(?:(ounces|ounce))/i,/^(?:(tons|ton))/i,/^(?:(seconds|second|s))/i,/^(?:(milliseconds|millisecond|ms))/i,/^(?:(microseconds|microsecond|μs))/i,/^(?:(nanosecondsnanosecond|ns))/i,/^(?:(minutes|minute|min))/i,/^(?:(hours|hour|hr))/i,/^(?:(days|day))/i,/^(?:(weeks|week))/i,/^(?:(months|month))/i,/^(?:(years|year))/i,/^(?:(degrees|degree|°))/i,/^(?:(radians|radian|rad))/i,/^(?:(kelvin|degree kelvin|K))/i,/^(?:(degree celcius|celcius|centrigrade|°C))/i,/^(?:(fahrenheit|°F))/i,/^(?:(rupees|rs\.|rs|inr\.|inr))/i,/^(?:(rupee|re\.|re))/i,/^(?:(square|sq\.|sq))/i,/^(?:(cubic|cu\.|cu))/i,/^(?:AND|&)/i,/^(?:\/|\\|UPON|BY|OVER|OUT OF|PER)/i,/^(?:\*|x|×|TIMES|OF)/i,/^(?:-|MINUS)/i,/^(?:\+|PLUS)/i,/^(?:\^)/i,/^(?:!)/i,/^(?:\(|\[|\{)/i,/^(?:\)|\]|\})/i,/^(?:-)/i,/^(?:,)/i,/^(?:\.)/i,/^(?:PI)/i,/^(?:E)/i,/^(?:SQRT)/i,/^(?:%|PERCENT)/i,/^(?:$)/i,/^(?:.)/i,/^(?:.)/i],
conditions: {"INITIAL":{"rules":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89],"inclusive":true}}
};
return lexer;
})();
nParser.lexer = lexer;
function Parser () {
  this.yy = {};
}
Parser.prototype = nParser;nParser.Parser = Parser;
return new Parser;
})();


if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.nParser = nParser;
exports.Parser = nParser.Parser;
exports.parse = function () { return nParser.parse.apply(nParser, arguments); };
exports.main = function commonjsMain(args){if(!args[1]){console.log("Usage: "+args[0]+" FILE");process.exit(1)}var source=require("fs").readFileSync(require("path").normalize(args[1]),"utf8");return exports.nParser.parse(source)};
if (typeof module !== 'undefined' && require.main === module) {
  exports.main(process.argv.slice(1));
}
}var singleDigit = Array("zero","one","two","three","four","five","six","seven","eight","nine");
var doubleDigit = Array("eleven","twelve","thirteen","fourteen","fifteen","sixteen","seventeen","eighteen","nineteen");
var tenDigit = Array("ten", "twenty","thirty","forty","fifty","sixty","seventy","eighty", "ninety");
var fracSingularSingleDigit = Array("first","half","third","fourth","fifth","sixth","seventh","eighth","ninth");
var fracSingularDoubleDigit = Array("eleventh","twelfth","thirteenth","fourteenth","fifteenth","sixteenth","seventeenth","eighteenth","nineteenth");
var fracSingularTenDigit = Array("tenth","twentieth","thirtieth","fortieth","fiftieth","sixtieth","seventieth","eightieth", "ninetieth");
var fracPluralSingleDigit = Array("firsts","halves","thirds","fourths","fifths","sixths","sevenths","eighths","ninths");
var fracPluralDoubleDigit = Array("elevenths","twelfths","thirteenths","fourteenths","fifteenths","sixteenths","seventeenths","eighteenths","nineteenths");
var fracPluralTenDigit = Array("tenths","twentieths","thirtieths","fortieths","fiftieths","sixtieths","seventieths","eightieths", "ninetieths");

/* nParser generated by jison 0.4.13 */
/*
  Returns a Parser object of the following structure:

  Parser: {
    yy: {}
  }

  Parser.prototype: {
    yy: {},
    trace: function(),
    symbols_: {associative list: name ==> number},
    terminals_: {associative list: number ==> name},
    productions_: [...],
    performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate, $$, _$),
    table: [...],
    defaultActions: {...},
    parseError: function(str, hash),
    parse: function(input),

    lexer: {
        EOF: 1,
        parseError: function(str, hash),
        setInput: function(input),
        input: function(),
        unput: function(str),
        more: function(),
        less: function(n),
        pastInput: function(),
        upcomingInput: function(),
        showPosition: function(),
        test_match: function(regex_match_array, rule_index),
        next: function(),
        lex: function(),
        begin: function(condition),
        popState: function(),
        _currentRules: function(),
        topState: function(),
        pushState: function(condition),

        options: {
            ranges: boolean           (optional: true ==> token location info will include a .range[] member)
            flex: boolean             (optional: true ==> flex-like lexing behaviour where the rules are tested exhaustively to find the longest match)
            backtrack_lexer: boolean  (optional: true ==> lexer regexes are tested in order and for each matching regex the action code is invoked; the lexer terminates the scan when a token is returned by the action code)
        },

        performAction: function(yy, yy_, $avoiding_name_collisions, YY_START),
        rules: [...],
        conditions: {associative list: name ==> set},
    }
  }


  token location info (@$, _$, etc.): {
    first_line: n,
    last_line: n,
    first_column: n,
    last_column: n,
    range: [start_number, end_number]       (where the numbers are indexes into the input string, regular zero-based)
  }


  the parseError function receives a 'hash' object with these members for lexer and nParser errors: {
    text:        (matched text)
    token:       (the produced terminal token, if any)
    line:        (yylineno)
  }
  while nParser (grammar) errors will also provide these members, i.e. nParser errors deliver a superset of attributes: {
    loc:         (yylloc)
    expected:    (string describing the set of expected tokens)
    recoverable: (boolean: TRUE when the nParser has a error recovery rule available for this particular error)
  }
*/
var nParser = (function(){
var nParser = {trace: function trace(){},
yy: {},
symbols_: {"error":2,"expressions":3,"e":4,".":5,"EOF":6,"units_final":7,"c_units":8,"other_units_final":9,"money":10,"+":11,"-":12,"*":13,"/":14,"^":15,"NUMBER":16,"!":17,"(":18,")":19,"MFRAC":20,"w":21,"integer":22,"magnitude":23,"E":24,"PI":25,"PERCENT":26,"SQRT":27,"INUMBER":28,"FNUMBER":29,"crore":30,"crores":31,"billion":32,"billions":33,"fracWords":34,"AND":35,",":36,"millions":37,"BILLIONS":38,"million":39,"BILLION":40,"MILLIONS":41,"thousand":42,"thousands":43,"MILLION":44,"lakhs":45,"CRORES":46,"lakh":47,"CRORE":48,"LAKHS":49,"LAKH":50,"hundreds":51,"THOUSANDS":52,"hundred":53,"THOUSAND":54,"tens":55,"HUNDREDS":56,"ten":57,"HUNDRED":58,"one":59,"TEN":60,"DOUBLE_DIGIT":61,"TEN_DIGIT":62,"HYPHEN_DIGIT":63,"ones":64,"TENS":65,"SINGLE_DIGIT":66,"ONES":67,"oneths":68,"oneth":69,"tenth":70,"tenthsGroup":71,"tenDigith":72,"tenDigiths":73,"quarter":74,"hundredths":75,"thousandths":76,"FRAC_HYPHEN_DIGIT":77,"FRAC_SINGULAR_TEN_DIGIT":78,"FRAC_PLURAL_TEN_DIGIT":79,"FRAC_SINGULAR_DOUBLE_DIGIT":80,"FRAC_PLURAL_DOUBLE_DIGIT":81,"FRAC_SINGULAR_SINGLE_DIGIT":82,"FRAC_PLURAL_SINGLE_DIGIT":83,"QUARTER":84,"HUNDREDTH":85,"THOUSANDTH":86,"units":87,"METRE":88,"KILOGRAM":89,"SECOND":90,"KILOMETRE":91,"MILLIMETRE":92,"CENTIMETRE":93,"LITRE":94,"KILOLITRE":95,"MILLILITRE":96,"CENTILITRE":97,"MILE":98,"YARD":99,"FOOT":100,"INCH":101,"GRAM":102,"CENTIGRAM":103,"MILLIGRAM":104,"MICROGRAM":105,"POUND":106,"OUNCE":107,"TON":108,"NANOSECOND":109,"MICROSECOND":110,"MILLISECOND":111,"MINUTE":112,"HOUR":113,"DAY":114,"WEEK":115,"MONTH":116,"YEAR":117,"KELVIN":118,"other_units":119,"CELCIUS":120,"FAHRENHEIT":121,"DEGREE":122,"RADIAN":123,"SQUARE":124,"CUBIC":125,"RUPEE":126,"RUPEES":127,"$accept":0,"$end":1},
terminals_: {2:"error",5:".",6:"EOF",7:"units_final",11:"+",12:"-",13:"*",14:"/",15:"^",16:"NUMBER",17:"!",18:"(",19:")",20:"MFRAC",24:"E",25:"PI",26:"PERCENT",27:"SQRT",28:"INUMBER",29:"FNUMBER",35:"AND",36:",",38:"BILLIONS",40:"BILLION",41:"MILLIONS",44:"MILLION",46:"CRORES",48:"CRORE",49:"LAKHS",50:"LAKH",52:"THOUSANDS",54:"THOUSAND",56:"HUNDREDS",58:"HUNDRED",60:"TEN",61:"DOUBLE_DIGIT",62:"TEN_DIGIT",63:"HYPHEN_DIGIT",65:"TENS",66:"SINGLE_DIGIT",67:"ONES",77:"FRAC_HYPHEN_DIGIT",78:"FRAC_SINGULAR_TEN_DIGIT",79:"FRAC_PLURAL_TEN_DIGIT",80:"FRAC_SINGULAR_DOUBLE_DIGIT",81:"FRAC_PLURAL_DOUBLE_DIGIT",82:"FRAC_SINGULAR_SINGLE_DIGIT",83:"FRAC_PLURAL_SINGLE_DIGIT",84:"QUARTER",85:"HUNDREDTH",86:"THOUSANDTH",88:"METRE",89:"KILOGRAM",90:"SECOND",91:"KILOMETRE",92:"MILLIMETRE",93:"CENTIMETRE",94:"LITRE",95:"KILOLITRE",96:"MILLILITRE",97:"CENTILITRE",98:"MILE",99:"YARD",100:"FOOT",101:"INCH",102:"GRAM",103:"CENTIGRAM",104:"MILLIGRAM",105:"MICROGRAM",106:"POUND",107:"OUNCE",108:"TON",109:"NANOSECOND",110:"MICROSECOND",111:"MILLISECOND",112:"MINUTE",113:"HOUR",114:"DAY",115:"WEEK",116:"MONTH",117:"YEAR",118:"KELVIN",120:"CELCIUS",121:"FAHRENHEIT",122:"DEGREE",123:"RADIAN",124:"SQUARE",125:"CUBIC",126:"RUPEE",127:"RUPEES"},
productions_: [0,[3,3],[3,2],[3,3],[3,2],[3,3],[3,2],[3,2],[3,3],[4,3],[4,3],[4,3],[4,3],[4,3],[4,2],[4,3],[4,4],[4,1],[4,1],[4,1],[22,2],[22,2],[22,1],[23,1],[23,1],[23,1],[23,2],[23,2],[23,4],[23,1],[23,1],[21,1],[21,1],[21,1],[21,1],[21,1],[21,3],[21,3],[33,1],[33,2],[33,2],[33,3],[32,1],[32,1],[32,2],[32,2],[32,2],[32,3],[32,3],[37,2],[37,2],[37,3],[39,1],[39,2],[39,2],[39,2],[39,3],[39,3],[31,1],[31,2],[31,2],[31,3],[30,1],[30,1],[30,2],[30,2],[30,2],[30,3],[30,3],[45,1],[45,2],[45,2],[45,3],[47,1],[47,1],[47,2],[47,2],[47,2],[47,3],[47,3],[43,1],[43,2],[43,2],[43,3],[42,1],[42,1],[42,2],[42,2],[42,2],[42,3],[42,3],[51,1],[51,2],[51,2],[51,3],[53,1],[53,1],[53,2],[53,2],[53,2],[53,3],[53,3],[57,1],[57,1],[57,1],[57,1],[57,2],[57,1],[55,1],[55,2],[55,2],[55,2],[55,3],[59,1],[64,2],[64,2],[64,2],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[72,1],[72,2],[72,2],[72,2],[72,2],[72,2],[73,1],[73,2],[73,2],[73,2],[73,2],[73,2],[70,1],[70,2],[70,2],[70,2],[70,2],[70,2],[71,1],[71,2],[71,2],[71,2],[71,2],[71,2],[69,1],[69,2],[69,2],[69,2],[69,2],[69,2],[68,1],[68,2],[68,2],[68,2],[68,2],[68,2],[74,1],[74,2],[74,2],[74,2],[74,2],[74,2],[74,2],[75,1],[75,2],[75,2],[75,2],[75,2],[75,2],[75,2],[76,1],[76,2],[76,2],[76,2],[76,2],[76,2],[76,2],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,3],[119,4],[119,4],[119,4],[119,4],[119,4],[8,1],[8,2],[8,2],[8,2],[8,4],[8,3],[8,3],[8,5],[8,3],[8,3],[8,5],[8,3],[8,5],[8,3],[8,2],[8,2],[8,3],[8,3],[9,1],[9,3],[10,2],[10,2],[10,2],[10,2]],
performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate /* action[1] */, $$ /* vstack */, _$ /* lstack */
/**/) {
/* this == yyval */

var $0 = $$.length - 1;
switch (yystate) {
case 1:return ($$[$0-2].toString());
break;
case 2:return ($$[$0-1].toString());
break;
case 3: 
    var input_arr = $$[$0-2];
    var return_value = $$[$0-2][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 4: 
    var input_arr = $$[$0-1];
    var return_value = $$[$0-1][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 5: 
    var input_arr = $$[$0-2];
    var return_value = $$[$0-2][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 6: 
    var input_arr = $$[$0-1];
    var return_value = $$[$0-1][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 7:return $$[$0-1];
break;
case 8:return $$[$0-2];
break;
case 9:this.$ = $$[$0-2]+$$[$0];
break;
case 10:this.$ = $$[$0-2]-$$[$0];
break;
case 11:this.$ = $$[$0-2]*$$[$0];
break;
case 12:this.$ = $$[$0-2]/$$[$0];
break;
case 13:this.$ = Math.pow($$[$0-2], $$[$0]);
break;
case 14:
           if($$[$0-1] % 1 != 0 || $$[$0-1] < 0) throw new Error("error");    
        this.$ = 1;   
        for(var i = $$[$0-1]; i>1; i--)   
        {   
          this.$ = this.$*i;    
        }   
    
        
    
break;
case 15:this.$ = $$[$0-1];
break;
case 16:this.$ = (-1)*Number($$[$0-1]);
break;
case 17:
     var temp = yyleng; 
     var val = 0;
     var val1 = 0;
     var val2 = 0;
     var pos = 0;
     for(; pos < temp; pos++)
      {
      if(yytext[pos] == " ")
        break;
      val = 10*val + Number(yytext[pos]);
      }
     pos++;
     for(; pos <temp; pos++)
     {
       if(yytext[pos] == "/" || yytext[pos] == "\\")
        break;
      val1 = 10*val1 + Number(yytext[pos]);
     }
     pos++;
     for(; pos <temp; pos++)
     {
      val2 = 10*val2 + Number(yytext[pos]);
     }
     this.$ = val + (val1/val2);
    
break;
case 18:this.$ = $$[$0];
break;
case 19:this.$ = $$[$0];
break;
case 20:this.$ = Number($$[$0]);
break;
case 21:this.$ = (-1)*Number($$[$0]);
break;
case 22:this.$ = $$[$0];
break;
case 23:this.$ = Number(yytext);
break;
case 24:this.$ = Math.E;
break;
case 25:this.$ = Math.PI;
break;
case 26:this.$ = Number($$[$0-1])*Math.PI;
break;
case 27:this.$ = Number($$[$0-1])*0.01;
break;
case 28:this.$ = Math.sqrt($$[$0-1]);
break;
case 29:
        var temp = yyleng;
        var val = 0;
        for(var i = 0; i < temp; i++)
        {
        if(yytext[i] == ",")
          continue;
        val = 10*val + Number(yytext[i]);
        }  
        this.$ = val;
       
break;
case 30:
        var temp = yyleng;
        var val = 0;
        for(var i = 0; i < temp; i++)
        {
        if(yytext[i] == ",")
          continue;
        val = 10*val + Number(yytext[i]);
        }  
        this.$ = val;
       
break;
case 31:this.$ = $$[$0];
break;
case 35:this.$ = $$[$0];
break;
case 36:if($$[$0-2] < $$[$0]) throw new Error("Invalid number in words.");
     this.$ = $$[$0-2] + $$[$0];
break;
case 37:if($$[$0-2] < $$[$0]) throw new Error("Invalid number in words.");
     this.$ = $$[$0-2] + $$[$0];
break;
case 39:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000;
       
break;
case 40:
         this.$ = $$[$0-1]*1000000000;
      
break;
case 41:
         this.$ = $$[$0-2]*1000000000 + $$[$0];
      
break;
case 43:this.$ = 100000000;
break;
case 44:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000;
      
break;
case 45:this.$ = $$[$0-1]*1000000000;
break;
case 46:this.$ = 1000000000 + $$[$0];
break;
case 47:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000 + $$[$0];
      
break;
case 48:this.$ = $$[$0-2]*1000000000 + $$[$0];
break;
case 49:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000;
       
break;
case 50:
         this.$ = $$[$0-1]*1000000;
      
break;
case 51:
         this.$ = $$[$0-2]*1000000 + $$[$0];
      
break;
case 52:this.$ = 100000;
break;
case 53:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000;
      
break;
case 54:this.$ = $$[$0-1]*1000000;
break;
case 55:this.$ = 1000000 + $$[$0];
break;
case 56:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000 + $$[$0];
      
break;
case 57:this.$ = $$[$0-2]*1000000 + $$[$0];
break;
case 58:this.$ = $$[$0];
break;
case 59:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000;
       
break;
case 60:
         this.$ = $$[$0-1]*10000000;
      
break;
case 61:
         this.$ = $$[$0-2]*10000000 + $$[$0];
      
break;
case 62:this.$ = $$[$0];
break;
case 63:this.$ = 10000000;
break;
case 64:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000;
      
break;
case 65:this.$ = $$[$0-1]*100000;
break;
case 66:this.$ = 10000000 + $$[$0];
break;
case 67:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000 + $$[$0];
      
break;
case 68:this.$ = $$[$0-2]*10000000 + $$[$0];
break;
case 69:this.$ = $$[$0];
break;
case 70:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000;
       
break;
case 71:
         this.$ = $$[$0-1]*100000;
      
break;
case 72:
         this.$ = $$[$0-2]*100000 + $$[$0];
      
break;
case 73:this.$ = $$[$0];
break;
case 74:this.$ = 100000;
break;
case 75:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000;
      
break;
case 76:this.$ = $$[$0-1]*100000;
break;
case 77:this.$ = 100000 + $$[$0];
break;
case 78:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000 + $$[$0];
      
break;
case 79:this.$ = $$[$0-2]*100000 + $$[$0];
break;
case 80:this.$ = $$[$0];
break;
case 81:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000;
       
break;
case 82:
         this.$ = $$[$0-1]*1000;
      
break;
case 83:
         this.$ = $$[$0-2]*1000 + $$[$0];
      
break;
case 84:this.$ = $$[$0];
break;
case 85:this.$ = 1000;
break;
case 86:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000;
       
break;
case 87:this.$ = $$[$0-1]*1000;
break;
case 88:this.$ = 1000 + $$[$0];
break;
case 89:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000 + $$[$0];
       
break;
case 90:this.$ = $$[$0-2]*1000+$$[$0];
break;
case 91:this.$ = $$[$0];
break;
case 92:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100;
       
break;
case 93:
         this.$ = $$[$0-1]*100;
      
break;
case 94:
         this.$ = $$[$0-2]*100 + $$[$0];
      
break;
case 95:this.$ = $$[$0];
break;
case 96:this.$ = 100;
break;
case 97:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100;
       
break;
case 98:this.$ = $$[$0-1]*100;
break;
case 99:this.$ = 100+$$[$0];
break;
case 100:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100 + $$[$0];
       
break;
case 101:this.$ = $$[$0-2]*100+$$[$0];
break;
case 102:this.$ = $$[$0];
break;
case 103:this.$ = 10;
break;
case 104:
     var str = $$[$0];
     str = str.toLowerCase();
     this.$ = doubleDigit.indexOf(str)+11;
     
break;
case 105:var str = $$[$0];    
   str = str.toLowerCase();   
  this.$ = (tenDigit.indexOf(str)+1)*10;
     
break;
case 106:
    var str = $$[$0-1];   
  str = str.toLowerCase();    
  this.$ = (tenDigit.indexOf(str)+1)*10 + $$[$0];
     
break;
case 107:
    var str = $$[$0];
    str = str.toLowerCase();    
    var n = str.split("-");   
    this.$ = (tenDigit.indexOf(n[0])+1)*10 + singleDigit.indexOf(n[1]);
     
break;
case 108:this.$ = $$[$0];
break;
case 109:
      var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num*10;
   
break;
case 110:
      var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num*10;
   
break;
case 111:
     this.$ = $$[$0-1]*10;
    
break;
case 112:
     this.$ = $$[$0-2]*10 + $$[$0];
    
break;
case 113: var str = $$[$0];   
   str = str.toLowerCase();
     this.$ = singleDigit.indexOf(str);
break;
case 114:
     var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num;
    
break;
case 115: if($$[$0] != 1) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]);
break;
case 116: this.$ = $$[$0-1];       
break;
case 126:var str = $$[$0];
       str = str.toLowerCase();
       var n = str.split("-");
       var index = -1;
       var numerator = 1;
       var denominator = 1;
       var loopCount=0;
       while(index == -1)
       {
       loopCount++;
        index = singleDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  index;
         break;
         }
        index = doubleDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  index+11;
         break;
         }
         index = tenDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  (index+1)*10;
         break;
         }
         if(loopCount==20)
        break;
       }
       if(n[1] == "quarters"|"quarter")
       denominator = 4;
       else if(n[1] == "hundredths"|"hundredth")
       denominator = 100;
       else if(n[1] == "thousandths"|"thousandth")
       denominator = 1000;
       index = -1;
       loopCount=0;
       while(index == -1 && denominator == 1)
      {
        loopCount++;
        index = fracSingularSingleDigit.indexOf(n[1]);
        if(index != -1)
        {
          denominator = (index+1);
          break;
        }
        index = fracSingularDoubleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  index+11;
         break;
         }
         index = fracSingularTenDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  (index+1)*10;
         break;
         }
        index = fracPluralSingleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator = (index+1);
         break;
         }
        index = fracPluralDoubleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  index+11;
         break;
         }
         index = fracPluralTenDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  (index+1)*10;
         break;
         }
         if(loopCount==20)
        break;
       } 
       //if(numerator >= denominator) throw new Error("Invalid entry in words.");       
       this.$ = Number(numerator/denominator);
      
break;
case 127:
      var str = $$[$0];   
    str = str.toLowerCase();
      this.$ = Number(0.1/(fracSingularTenDigit.indexOf(str)+1));
       
break;
case 128: var str = $$[$0];   
     str = str.toLowerCase();
       var index = fracSingularTenDigit.indexOf(str);
       if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
       this.$ = Number($$[$0-1]*0.1/(index+1));
       
break;
case 129: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/((index+1)*10));
     
break;
case 130: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100/((index+1)*10));
     
break;
case 131: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(1000/((index+1)*10));
     
break;
case 132: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100000/((index+1)*10));
     
break;
case 133:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(0.1/(fracPluralTenDigit.indexOf(str)+1)); 
break;
case 134: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.1/(index+1));
     
break;
case 135: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/((index+1)*10));
     
break;
case 136: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(100/((index+1)*10));
     
break;
case 137: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 138: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(100000/((index+1)*10));
     
break;
case 139:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracSingularDoubleDigit.indexOf(str)+11)); 
break;
case 140: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 141: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 142: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 143: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 144: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 145:var str = $$[$0];    
    str = str.toLowerCase();
     this.$ = Number(1/(fracPluralDoubleDigit.indexOf(str)+11)); 
break;
case 146: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 147: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 148: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 149: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 150: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 151:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracSingularSingleDigit.indexOf(str)+1)); 
break;
case 152: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 153: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 154: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 155: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 156: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 157:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracPluralSingleDigit.indexOf(str)+1)); 
break;
case 158: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 159: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 160: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 161: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 162: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 163: this.$ = Number(1/4); 
break;
case 164: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/4);
     
break;
case 165:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/4);
     
break;
case 166: 
     this.$ = Number(25);
     
break;
case 167: 
      this.$ = Number(250);
     
break;
case 168:  this.$ = Number(25000);
break;
case 169:  this.$ = Number(2500000);
break;
case 170: this.$ = Number(0.01); 
break;
case 171: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.01);
     
break;
case 172:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.01);
     
break;
case 173: 
     this.$ = Number(1);
     
break;
case 174: 
      this.$ = Number(10);
     
break;
case 175:  this.$ = Number(1000);
break;
case 176:  this.$ = Number(100000);
break;
case 177: this.$ = Number(0.001); 
break;
case 178: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.001);
     
break;
case 179:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.001);
     
break;
case 180: 
     this.$ = Number(0.1);
     
break;
case 181: 
      this.$ = Number(1);
     
break;
case 182:  this.$ = Number(100);
break;
case 183:  this.$ = Number(10000);
break;
case 184:this.$ = [1, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 185:this.$ = [1, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 186:this.$ = [1, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 187:this.$ = [1e3, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 188:this.$ = [1e-3, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 189:this.$ = [1e-2, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 190:this.$ = [1e-3, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 191:this.$ = [1, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 192:this.$ = [1e-6, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 193:this.$ = [1e-5, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 194:this.$ = [1609.34, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 195:this.$ = [0.9144, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 196:this.$ = [0.3048, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 197:this.$ = [0.0254, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 198:this.$ = [1e-3, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 199:this.$ = [1e-5, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 200:this.$ = [1e-6, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 201:this.$ = [1e-9, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 202:this.$ = [0.45, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 203:this.$ = [0.028, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 204:this.$ = [1e3, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 205:this.$ = [1e-9, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 206:this.$ = [1e-6, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 207:this.$ = [1e-3, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 208:this.$ = [60, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 209:this.$ = [36e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 210:this.$ = [864e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 211:this.$ = [6048e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 212:this.$ = [2.63e+6, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 213:this.$ = [3.156e+7, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 214:this.$ = [1, 0, 0, 0, 0, 1, 0, 0, 0];
break;
case 215:
          var magnitude = (Number($$[$0-1])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 216:
          var magnitude = ((-1)*Number($$[$0-1])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 217:
          var magnitude = Number($$[$0-1]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 218:
          var magnitude = (-1)*Number($$[$0-2]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 219:
          this.$ = [(Number($$[$0-1])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 220:
          this.$ = [((-1)*Number($$[$0-1])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 221:
          this.$ = [Number($$[$0-1]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 222:
          this.$ = [(-1)*Number($$[$0-1]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 223:
          this.$ = [Number(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 224:
          this.$ = [(-1)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 225:
          this.$ = [(Math.PI)*Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 226:
          this.$ = [(-1)*(Math.PI)*Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 227:
          var magnitude = (Number($$[$0-2])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 228:
          var magnitude = Number($$[$0-2]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 229:
          this.$ = [(Number($$[$0-2])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 230:
          this.$ = [Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 231:
      this.$ = $$[$0];
     
break;
case 232:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 233:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 234:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 235:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-2])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 236:
      var arr1 = $$[$0-2];
      var arr2 = $$[$0];
      var res_arr = [(arr1[0]*arr2[0])];
      for(var i = 1; i < 9; i++)
      {
         res_arr[i] = arr1[i] + arr2[i];
      }
      this.$ = res_arr;
     
break;
case 237:
      var res_arr = $$[$0-2];
      var factor = Number($$[$0]);
      res_arr[0] = res_arr[0]*factor;
      this.$ = res_arr;
     
break;
case 238:
      var res_arr = $$[$0-4];
      var factor = Number($$[$0-1]);
      res_arr[0] = res_arr[0]*factor;
      this.$ = res_arr;
     
break;
case 239:
      var arr1 = $$[$0-2];
      var arr2 = $$[$0];
      var res_arr = [(arr1[0]/arr2[0])];
      for(var i = 1; i < 9; i++)
      {
         res_arr[i] = arr1[i] - arr2[i];
      }
      this.$ = res_arr;
    
break;
case 240:
      var res_arr = $$[$0-2];
      var factor = Number($$[$0]);
      if(factor == 0)
        throw new Error("Division by 0!");
      res_arr[0] = res_arr[0]/factor;
      this.$ = res_arr;
     
break;
case 241:
      var res_arr = $$[$0-4];
      var factor = Number($$[$0-1]);
      if(factor == 0)
        throw new Error("Division by 0!");
      res_arr[0] = res_arr[0]/factor;
      this.$ = res_arr;
     
break;
case 242:
      var arr1 = $$[$0-2];

      var power = Number($$[$0]);
      var res_arr = [Math.pow(arr1[0],power)];;
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
            res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
      console.log("fe"+res_arr);
     
break;
case 243:
      var arr1 = $$[$0-4];
      var power = Number($$[$0-1]);
      var res_arr = [Math.pow(arr1[0],power)];;
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
            res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
      console.log("fe"+res_arr);
     
break;
case 244:
      this.$ = $$[$0-1];
    
break;
case 245:
      var arr1 = $$[$0];
      var power = Number(2);
      var res_arr = [Math.pow(arr1[0],power)];
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
          res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
    
break;
case 246:
      var arr1 = $$[$0];
      var power = Number(3);
      var res_arr = [Math.pow(arr1[0],power)];
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
          res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
    
break;
case 247:
         var arr1 = $$[$0-2];
         var arr2 = $$[$0];
         for(var i = 1; i < 9; i++)
         {
          if(arr1[i] != arr2[i])
            throw new Error("Addition cannot be performed on different units!");
         }
         this.$ = arr1;
         this.$[0] = arr1[0] + arr2[0];
       
break;
case 248:
       var arr1 = $$[$0-2];
       var arr2 = $$[$0];
       for(var i = 1; i < 9; i++)
       {
        if(arr1[i] != arr2[i])
          throw new Error("Subtraction cannot be performed on different units!");
       }
       this.$ = arr1;
       this.$[0] = arr1[0] - arr2[0];
     
break;
case 249:
            this.$ = $$[$0];
          
break;
case 250:
            this.$ = $$[$0-1];
          
break;
case 251:
          if(Number($$[$0]) == 1)
            this.$ = "Re. "+($$[$0]).toString();
          else
            throw new Error("");
          
break;
case 252:this.$ = "Rs. "+($$[$0]).toString();
break;
case 253:
          if(Number($$[$0-1]) == 1)
            this.$ = "Re. "+ ($$[$0-1]).toString();
          else
            throw new Error("");
          
break;
case 254:this.$ = "Rs. "+ ($$[$0-1]).toString();
break;
}
},
table: [{3:1,4:2,7:[1,3],8:4,9:5,10:6,11:[1,27],12:[1,9],16:[1,7],18:[1,8],20:[1,10],21:11,22:12,23:21,24:[1,60],25:[1,59],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],119:18,124:[1,16],125:[1,17],126:[1,19],127:[1,20]},{1:[3]},{5:[1,104],6:[1,105],11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110]},{5:[1,111]},{5:[1,113],6:[1,112],11:[1,117],12:[1,118],13:[1,114],14:[1,115],15:[1,116]},{6:[1,119]},{5:[1,121],6:[1,120]},{5:[2,23],6:[2,23],8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],17:[1,122],18:[1,158],19:[2,23],25:[1,128],26:[1,129],28:[1,156],29:[1,157],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],120:[1,124],121:[1,125],122:[1,126],123:[1,127],124:[1,16],125:[1,17],126:[2,23],127:[2,23]},{4:159,8:160,9:161,11:[1,27],12:[1,9],16:[1,7],18:[1,8],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,59],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],119:18,124:[1,16],125:[1,17]},{16:[1,165],18:[1,163],23:164,24:[1,60],25:[1,166],27:[1,61],28:[1,167],29:[1,168]},{5:[2,17],6:[2,17],11:[2,17],12:[2,17],13:[2,17],14:[2,17],15:[2,17],19:[2,17]},{5:[2,18],6:[2,18],11:[2,18],12:[2,18],13:[2,18],14:[2,18],15:[2,18],19:[2,18],35:[1,169],36:[1,170]},{5:[2,19],6:[2,19],11:[2,19],12:[2,19],13:[2,19],14:[2,19],15:[2,19],19:[2,19]},{5:[2,231],6:[2,231],11:[2,231],12:[2,231],13:[2,231],14:[2,231],15:[2,231],19:[2,231]},{5:[2,29],6:[2,29],8:171,11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],16:[1,155],18:[1,158],19:[2,29],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17],126:[2,29],127:[2,29]},{5:[2,30],6:[2,30],8:172,11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],16:[1,155],18:[1,158],19:[2,30],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17],126:[2,30],127:[2,30]},{8:173,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:174,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{6:[2,249],19:[2,249]},{16:[1,176],23:175,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{16:[1,176],23:178,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,22],6:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],126:[1,179],127:[1,180]},{5:[2,31],6:[2,31],11:[2,31],12:[2,31],13:[2,31],14:[2,31],15:[2,31],19:[2,31],35:[2,31],36:[2,31],46:[1,181],49:[1,182],52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{5:[2,32],6:[2,32],11:[2,32],12:[2,32],13:[2,32],14:[2,32],15:[2,32],19:[2,32],35:[2,32],36:[2,32]},{5:[2,33],6:[2,33],11:[2,33],12:[2,33],13:[2,33],14:[2,33],15:[2,33],19:[2,33],35:[2,33],36:[2,33]},{5:[2,34],6:[2,34],11:[2,34],12:[2,34],13:[2,34],14:[2,34],15:[2,34],19:[2,34],35:[2,34],36:[2,34]},{5:[2,35],6:[2,35],11:[2,35],12:[2,35],13:[2,35],14:[2,35],15:[2,35],19:[2,35],35:[2,35],36:[2,35]},{16:[1,176],23:187,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,184],6:[2,184],11:[2,184],12:[2,184],13:[2,184],14:[2,184],15:[2,184],19:[2,184]},{5:[2,185],6:[2,185],11:[2,185],12:[2,185],13:[2,185],14:[2,185],15:[2,185],19:[2,185]},{5:[2,186],6:[2,186],11:[2,186],12:[2,186],13:[2,186],14:[2,186],15:[2,186],19:[2,186]},{5:[2,187],6:[2,187],11:[2,187],12:[2,187],13:[2,187],14:[2,187],15:[2,187],19:[2,187]},{5:[2,188],6:[2,188],11:[2,188],12:[2,188],13:[2,188],14:[2,188],15:[2,188],19:[2,188]},{5:[2,189],6:[2,189],11:[2,189],12:[2,189],13:[2,189],14:[2,189],15:[2,189],19:[2,189]},{5:[2,190],6:[2,190],11:[2,190],12:[2,190],13:[2,190],14:[2,190],15:[2,190],19:[2,190]},{5:[2,191],6:[2,191],11:[2,191],12:[2,191],13:[2,191],14:[2,191],15:[2,191],19:[2,191]},{5:[2,192],6:[2,192],11:[2,192],12:[2,192],13:[2,192],14:[2,192],15:[2,192],19:[2,192]},{5:[2,193],6:[2,193],11:[2,193],12:[2,193],13:[2,193],14:[2,193],15:[2,193],19:[2,193]},{5:[2,194],6:[2,194],11:[2,194],12:[2,194],13:[2,194],14:[2,194],15:[2,194],19:[2,194]},{5:[2,195],6:[2,195],11:[2,195],12:[2,195],13:[2,195],14:[2,195],15:[2,195],19:[2,195]},{5:[2,196],6:[2,196],11:[2,196],12:[2,196],13:[2,196],14:[2,196],15:[2,196],19:[2,196]},{5:[2,197],6:[2,197],11:[2,197],12:[2,197],13:[2,197],14:[2,197],15:[2,197],19:[2,197]},{5:[2,198],6:[2,198],11:[2,198],12:[2,198],13:[2,198],14:[2,198],15:[2,198],19:[2,198]},{5:[2,199],6:[2,199],11:[2,199],12:[2,199],13:[2,199],14:[2,199],15:[2,199],19:[2,199]},{5:[2,200],6:[2,200],11:[2,200],12:[2,200],13:[2,200],14:[2,200],15:[2,200],19:[2,200]},{5:[2,201],6:[2,201],11:[2,201],12:[2,201],13:[2,201],14:[2,201],15:[2,201],19:[2,201]},{5:[2,202],6:[2,202],11:[2,202],12:[2,202],13:[2,202],14:[2,202],15:[2,202],19:[2,202]},{5:[2,203],6:[2,203],11:[2,203],12:[2,203],13:[2,203],14:[2,203],15:[2,203],19:[2,203]},{5:[2,204],6:[2,204],11:[2,204],12:[2,204],13:[2,204],14:[2,204],15:[2,204],19:[2,204]},{5:[2,205],6:[2,205],11:[2,205],12:[2,205],13:[2,205],14:[2,205],15:[2,205],19:[2,205]},{5:[2,206],6:[2,206],11:[2,206],12:[2,206],13:[2,206],14:[2,206],15:[2,206],19:[2,206]},{5:[2,207],6:[2,207],11:[2,207],12:[2,207],13:[2,207],14:[2,207],15:[2,207],19:[2,207]},{5:[2,208],6:[2,208],11:[2,208],12:[2,208],13:[2,208],14:[2,208],15:[2,208],19:[2,208]},{5:[2,209],6:[2,209],11:[2,209],12:[2,209],13:[2,209],14:[2,209],15:[2,209],19:[2,209]},{5:[2,210],6:[2,210],11:[2,210],12:[2,210],13:[2,210],14:[2,210],15:[2,210],19:[2,210]},{5:[2,211],6:[2,211],11:[2,211],12:[2,211],13:[2,211],14:[2,211],15:[2,211],19:[2,211]},{5:[2,212],6:[2,212],11:[2,212],12:[2,212],13:[2,212],14:[2,212],15:[2,212],19:[2,212]},{5:[2,213],6:[2,213],11:[2,213],12:[2,213],13:[2,213],14:[2,213],15:[2,213],19:[2,213]},{5:[2,214],6:[2,214],11:[2,214],12:[2,214],13:[2,214],14:[2,214],15:[2,214],19:[2,214]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25],123:[1,188],126:[2,25],127:[2,25]},{5:[2,24],6:[2,24],11:[2,24],12:[2,24],13:[2,24],14:[2,24],15:[2,24],19:[2,24],126:[2,24],127:[2,24]},{18:[1,189]},{5:[2,62],6:[2,62],11:[2,62],12:[2,62],13:[2,62],14:[2,62],15:[2,62],19:[2,62],35:[2,62],36:[2,62],46:[2,62],48:[1,190],49:[2,62],52:[2,62],56:[2,62],65:[2,62],67:[2,62]},{5:[2,63],6:[2,63],11:[2,63],12:[2,63],13:[2,63],14:[2,63],15:[2,63],16:[1,197],19:[2,63],35:[2,63],36:[2,63],42:195,46:[2,63],47:191,49:[2,63],50:[1,196],52:[2,63],53:94,54:[1,198],56:[2,63],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,63],66:[1,102],67:[2,63],84:[1,192],85:[1,193],86:[1,194]},{5:[2,58],6:[2,58],11:[2,58],12:[2,58],13:[2,58],14:[2,58],15:[2,58],19:[2,58],35:[2,58],36:[2,58]},{5:[2,42],6:[2,42],11:[2,42],12:[2,42],13:[2,42],14:[2,42],15:[2,42],19:[2,42],35:[2,42],36:[2,42],38:[1,202],40:[1,201]},{5:[2,43],6:[2,43],11:[2,43],12:[2,43],13:[2,43],14:[2,43],15:[2,43],16:[1,204],19:[2,43],35:[2,43],36:[2,43],39:203,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,38],6:[2,38],11:[2,38],12:[2,38],13:[2,38],14:[2,38],15:[2,38],19:[2,38],35:[2,38],36:[2,38]},{5:[2,117],6:[2,117],11:[2,117],12:[2,117],13:[2,117],14:[2,117],15:[2,117],19:[2,117],35:[2,117],36:[2,117]},{5:[2,118],6:[2,118],11:[2,118],12:[2,118],13:[2,118],14:[2,118],15:[2,118],19:[2,118],35:[2,118],36:[2,118]},{5:[2,119],6:[2,119],11:[2,119],12:[2,119],13:[2,119],14:[2,119],15:[2,119],19:[2,119],35:[2,119],36:[2,119]},{5:[2,120],6:[2,120],11:[2,120],12:[2,120],13:[2,120],14:[2,120],15:[2,120],19:[2,120],35:[2,120],36:[2,120]},{5:[2,121],6:[2,121],11:[2,121],12:[2,121],13:[2,121],14:[2,121],15:[2,121],19:[2,121],35:[2,121],36:[2,121]},{5:[2,122],6:[2,122],11:[2,122],12:[2,122],13:[2,122],14:[2,122],15:[2,122],19:[2,122],35:[2,122],36:[2,122]},{5:[2,123],6:[2,123],11:[2,123],12:[2,123],13:[2,123],14:[2,123],15:[2,123],19:[2,123],35:[2,123],36:[2,123]},{5:[2,124],6:[2,124],11:[2,124],12:[2,124],13:[2,124],14:[2,124],15:[2,124],19:[2,124],35:[2,124],36:[2,124]},{5:[2,125],6:[2,125],11:[2,125],12:[2,125],13:[2,125],14:[2,125],15:[2,125],19:[2,125],35:[2,125],36:[2,125]},{5:[2,126],6:[2,126],11:[2,126],12:[2,126],13:[2,126],14:[2,126],15:[2,126],19:[2,126],35:[2,126],36:[2,126]},{5:[2,73],6:[2,73],11:[2,73],12:[2,73],13:[2,73],14:[2,73],15:[2,73],19:[2,73],35:[2,73],36:[2,73],41:[1,208],44:[1,207],46:[2,73],48:[2,73],49:[2,73],50:[1,206],52:[2,73],56:[2,73],65:[2,73],67:[2,73]},{5:[2,74],6:[2,74],11:[2,74],12:[2,74],13:[2,74],14:[2,74],15:[2,74],16:[1,219],19:[2,74],35:[2,74],36:[2,74],42:209,46:[2,74],48:[2,74],49:[2,74],52:[2,74],53:94,54:[1,198],56:[2,74],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,74],66:[1,102],67:[2,74],78:[1,214],79:[1,215],80:[1,212],81:[1,213],82:[1,211],83:[1,210],84:[1,216],85:[1,217],86:[1,218]},{5:[2,69],6:[2,69],11:[2,69],12:[2,69],13:[2,69],14:[2,69],15:[2,69],19:[2,69],35:[2,69],36:[2,69]},{5:[2,52],6:[2,52],11:[2,52],12:[2,52],13:[2,52],14:[2,52],15:[2,52],16:[1,219],19:[2,52],35:[2,52],36:[2,52],38:[2,52],40:[2,52],42:220,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,157],6:[2,157],11:[2,157],12:[2,157],13:[2,157],14:[2,157],15:[2,157],19:[2,157],35:[2,157],36:[2,157]},{5:[2,95],6:[2,95],11:[2,95],12:[2,95],13:[2,95],14:[2,95],15:[2,95],19:[2,95],35:[2,95],36:[2,95],41:[2,95],44:[2,95],46:[2,95],48:[2,95],49:[2,95],50:[2,95],52:[2,95],54:[2,95],56:[2,95],58:[1,230],65:[2,95],67:[2,95],78:[1,225],79:[1,226],80:[1,223],81:[1,224],82:[1,222],83:[1,221],84:[1,227],85:[1,228],86:[1,229]},{5:[2,96],6:[2,96],11:[2,96],12:[2,96],13:[2,96],14:[2,96],15:[2,96],19:[2,96],35:[2,96],36:[2,96],41:[2,96],44:[2,96],46:[2,96],48:[2,96],49:[2,96],50:[2,96],52:[2,96],54:[2,96],56:[2,96],57:240,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,96],66:[1,102],67:[2,96],78:[1,235],79:[1,236],80:[1,233],81:[1,234],82:[1,232],83:[1,231],84:[1,237],85:[1,238],86:[1,239]},{5:[2,85],6:[2,85],11:[2,85],12:[2,85],13:[2,85],14:[2,85],15:[2,85],16:[1,251],19:[2,85],35:[2,85],36:[2,85],41:[2,85],44:[2,85],46:[2,85],48:[2,85],49:[2,85],50:[2,85],52:[2,85],53:250,56:[2,85],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,85],66:[1,102],67:[2,85],78:[1,245],79:[1,246],80:[1,243],81:[1,244],82:[1,242],83:[1,241],84:[1,247],85:[1,248],86:[1,249]},{5:[2,151],6:[2,151],11:[2,151],12:[2,151],13:[2,151],14:[2,151],15:[2,151],19:[2,151],35:[2,151],36:[2,151]},{5:[2,139],6:[2,139],11:[2,139],12:[2,139],13:[2,139],14:[2,139],15:[2,139],19:[2,139],35:[2,139],36:[2,139]},{5:[2,145],6:[2,145],11:[2,145],12:[2,145],13:[2,145],14:[2,145],15:[2,145],19:[2,145],35:[2,145],36:[2,145]},{5:[2,127],6:[2,127],11:[2,127],12:[2,127],13:[2,127],14:[2,127],15:[2,127],19:[2,127],35:[2,127],36:[2,127]},{5:[2,133],6:[2,133],11:[2,133],12:[2,133],13:[2,133],14:[2,133],15:[2,133],19:[2,133],35:[2,133],36:[2,133]},{5:[2,163],6:[2,163],11:[2,163],12:[2,163],13:[2,163],14:[2,163],15:[2,163],19:[2,163],35:[2,163],36:[2,163]},{5:[2,170],6:[2,170],11:[2,170],12:[2,170],13:[2,170],14:[2,170],15:[2,170],19:[2,170],35:[2,170],36:[2,170]},{5:[2,177],6:[2,177],11:[2,177],12:[2,177],13:[2,177],14:[2,177],15:[2,177],19:[2,177],35:[2,177],36:[2,177]},{5:[2,84],6:[2,84],11:[2,84],12:[2,84],13:[2,84],14:[2,84],15:[2,84],19:[2,84],35:[2,84],36:[2,84],38:[2,84],40:[2,84],41:[2,84],44:[2,84],46:[2,84],48:[2,84],49:[2,84],50:[2,84],52:[2,84],54:[1,252],56:[2,84],65:[2,84],67:[2,84]},{5:[2,80],6:[2,80],11:[2,80],12:[2,80],13:[2,80],14:[2,80],15:[2,80],19:[2,80],35:[2,80],36:[2,80]},{5:[2,102],6:[2,102],11:[2,102],12:[2,102],13:[2,102],14:[2,102],15:[2,102],19:[2,102],35:[2,102],36:[2,102],38:[2,102],40:[2,102],41:[2,102],44:[2,102],46:[2,102],48:[2,102],49:[2,102],50:[2,102],52:[2,102],54:[2,102],56:[2,102],58:[2,102],65:[2,102],67:[2,102],78:[2,102],79:[2,102],80:[2,102],81:[2,102],82:[2,102],83:[2,102],84:[2,102],85:[2,102],86:[2,102]},{5:[2,103],6:[2,103],11:[2,103],12:[2,103],13:[2,103],14:[2,103],15:[2,103],19:[2,103],35:[2,103],36:[2,103],38:[2,103],40:[2,103],41:[2,103],44:[2,103],46:[2,103],48:[2,103],49:[2,103],50:[2,103],52:[2,103],54:[2,103],56:[2,103],58:[2,103],65:[2,103],67:[2,103],78:[2,103],79:[2,103],80:[2,103],81:[2,103],82:[2,103],83:[2,103],84:[2,103],85:[2,103],86:[2,103]},{5:[2,104],6:[2,104],11:[2,104],12:[2,104],13:[2,104],14:[2,104],15:[2,104],19:[2,104],35:[2,104],36:[2,104],38:[2,104],40:[2,104],41:[2,104],44:[2,104],46:[2,104],48:[2,104],49:[2,104],50:[2,104],52:[2,104],54:[2,104],56:[2,104],58:[2,104],65:[2,104],67:[2,104],78:[2,104],79:[2,104],80:[2,104],81:[2,104],82:[2,104],83:[2,104],84:[2,104],85:[2,104],86:[2,104]},{5:[2,105],6:[2,105],11:[2,105],12:[2,105],13:[2,105],14:[2,105],15:[2,105],19:[2,105],35:[2,105],36:[2,105],38:[2,105],40:[2,105],41:[2,105],44:[2,105],46:[2,105],48:[2,105],49:[2,105],50:[2,105],52:[2,105],54:[2,105],56:[2,105],58:[2,105],59:253,65:[2,105],66:[1,102],67:[2,105],78:[2,105],79:[2,105],80:[2,105],81:[2,105],82:[2,105],83:[2,105],84:[2,105],85:[2,105],86:[2,105]},{5:[2,107],6:[2,107],11:[2,107],12:[2,107],13:[2,107],14:[2,107],15:[2,107],19:[2,107],35:[2,107],36:[2,107],38:[2,107],40:[2,107],41:[2,107],44:[2,107],46:[2,107],48:[2,107],49:[2,107],50:[2,107],52:[2,107],54:[2,107],56:[2,107],58:[2,107],65:[2,107],67:[2,107],78:[2,107],79:[2,107],80:[2,107],81:[2,107],82:[2,107],83:[2,107],84:[2,107],85:[2,107],86:[2,107]},{5:[2,91],6:[2,91],11:[2,91],12:[2,91],13:[2,91],14:[2,91],15:[2,91],19:[2,91],35:[2,91],36:[2,91]},{5:[2,113],6:[2,113],11:[2,113],12:[2,113],13:[2,113],14:[2,113],15:[2,113],19:[2,113],35:[2,113],36:[2,113],38:[2,113],40:[2,113],41:[2,113],44:[2,113],46:[2,113],48:[2,113],49:[2,113],50:[2,113],52:[2,113],54:[2,113],56:[2,113],58:[2,113],65:[2,113],67:[2,113],78:[2,113],79:[2,113],80:[2,113],81:[2,113],82:[2,113],83:[2,113],84:[2,113],85:[2,113],86:[2,113]},{5:[2,108],6:[2,108],11:[2,108],12:[2,108],13:[2,108],14:[2,108],15:[2,108],19:[2,108],35:[2,108],36:[2,108]},{6:[1,254]},{1:[2,2]},{4:255,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:259,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:260,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:261,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:262,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{6:[1,263]},{1:[2,4]},{6:[1,264]},{8:265,11:[1,27],12:[1,269],16:[1,268],18:[1,267],22:266,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:270,11:[1,27],12:[1,269],16:[1,268],18:[1,272],22:271,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{11:[1,27],12:[1,269],16:[1,176],18:[1,274],22:273,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{8:275,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:276,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{1:[2,6]},{1:[2,7]},{6:[1,277]},{5:[2,14],6:[2,14],11:[2,14],12:[2,14],13:[2,14],14:[2,14],15:[2,14],19:[2,14]},{5:[2,232],6:[2,232],11:[2,232],12:[2,232],13:[2,232],14:[2,232],15:[1,116],19:[2,232]},{6:[2,215],19:[2,215]},{6:[2,217],19:[2,217]},{6:[2,219],19:[2,219]},{6:[2,221],19:[2,221]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26],123:[1,278],126:[2,26],127:[2,26]},{5:[2,27],6:[2,27],11:[2,27],12:[2,27],13:[2,27],14:[2,27],15:[2,27],19:[2,27],126:[2,27],127:[2,27]},{5:[2,64],6:[2,64],11:[2,64],12:[2,64],13:[2,64],14:[2,64],15:[2,64],16:[1,197],19:[2,64],35:[2,64],36:[2,64],42:195,46:[2,64],47:279,49:[2,64],50:[1,196],52:[2,64],53:94,54:[1,198],56:[2,64],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,64],66:[1,102],67:[2,64]},{5:[2,59],6:[2,59],11:[2,59],12:[2,59],13:[2,59],14:[2,59],15:[2,59],19:[2,59],35:[2,59],36:[2,59]},{5:[2,44],6:[2,44],11:[2,44],12:[2,44],13:[2,44],14:[2,44],15:[2,44],16:[1,204],19:[2,44],35:[2,44],36:[2,44],39:280,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,39],6:[2,39],11:[2,39],12:[2,39],13:[2,39],14:[2,39],15:[2,39],19:[2,39],35:[2,39],36:[2,39]},{5:[2,75],6:[2,75],11:[2,75],12:[2,75],13:[2,75],14:[2,75],15:[2,75],16:[1,219],19:[2,75],35:[2,75],36:[2,75],42:281,46:[2,75],48:[2,75],49:[2,75],52:[2,75],53:94,54:[1,198],56:[2,75],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,75],66:[1,102],67:[2,75]},{5:[2,70],6:[2,70],11:[2,70],12:[2,70],13:[2,70],14:[2,70],15:[2,70],19:[2,70],35:[2,70],36:[2,70]},{5:[2,53],6:[2,53],11:[2,53],12:[2,53],13:[2,53],14:[2,53],15:[2,53],16:[1,219],19:[2,53],35:[2,53],36:[2,53],38:[2,53],40:[2,53],42:282,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,49],6:[2,49],11:[2,49],12:[2,49],13:[2,49],14:[2,49],15:[2,49],19:[2,49],35:[2,49],36:[2,49]},{5:[2,158],6:[2,158],11:[2,158],12:[2,158],13:[2,158],14:[2,158],15:[2,158],19:[2,158],35:[2,158],36:[2,158]},{5:[2,152],6:[2,152],11:[2,152],12:[2,152],13:[2,152],14:[2,152],15:[2,152],19:[2,152],35:[2,152],36:[2,152]},{5:[2,140],6:[2,140],11:[2,140],12:[2,140],13:[2,140],14:[2,140],15:[2,140],19:[2,140],35:[2,140],36:[2,140]},{5:[2,146],6:[2,146],11:[2,146],12:[2,146],13:[2,146],14:[2,146],15:[2,146],19:[2,146],35:[2,146],36:[2,146]},{5:[2,128],6:[2,128],11:[2,128],12:[2,128],13:[2,128],14:[2,128],15:[2,128],19:[2,128],35:[2,128],36:[2,128]},{5:[2,134],6:[2,134],11:[2,134],12:[2,134],13:[2,134],14:[2,134],15:[2,134],19:[2,134],35:[2,134],36:[2,134]},{5:[2,164],6:[2,164],11:[2,164],12:[2,164],13:[2,164],14:[2,164],15:[2,164],19:[2,164],35:[2,164],36:[2,164]},{5:[2,171],6:[2,171],11:[2,171],12:[2,171],13:[2,171],14:[2,171],15:[2,171],19:[2,171],35:[2,171],36:[2,171]},{5:[2,178],6:[2,178],11:[2,178],12:[2,178],13:[2,178],14:[2,178],15:[2,178],19:[2,178],35:[2,178],36:[2,178]},{5:[2,86],6:[2,86],11:[2,86],12:[2,86],13:[2,86],14:[2,86],15:[2,86],16:[1,251],19:[2,86],35:[2,86],36:[2,86],38:[2,86],40:[2,86],41:[2,86],44:[2,86],46:[2,86],48:[2,86],49:[2,86],50:[2,86],52:[2,86],53:283,56:[2,86],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,86],66:[1,102],67:[2,86]},{5:[2,81],6:[2,81],11:[2,81],12:[2,81],13:[2,81],14:[2,81],15:[2,81],19:[2,81],35:[2,81],36:[2,81]},{5:[2,97],6:[2,97],11:[2,97],12:[2,97],13:[2,97],14:[2,97],15:[2,97],19:[2,97],35:[2,97],36:[2,97],38:[2,97],40:[2,97],41:[2,97],44:[2,97],46:[2,97],48:[2,97],49:[2,97],50:[2,97],52:[2,97],54:[2,97],56:[2,97],57:284,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,97],66:[1,102],67:[2,97]},{5:[2,92],6:[2,92],11:[2,92],12:[2,92],13:[2,92],14:[2,92],15:[2,92],19:[2,92],35:[2,92],36:[2,92]},{5:[2,109],6:[2,109],11:[2,109],12:[2,109],13:[2,109],14:[2,109],15:[2,109],19:[2,109],35:[2,109],36:[2,109]},{5:[2,110],6:[2,110],11:[2,110],12:[2,110],13:[2,110],14:[2,110],15:[2,110],19:[2,110],35:[2,110],36:[2,110]},{5:[2,114],6:[2,114],11:[2,114],12:[2,114],13:[2,114],14:[2,114],15:[2,114],19:[2,114],35:[2,114],36:[2,114]},{5:[2,115],6:[2,115],11:[2,115],12:[2,115],13:[2,115],14:[2,115],15:[2,115],19:[2,115],35:[2,115],36:[2,115]},{8:123,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:171,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:172,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{4:285,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,288]},{11:[1,117],12:[1,118],13:[1,114],14:[1,115],15:[1,116],19:[1,289]},{19:[1,290]},{5:[2,22],6:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],19:[2,22]},{4:291,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,21],6:[2,21],11:[2,21],12:[2,21],13:[2,21],14:[2,21],15:[2,21],19:[2,21]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],19:[2,23],25:[1,296],26:[1,129],120:[1,292],121:[1,293],122:[1,294],123:[1,295]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25],123:[1,297]},{5:[2,29],6:[2,29],11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],19:[2,29]},{5:[2,30],6:[2,30],11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],19:[2,30]},{16:[1,299],21:298,30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{16:[1,299],21:300,30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,233],6:[2,233],11:[2,233],12:[2,233],13:[2,233],14:[2,233],15:[1,116],19:[2,233]},{5:[2,234],6:[2,234],11:[2,234],12:[2,234],13:[2,234],14:[2,234],15:[1,116],19:[2,234]},{5:[2,245],6:[2,245],11:[2,245],12:[2,245],13:[2,245],14:[2,245],15:[2,245],19:[2,245]},{5:[2,246],6:[2,246],11:[2,246],12:[2,246],13:[2,246],14:[2,246],15:[2,246],19:[2,246]},{5:[2,251],6:[2,251]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],19:[2,23],25:[1,301],26:[1,129]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25]},{5:[2,252],6:[2,252]},{5:[2,253],6:[2,253]},{5:[2,254],6:[2,254]},{5:[2,60],6:[2,60],11:[2,60],12:[2,60],13:[2,60],14:[2,60],15:[2,60],16:[1,303],19:[2,60],30:304,35:[2,60],36:[2,60],42:195,43:80,45:302,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,71],6:[2,71],11:[2,71],12:[2,71],13:[2,71],14:[2,71],15:[2,71],16:[1,307],19:[2,71],30:308,35:[2,71],36:[2,71],42:195,43:306,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,82],6:[2,82],11:[2,82],12:[2,82],13:[2,82],14:[2,82],15:[2,82],16:[1,310],19:[2,82],30:311,35:[2,82],36:[2,82],42:195,47:62,48:[1,305],50:[1,196],51:309,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,93],6:[2,93],11:[2,93],12:[2,93],13:[2,93],14:[2,93],15:[2,93],16:[1,313],19:[2,93],30:314,35:[2,93],36:[2,93],42:195,47:62,48:[1,305],50:[1,196],53:94,54:[1,198],55:312,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,111],6:[2,111],11:[2,111],12:[2,111],13:[2,111],14:[2,111],15:[2,111],16:[1,316],19:[2,111],30:317,35:[2,111],36:[2,111],42:195,47:62,48:[1,305],50:[1,196],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:315,66:[1,102]},{5:[2,116],6:[2,116],11:[2,116],12:[2,116],13:[2,116],14:[2,116],15:[2,116],19:[2,116],35:[2,116],36:[2,116]},{5:[2,20],6:[2,20],11:[2,20],12:[2,20],13:[2,20],14:[2,20],15:[2,20],19:[2,20]},{6:[2,223],19:[2,223]},{4:318,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,65],6:[2,65],11:[2,65],12:[2,65],13:[2,65],14:[2,65],15:[2,65],16:[1,197],19:[2,65],35:[2,65],36:[2,65],42:195,46:[2,65],47:319,49:[2,65],50:[1,196],52:[2,65],53:94,54:[1,198],56:[2,65],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,65],66:[1,102],67:[2,65]},{5:[2,66],6:[2,66],11:[2,66],12:[2,66],13:[2,66],14:[2,66],15:[2,66],19:[2,66],35:[2,66],36:[2,66],46:[2,66],49:[2,66],52:[2,66],56:[2,66],65:[2,66],67:[2,66]},{5:[2,169],6:[2,169],11:[2,169],12:[2,169],13:[2,169],14:[2,169],15:[2,169],19:[2,169],35:[2,169],36:[2,169]},{5:[2,176],6:[2,176],11:[2,176],12:[2,176],13:[2,176],14:[2,176],15:[2,176],19:[2,176],35:[2,176],36:[2,176]},{5:[2,183],6:[2,183],11:[2,183],12:[2,183],13:[2,183],14:[2,183],15:[2,183],19:[2,183],35:[2,183],36:[2,183]},{5:[2,73],6:[2,73],11:[2,73],12:[2,73],13:[2,73],14:[2,73],15:[2,73],19:[2,73],35:[2,73],36:[2,73],46:[2,73],48:[2,73],49:[2,73],50:[1,206],52:[2,73],56:[2,73],65:[2,73],67:[2,73]},{5:[2,74],6:[2,74],11:[2,74],12:[2,74],13:[2,74],14:[2,74],15:[2,74],16:[1,219],19:[2,74],35:[2,74],36:[2,74],42:209,46:[2,74],48:[2,74],49:[2,74],52:[2,74],53:94,54:[1,198],56:[2,74],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,74],66:[1,102],67:[2,74]},{50:[1,134],54:[1,147],58:[1,149]},{5:[2,85],6:[2,85],11:[2,85],12:[2,85],13:[2,85],14:[2,85],15:[2,85],16:[1,251],19:[2,85],35:[2,85],36:[2,85],38:[2,85],40:[2,85],41:[2,85],44:[2,85],46:[2,85],48:[2,85],49:[2,85],50:[2,85],52:[2,85],53:250,56:[2,85],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,85],66:[1,102],67:[2,85]},{5:[2,95],6:[2,95],11:[2,95],12:[2,95],13:[2,95],14:[2,95],15:[2,95],19:[2,95],35:[2,95],36:[2,95],38:[2,95],40:[2,95],41:[2,95],44:[2,95],46:[2,95],48:[2,95],49:[2,95],50:[2,95],52:[2,95],54:[2,95],56:[2,95],58:[1,230],65:[2,95],67:[2,95]},{5:[2,96],6:[2,96],11:[2,96],12:[2,96],13:[2,96],14:[2,96],15:[2,96],19:[2,96],35:[2,96],36:[2,96],38:[2,96],40:[2,96],41:[2,96],44:[2,96],46:[2,96],48:[2,96],49:[2,96],50:[2,96],52:[2,96],54:[2,96],56:[2,96],57:240,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,96],66:[1,102],67:[2,96]},{5:[2,45],6:[2,45],11:[2,45],12:[2,45],13:[2,45],14:[2,45],15:[2,45],16:[1,204],19:[2,45],35:[2,45],36:[2,45],39:320,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,40],6:[2,40],11:[2,40],12:[2,40],13:[2,40],14:[2,40],15:[2,40],16:[1,322],19:[2,40],35:[2,40],36:[2,40],37:321,42:323,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,46],6:[2,46],11:[2,46],12:[2,46],13:[2,46],14:[2,46],15:[2,46],19:[2,46],35:[2,46],36:[2,46]},{44:[1,136],54:[1,147],58:[1,149]},{44:[1,207]},{5:[2,76],6:[2,76],11:[2,76],12:[2,76],13:[2,76],14:[2,76],15:[2,76],16:[1,219],19:[2,76],35:[2,76],36:[2,76],42:324,46:[2,76],48:[2,76],49:[2,76],52:[2,76],53:94,54:[1,198],56:[2,76],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,76],66:[1,102],67:[2,76]},{5:[2,54],6:[2,54],11:[2,54],12:[2,54],13:[2,54],14:[2,54],15:[2,54],16:[1,219],19:[2,54],35:[2,54],36:[2,54],38:[2,54],40:[2,54],42:325,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,50],6:[2,50],11:[2,50],12:[2,50],13:[2,50],14:[2,50],15:[2,50],16:[1,307],19:[2,50],30:308,35:[2,50],36:[2,50],42:195,43:326,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,77],6:[2,77],11:[2,77],12:[2,77],13:[2,77],14:[2,77],15:[2,77],19:[2,77],35:[2,77],36:[2,77],46:[2,77],48:[2,77],49:[2,77],52:[2,77],56:[2,77],65:[2,77],67:[2,77]},{5:[2,162],6:[2,162],11:[2,162],12:[2,162],13:[2,162],14:[2,162],15:[2,162],19:[2,162],35:[2,162],36:[2,162]},{5:[2,156],6:[2,156],11:[2,156],12:[2,156],13:[2,156],14:[2,156],15:[2,156],19:[2,156],35:[2,156],36:[2,156]},{5:[2,144],6:[2,144],11:[2,144],12:[2,144],13:[2,144],14:[2,144],15:[2,144],19:[2,144],35:[2,144],36:[2,144]},{5:[2,150],6:[2,150],11:[2,150],12:[2,150],13:[2,150],14:[2,150],15:[2,150],19:[2,150],35:[2,150],36:[2,150]},{5:[2,132],6:[2,132],11:[2,132],12:[2,132],13:[2,132],14:[2,132],15:[2,132],19:[2,132],35:[2,132],36:[2,132]},{5:[2,138],6:[2,138],11:[2,138],12:[2,138],13:[2,138],14:[2,138],15:[2,138],19:[2,138],35:[2,138],36:[2,138]},{5:[2,168],6:[2,168],11:[2,168],12:[2,168],13:[2,168],14:[2,168],15:[2,168],19:[2,168],35:[2,168],36:[2,168]},{5:[2,175],6:[2,175],11:[2,175],12:[2,175],13:[2,175],14:[2,175],15:[2,175],19:[2,175],35:[2,175],36:[2,175]},{5:[2,182],6:[2,182],11:[2,182],12:[2,182],13:[2,182],14:[2,182],15:[2,182],19:[2,182],35:[2,182],36:[2,182]},{54:[1,147],58:[1,149]},{5:[2,55],6:[2,55],11:[2,55],12:[2,55],13:[2,55],14:[2,55],15:[2,55],19:[2,55],35:[2,55],36:[2,55],38:[2,55],40:[2,55]},{5:[2,159],6:[2,159],11:[2,159],12:[2,159],13:[2,159],14:[2,159],15:[2,159],19:[2,159],35:[2,159],36:[2,159]},{5:[2,153],6:[2,153],11:[2,153],12:[2,153],13:[2,153],14:[2,153],15:[2,153],19:[2,153],35:[2,153],36:[2,153]},{5:[2,141],6:[2,141],11:[2,141],12:[2,141],13:[2,141],14:[2,141],15:[2,141],19:[2,141],35:[2,141],36:[2,141]},{5:[2,147],6:[2,147],11:[2,147],12:[2,147],13:[2,147],14:[2,147],15:[2,147],19:[2,147],35:[2,147],36:[2,147]},{5:[2,129],6:[2,129],11:[2,129],12:[2,129],13:[2,129],14:[2,129],15:[2,129],19:[2,129],35:[2,129],36:[2,129]},{5:[2,135],6:[2,135],11:[2,135],12:[2,135],13:[2,135],14:[2,135],15:[2,135],19:[2,135],35:[2,135],36:[2,135]},{5:[2,165],6:[2,165],11:[2,165],12:[2,165],13:[2,165],14:[2,165],15:[2,165],19:[2,165],35:[2,165],36:[2,165]},{5:[2,172],6:[2,172],11:[2,172],12:[2,172],13:[2,172],14:[2,172],15:[2,172],19:[2,172],35:[2,172],36:[2,172]},{5:[2,179],6:[2,179],11:[2,179],12:[2,179],13:[2,179],14:[2,179],15:[2,179],19:[2,179],35:[2,179],36:[2,179]},{5:[2,98],6:[2,98],11:[2,98],12:[2,98],13:[2,98],14:[2,98],15:[2,98],19:[2,98],35:[2,98],36:[2,98],38:[2,98],40:[2,98],41:[2,98],44:[2,98],46:[2,98],48:[2,98],49:[2,98],50:[2,98],52:[2,98],54:[2,98],56:[2,98],57:327,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,98],66:[1,102],67:[2,98]},{5:[2,160],6:[2,160],11:[2,160],12:[2,160],13:[2,160],14:[2,160],15:[2,160],19:[2,160],35:[2,160],36:[2,160]},{5:[2,154],6:[2,154],11:[2,154],12:[2,154],13:[2,154],14:[2,154],15:[2,154],19:[2,154],35:[2,154],36:[2,154]},{5:[2,142],6:[2,142],11:[2,142],12:[2,142],13:[2,142],14:[2,142],15:[2,142],19:[2,142],35:[2,142],36:[2,142]},{5:[2,148],6:[2,148],11:[2,148],12:[2,148],13:[2,148],14:[2,148],15:[2,148],19:[2,148],35:[2,148],36:[2,148]},{5:[2,130],6:[2,130],11:[2,130],12:[2,130],13:[2,130],14:[2,130],15:[2,130],19:[2,130],35:[2,130],36:[2,130]},{5:[2,136],6:[2,136],11:[2,136],12:[2,136],13:[2,136],14:[2,136],15:[2,136],19:[2,136],35:[2,136],36:[2,136]},{5:[2,166],6:[2,166],11:[2,166],12:[2,166],13:[2,166],14:[2,166],15:[2,166],19:[2,166],35:[2,166],36:[2,166]},{5:[2,173],6:[2,173],11:[2,173],12:[2,173],13:[2,173],14:[2,173],15:[2,173],19:[2,173],35:[2,173],36:[2,173]},{5:[2,180],6:[2,180],11:[2,180],12:[2,180],13:[2,180],14:[2,180],15:[2,180],19:[2,180],35:[2,180],36:[2,180]},{5:[2,99],6:[2,99],11:[2,99],12:[2,99],13:[2,99],14:[2,99],15:[2,99],19:[2,99],35:[2,99],36:[2,99],38:[2,99],40:[2,99],41:[2,99],44:[2,99],46:[2,99],48:[2,99],49:[2,99],50:[2,99],52:[2,99],54:[2,99],56:[2,99],65:[2,99],67:[2,99]},{5:[2,161],6:[2,161],11:[2,161],12:[2,161],13:[2,161],14:[2,161],15:[2,161],19:[2,161],35:[2,161],36:[2,161]},{5:[2,155],6:[2,155],11:[2,155],12:[2,155],13:[2,155],14:[2,155],15:[2,155],19:[2,155],35:[2,155],36:[2,155]},{5:[2,143],6:[2,143],11:[2,143],12:[2,143],13:[2,143],14:[2,143],15:[2,143],19:[2,143],35:[2,143],36:[2,143]},{5:[2,149],6:[2,149],11:[2,149],12:[2,149],13:[2,149],14:[2,149],15:[2,149],19:[2,149],35:[2,149],36:[2,149]},{5:[2,131],6:[2,131],11:[2,131],12:[2,131],13:[2,131],14:[2,131],15:[2,131],19:[2,131],35:[2,131],36:[2,131]},{5:[2,137],6:[2,137],11:[2,137],12:[2,137],13:[2,137],14:[2,137],15:[2,137],19:[2,137],35:[2,137],36:[2,137]},{5:[2,167],6:[2,167],11:[2,167],12:[2,167],13:[2,167],14:[2,167],15:[2,167],19:[2,167],35:[2,167],36:[2,167]},{5:[2,174],6:[2,174],11:[2,174],12:[2,174],13:[2,174],14:[2,174],15:[2,174],19:[2,174],35:[2,174],36:[2,174]},{5:[2,181],6:[2,181],11:[2,181],12:[2,181],13:[2,181],14:[2,181],15:[2,181],19:[2,181],35:[2,181],36:[2,181]},{5:[2,88],6:[2,88],11:[2,88],12:[2,88],13:[2,88],14:[2,88],15:[2,88],19:[2,88],35:[2,88],36:[2,88],38:[2,88],40:[2,88],41:[2,88],44:[2,88],46:[2,88],48:[2,88],49:[2,88],50:[2,88],52:[2,88],56:[2,88],65:[2,88],67:[2,88]},{58:[1,149]},{5:[2,87],6:[2,87],11:[2,87],12:[2,87],13:[2,87],14:[2,87],15:[2,87],16:[1,251],19:[2,87],35:[2,87],36:[2,87],38:[2,87],40:[2,87],41:[2,87],44:[2,87],46:[2,87],48:[2,87],49:[2,87],50:[2,87],52:[2,87],53:328,56:[2,87],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,87],66:[1,102],67:[2,87]},{5:[2,106],6:[2,106],11:[2,106],12:[2,106],13:[2,106],14:[2,106],15:[2,106],19:[2,106],35:[2,106],36:[2,106],38:[2,106],40:[2,106],41:[2,106],44:[2,106],46:[2,106],48:[2,106],49:[2,106],50:[2,106],52:[2,106],54:[2,106],56:[2,106],58:[2,106],65:[2,106],67:[2,106],78:[2,106],79:[2,106],80:[2,106],81:[2,106],82:[2,106],83:[2,106],84:[2,106],85:[2,106],86:[2,106]},{1:[2,1]},{5:[2,9],6:[2,9],11:[2,9],12:[2,9],13:[1,108],14:[1,109],15:[1,110],19:[2,9]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],17:[1,122],19:[2,23],25:[1,301],26:[1,129],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146]},{4:329,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{16:[1,176],18:[1,163],23:164,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,10],6:[2,10],11:[2,10],12:[2,10],13:[1,108],14:[1,109],15:[1,110],19:[2,10]},{5:[2,11],6:[2,11],11:[2,11],12:[2,11],13:[2,11],14:[2,11],15:[1,110],19:[2,11]},{5:[2,12],6:[2,12],11:[2,12],12:[2,12],13:[2,12],14:[2,12],15:[1,110],19:[2,12]},{5:[2,13],6:[2,13],11:[2,13],12:[2,13],13:[2,13],14:[2,13],15:[2,13],19:[2,13]},{1:[2,3]},{1:[2,5]},{5:[2,236],6:[2,236],11:[2,236],12:[2,236],13:[2,236],14:[2,236],15:[1,116],19:[2,236]},{5:[2,237],6:[2,237],11:[2,237],12:[2,237],13:[2,237],14:[2,237],15:[2,237],19:[2,237]},{4:330,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,23],6:[2,23],8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],18:[1,158],19:[2,23],25:[1,301],26:[1,129],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{16:[1,176],23:164,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,239],6:[2,239],11:[2,239],12:[2,239],13:[2,239],14:[2,239],15:[1,116],19:[2,239]},{5:[2,240],6:[2,240],11:[2,240],12:[2,240],13:[2,240],14:[2,240],15:[2,240],19:[2,240]},{4:331,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,242],6:[2,242],11:[2,242],12:[2,242],13:[2,242],14:[2,242],15:[2,242],19:[2,242]},{4:332,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,247],6:[2,247],11:[2,247],12:[2,247],13:[1,114],14:[1,115],15:[1,116],19:[2,247]},{5:[2,248],6:[2,248],11:[2,248],12:[2,248],13:[1,114],14:[1,115],15:[1,116],19:[2,248]},{1:[2,8]},{6:[2,225],19:[2,225]},{5:[2,67],6:[2,67],11:[2,67],12:[2,67],13:[2,67],14:[2,67],15:[2,67],19:[2,67],35:[2,67],36:[2,67],46:[2,67],49:[2,67],52:[2,67],56:[2,67],65:[2,67],67:[2,67]},{5:[2,47],6:[2,47],11:[2,47],12:[2,47],13:[2,47],14:[2,47],15:[2,47],19:[2,47],35:[2,47],36:[2,47]},{5:[2,78],6:[2,78],11:[2,78],12:[2,78],13:[2,78],14:[2,78],15:[2,78],19:[2,78],35:[2,78],36:[2,78],46:[2,78],48:[2,78],49:[2,78],52:[2,78],56:[2,78],65:[2,78],67:[2,78]},{5:[2,56],6:[2,56],11:[2,56],12:[2,56],13:[2,56],14:[2,56],15:[2,56],19:[2,56],35:[2,56],36:[2,56],38:[2,56],40:[2,56]},{5:[2,89],6:[2,89],11:[2,89],12:[2,89],13:[2,89],14:[2,89],15:[2,89],19:[2,89],35:[2,89],36:[2,89],38:[2,89],40:[2,89],41:[2,89],44:[2,89],46:[2,89],48:[2,89],49:[2,89],50:[2,89],52:[2,89],56:[2,89],65:[2,89],67:[2,89]},{5:[2,100],6:[2,100],11:[2,100],12:[2,100],13:[2,100],14:[2,100],15:[2,100],19:[2,100],35:[2,100],36:[2,100],38:[2,100],40:[2,100],41:[2,100],44:[2,100],46:[2,100],48:[2,100],49:[2,100],50:[2,100],52:[2,100],54:[2,100],56:[2,100],65:[2,100],67:[2,100]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,333]},{8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],17:[1,122],18:[1,158],19:[2,23],25:[1,301],26:[1,129],28:[1,156],29:[1,157],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{4:334,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,15],6:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],120:[1,336],121:[1,337],122:[1,338],123:[1,339]},{5:[2,244],6:[2,244],11:[2,244],12:[2,244],13:[2,244],14:[2,244],15:[2,244],19:[2,244]},{6:[2,250],19:[2,250]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,340]},{6:[2,216],19:[2,216]},{6:[2,218],19:[2,218]},{6:[2,220],19:[2,220]},{6:[2,222],19:[2,222]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26],123:[1,341]},{6:[2,224],19:[2,224]},{5:[2,36],6:[2,36],11:[2,36],12:[2,36],13:[2,36],14:[2,36],15:[2,36],19:[2,36],35:[2,36],36:[2,36]},{38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146]},{5:[2,37],6:[2,37],11:[2,37],12:[2,37],13:[2,37],14:[2,37],15:[2,37],19:[2,37],35:[2,37],36:[2,37]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26]},{5:[2,61],6:[2,61],11:[2,61],12:[2,61],13:[2,61],14:[2,61],15:[2,61],19:[2,61],35:[2,61],36:[2,61]},{48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{49:[1,182],52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{16:[1,197],42:195,47:191,49:[2,63],50:[1,196],52:[2,63],53:94,54:[1,198],56:[2,63],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,63],66:[1,102],67:[2,63]},{5:[2,72],6:[2,72],11:[2,72],12:[2,72],13:[2,72],14:[2,72],15:[2,72],19:[2,72],35:[2,72],36:[2,72]},{48:[1,130],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{5:[2,83],6:[2,83],11:[2,83],12:[2,83],13:[2,83],14:[2,83],15:[2,83],19:[2,83],35:[2,83],36:[2,83]},{48:[1,130],50:[1,134],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{56:[1,184],65:[1,185],67:[1,186]},{5:[2,94],6:[2,94],11:[2,94],12:[2,94],13:[2,94],14:[2,94],15:[2,94],19:[2,94],35:[2,94],36:[2,94]},{48:[1,130],50:[1,134],54:[1,147],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{65:[1,185],67:[1,186]},{5:[2,112],6:[2,112],11:[2,112],12:[2,112],13:[2,112],14:[2,112],15:[2,112],19:[2,112],35:[2,112],36:[2,112]},{48:[1,130],50:[1,134],54:[1,147],58:[1,149],66:[1,154],67:[1,153]},{67:[1,186]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,342]},{5:[2,68],6:[2,68],11:[2,68],12:[2,68],13:[2,68],14:[2,68],15:[2,68],19:[2,68],35:[2,68],36:[2,68],46:[2,68],49:[2,68],52:[2,68],56:[2,68],65:[2,68],67:[2,68]},{5:[2,48],6:[2,48],11:[2,48],12:[2,48],13:[2,48],14:[2,48],15:[2,48],19:[2,48],35:[2,48],36:[2,48]},{5:[2,41],6:[2,41],11:[2,41],12:[2,41],13:[2,41],14:[2,41],15:[2,41],19:[2,41],35:[2,41],36:[2,41]},{41:[1,137],54:[1,147],58:[1,149]},{41:[1,208]},{5:[2,79],6:[2,79],11:[2,79],12:[2,79],13:[2,79],14:[2,79],15:[2,79],19:[2,79],35:[2,79],36:[2,79],46:[2,79],48:[2,79],49:[2,79],52:[2,79],56:[2,79],65:[2,79],67:[2,79]},{5:[2,57],6:[2,57],11:[2,57],12:[2,57],13:[2,57],14:[2,57],15:[2,57],19:[2,57],35:[2,57],36:[2,57],38:[2,57],40:[2,57]},{5:[2,51],6:[2,51],11:[2,51],12:[2,51],13:[2,51],14:[2,51],15:[2,51],19:[2,51],35:[2,51],36:[2,51]},{5:[2,101],6:[2,101],11:[2,101],12:[2,101],13:[2,101],14:[2,101],15:[2,101],19:[2,101],35:[2,101],36:[2,101],38:[2,101],40:[2,101],41:[2,101],44:[2,101],46:[2,101],48:[2,101],49:[2,101],50:[2,101],52:[2,101],54:[2,101],56:[2,101],65:[2,101],67:[2,101]},{5:[2,90],6:[2,90],11:[2,90],12:[2,90],13:[2,90],14:[2,90],15:[2,90],19:[2,90],35:[2,90],36:[2,90],38:[2,90],40:[2,90],41:[2,90],44:[2,90],46:[2,90],48:[2,90],49:[2,90],50:[2,90],52:[2,90],56:[2,90],65:[2,90],67:[2,90]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,343]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,344]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,345]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,346]},{87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,347]},{5:[2,235],6:[2,235],11:[2,235],12:[2,235],13:[2,235],14:[2,235],15:[2,235],19:[2,235]},{6:[2,227],19:[2,227]},{6:[2,228],19:[2,228]},{6:[2,229],19:[2,229]},{6:[2,230],19:[2,230]},{5:[2,16],6:[2,16],11:[2,16],12:[2,16],13:[2,16],14:[2,16],15:[2,16],19:[2,16]},{6:[2,226],19:[2,226]},{5:[2,28],6:[2,28],11:[2,28],12:[2,28],13:[2,28],14:[2,28],15:[2,28],19:[2,28],126:[2,28],127:[2,28]},{5:[2,15],6:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15]},{5:[2,238],6:[2,238],11:[2,238],12:[2,238],13:[2,238],14:[2,238],15:[2,238],19:[2,238],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{5:[2,241],6:[2,241],11:[2,241],12:[2,241],13:[2,241],14:[2,241],15:[2,241],19:[2,241],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{5:[2,243],6:[2,243],11:[2,243],12:[2,243],13:[2,243],14:[2,243],15:[2,243],19:[2,243]},{11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]}],
defaultActions: {105:[2,2],112:[2,4],119:[2,6],120:[2,7],254:[2,1],263:[2,3],264:[2,5],277:[2,8]},
parseError: function parseError(str,hash){if(hash.recoverable){this.trace(str)}else{throw new Error(str)}},
parse: function parse(input) {
    var self = this, stack = [0], vstack = [null], lstack = [], table = this.table, yytext = '', yylineno = 0, yyleng = 0, recovering = 0, TERROR = 2, EOF = 1;
    var args = lstack.slice.call(arguments, 1);
    this.lexer.setInput(input);
    this.lexer.yy = this.yy;
    this.yy.lexer = this.lexer;
    this.yy.nParser = this;
    if (typeof this.lexer.yylloc == 'undefined') {
        this.lexer.yylloc = {};
    }
    var yyloc = this.lexer.yylloc;
    lstack.push(yyloc);
    var ranges = this.lexer.options && this.lexer.options.ranges;
    if (typeof this.yy.parseError === 'function') {
        this.parseError = this.yy.parseError;
    } else {
        this.parseError = Object.getPrototypeOf(this).parseError;
    }
    function popStack(n) {
        stack.length = stack.length - 2 * n;
        vstack.length = vstack.length - n;
        lstack.length = lstack.length - n;
    }
    function lex() {
        var token;
        token = self.lexer.lex() || EOF;
        if (typeof token !== 'number') {
            token = self.symbols_[token] || token;
        }
        return token;
    }
    var symbol, preErrorSymbol, state, action, a, r, yyval = {}, p, len, newState, expected;
    while (true) {
        state = stack[stack.length - 1];
        if (this.defaultActions[state]) {
            action = this.defaultActions[state];
        } else {
            if (symbol === null || typeof symbol == 'undefined') {
                symbol = lex();
            }
            action = table[state] && table[state][symbol];
        }
                    if (typeof action === 'undefined' || !action.length || !action[0]) {
                var errStr = '';
                expected = [];
                for (p in table[state]) {
                    if (this.terminals_[p] && p > TERROR) {
                        expected.push('\'' + this.terminals_[p] + '\'');
                    }
                }
                if (this.lexer.showPosition) {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ':\n' + this.lexer.showPosition() + '\nExpecting ' + expected.join(', ') + ', got \'' + (this.terminals_[symbol] || symbol) + '\'';
                } else {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ': Unexpected ' + (symbol == EOF ? 'end of input' : '\'' + (this.terminals_[symbol] || symbol) + '\'');
                }
                this.parseError(errStr, {
                    text: this.lexer.match,
                    token: this.terminals_[symbol] || symbol,
                    line: this.lexer.yylineno,
                    loc: yyloc,
                    expected: expected
                });
            }
        if (action[0] instanceof Array && action.length > 1) {
            throw new Error('Parse Error: multiple actions possible at state: ' + state + ', token: ' + symbol);
        }
        switch (action[0]) {
        case 1:
            stack.push(symbol);
            vstack.push(this.lexer.yytext);
            lstack.push(this.lexer.yylloc);
            stack.push(action[1]);
            symbol = null;
            if (!preErrorSymbol) {
                yyleng = this.lexer.yyleng;
                yytext = this.lexer.yytext;
                yylineno = this.lexer.yylineno;
                yyloc = this.lexer.yylloc;
                if (recovering > 0) {
                    recovering--;
                }
            } else {
                symbol = preErrorSymbol;
                preErrorSymbol = null;
            }
            break;
        case 2:
            len = this.productions_[action[1]][1];
            yyval.$ = vstack[vstack.length - len];
            yyval._$ = {
                first_line: lstack[lstack.length - (len || 1)].first_line,
                last_line: lstack[lstack.length - 1].last_line,
                first_column: lstack[lstack.length - (len || 1)].first_column,
                last_column: lstack[lstack.length - 1].last_column
            };
            if (ranges) {
                yyval._$.range = [
                    lstack[lstack.length - (len || 1)].range[0],
                    lstack[lstack.length - 1].range[1]
                ];
            }
            r = this.performAction.apply(yyval, [
                yytext,
                yyleng,
                yylineno,
                this.yy,
                action[1],
                vstack,
                lstack
            ].concat(args));
            if (typeof r !== 'undefined') {
                return r;
            }
            if (len) {
                stack = stack.slice(0, -1 * len * 2);
                vstack = vstack.slice(0, -1 * len);
                lstack = lstack.slice(0, -1 * len);
            }
            stack.push(this.productions_[action[1]][0]);
            vstack.push(yyval.$);
            lstack.push(yyval._$);
            newState = table[stack[stack.length - 2]][stack[stack.length - 1]];
            stack.push(newState);
            break;
        case 3:
            return true;
        }
    }
    return true;
}};
/* generated by jison-lex 0.2.1 */
var lexer = (function(){
var lexer = {

EOF:1,

parseError:function parseError(str,hash){if(this.yy.nParser){this.yy.nParser.parseError(str,hash)}else{throw new Error(str)}},

// resets the lexer, sets new input
setInput:function (input){this._input=input;this._more=this._backtrack=this.done=false;this.yylineno=this.yyleng=0;this.yytext=this.matched=this.match="";this.conditionStack=["INITIAL"];this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0};if(this.options.ranges){this.yylloc.range=[0,0]}this.offset=0;return this},

// consumes and returns one char from the input
input:function (){var ch=this._input[0];this.yytext+=ch;this.yyleng++;this.offset++;this.match+=ch;this.matched+=ch;var lines=ch.match(/(?:\r\n?|\n).*/g);if(lines){this.yylineno++;this.yylloc.last_line++}else{this.yylloc.last_column++}if(this.options.ranges){this.yylloc.range[1]++}this._input=this._input.slice(1);return ch},

// unshifts one char (or a string) into the input
unput:function (ch){var len=ch.length;var lines=ch.split(/(?:\r\n?|\n)/g);this._input=ch+this._input;this.yytext=this.yytext.substr(0,this.yytext.length-len-1);this.offset-=len;var oldLines=this.match.split(/(?:\r\n?|\n)/g);this.match=this.match.substr(0,this.match.length-1);this.matched=this.matched.substr(0,this.matched.length-1);if(lines.length-1){this.yylineno-=lines.length-1}var r=this.yylloc.range;this.yylloc={first_line:this.yylloc.first_line,last_line:this.yylineno+1,first_column:this.yylloc.first_column,last_column:lines?(lines.length===oldLines.length?this.yylloc.first_column:0)+oldLines[oldLines.length-lines.length].length-lines[0].length:this.yylloc.first_column-len};if(this.options.ranges){this.yylloc.range=[r[0],r[0]+this.yyleng-len]}this.yyleng=this.yytext.length;return this},

// When called from action, caches matched text and appends it on next action
more:function (){this._more=true;return this},

// When called from action, signals the lexer that this rule fails to match the input, so the next matching rule (regex) should be tested instead.
reject:function (){if(this.options.backtrack_lexer){this._backtrack=true}else{return this.parseError("Lexical error on line "+(this.yylineno+1)+". You can only invoke reject() in the lexer when the lexer is of the backtracking persuasion (options.backtrack_lexer = true).\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})}return this},

// retain first n characters of the match
less:function (n){this.unput(this.match.slice(n))},

// displays already matched input, i.e. for error messages
pastInput:function (){var past=this.matched.substr(0,this.matched.length-this.match.length);return(past.length>20?"...":"")+past.substr(-20).replace(/\n/g,"")},

// displays upcoming input, i.e. for error messages
upcomingInput:function (){var next=this.match;if(next.length<20){next+=this._input.substr(0,20-next.length)}return(next.substr(0,20)+(next.length>20?"...":"")).replace(/\n/g,"")},

// displays the character position where the lexing error occurred, i.e. for error messages
showPosition:function (){var pre=this.pastInput();var c=new Array(pre.length+1).join("-");return pre+this.upcomingInput()+"\n"+c+"^"},

// test the lexed token: return FALSE when not a match, otherwise return token
test_match:function (match,indexed_rule){var token,lines,backup;if(this.options.backtrack_lexer){backup={yylineno:this.yylineno,yylloc:{first_line:this.yylloc.first_line,last_line:this.last_line,first_column:this.yylloc.first_column,last_column:this.yylloc.last_column},yytext:this.yytext,match:this.match,matches:this.matches,matched:this.matched,yyleng:this.yyleng,offset:this.offset,_more:this._more,_input:this._input,yy:this.yy,conditionStack:this.conditionStack.slice(0),done:this.done};if(this.options.ranges){backup.yylloc.range=this.yylloc.range.slice(0)}}lines=match[0].match(/(?:\r\n?|\n).*/g);if(lines){this.yylineno+=lines.length}this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:lines?lines[lines.length-1].length-lines[lines.length-1].match(/\r?\n?/)[0].length:this.yylloc.last_column+match[0].length};this.yytext+=match[0];this.match+=match[0];this.matches=match;this.yyleng=this.yytext.length;if(this.options.ranges){this.yylloc.range=[this.offset,this.offset+=this.yyleng]}this._more=false;this._backtrack=false;this._input=this._input.slice(match[0].length);this.matched+=match[0];token=this.performAction.call(this,this.yy,this,indexed_rule,this.conditionStack[this.conditionStack.length-1]);if(this.done&&this._input){this.done=false}if(token){return token}else if(this._backtrack){for(var k in backup){this[k]=backup[k]}return false}return false},

// return next match in input
next:function (){if(this.done){return this.EOF}if(!this._input){this.done=true}var token,match,tempMatch,index;if(!this._more){this.yytext="";this.match=""}var rules=this._currentRules();for(var i=0;i<rules.length;i++){tempMatch=this._input.match(this.rules[rules[i]]);if(tempMatch&&(!match||tempMatch[0].length>match[0].length)){match=tempMatch;index=i;if(this.options.backtrack_lexer){token=this.test_match(tempMatch,rules[i]);if(token!==false){return token}else if(this._backtrack){match=false;continue}else{return false}}else if(!this.options.flex){break}}}if(match){token=this.test_match(match,rules[index]);if(token!==false){return token}return false}if(this._input===""){return this.EOF}else{return this.parseError("Lexical error on line "+(this.yylineno+1)+". Unrecognized text.\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})}},

// return next match that has a token
lex:function lex(){var r=this.next();if(r){return r}else{return this.lex()}},

// activates a new lexer condition state (pushes the new lexer condition state onto the condition stack)
begin:function begin(condition){this.conditionStack.push(condition)},

// pop the previously active lexer condition state off the condition stack
popState:function popState(){var n=this.conditionStack.length-1;if(n>0){return this.conditionStack.pop()}else{return this.conditionStack[0]}},

// produce the lexer rule set which is active for the currently active lexer condition state
_currentRules:function _currentRules(){if(this.conditionStack.length&&this.conditionStack[this.conditionStack.length-1]){return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules}else{return this.conditions["INITIAL"].rules}},

// return the currently active lexer condition state; when an index argument is provided it produces the N-th previous condition state, if available
topState:function topState(n){n=this.conditionStack.length-1-Math.abs(n||0);if(n>=0){return this.conditionStack[n]}else{return"INITIAL"}},

// alias for begin(condition)
pushState:function pushState(condition){this.begin(condition)},

// return the number of states currently on the stack
stateStackSize:function stateStackSize(){return this.conditionStack.length},
options: {"flex":true,"case-insensitive":true},
performAction: function anonymous(yy,yy_,$avoiding_name_collisions,YY_START
/**/) {

var YYSTATE=YY_START;
switch($avoiding_name_collisions) {
case 0:/* skip whitespace */
break;
case 1:return 28
break;
case 2:return 29
break;
case 3:return 20
break;
case 4:return 16
break;
case 5:return 63
break;
case 6:return 77
break;
case 7:return 77
break;
case 8:return 66
break;
case 9:return 61
break;
case 10:return 62 
break;
case 11:return 67
break;
case 12:return 65                                                                                                                                     
break;
case 13:return 60
break;
case 14:return 56
break;
case 15:return 58
break;
case 16:return 54
break;
case 17:return 50
break;
case 18:return 48
break;
case 19:return 41
break;
case 20:return 44
break;
case 21:return 38
break;
case 22:return 40
break;
case 23:return 82
break;
case 24:return 83
break;
case 25:return 80
break;
case 26:return 81
break;
case 27:return 78
break;
case 28:return 79
break;
case 29:return 84
break;
case 30:return 85
break;
case 31:return 86
break;
case 32:return 88
break;
case 33:return 91
break;
case 34:return 92
break;
case 35:return 93
break;
case 36:return 94
break;
case 37:return 95
break;
case 38:return 96
break;
case 39:return 97
break;
case 40:return 98
break;
case 41:return 99
break;
case 42:return 100
break;
case 43:return 101
break;
case 44:return 102
break;
case 45:return 89
break;
case 46:return 104
break;
case 47:return 103
break;
case 48:return 105
break;
case 49:return 106
break;
case 50:return 107
break;
case 51:return 108
break;
case 52:return 90
break;
case 53:return 111
break;
case 54:return 110
break;
case 55:return 109
break;
case 56:return 112
break;
case 57:return 113
break;
case 58:return 114
break;
case 59:return 115
break;
case 60:return 116
break;
case 61:return 117
break;
case 62:return 122
break;
case 63:return 123
break;
case 64:return 118
break;
case 65:return 120
break;
case 66:return 121
break;
case 67:return 127
break;
case 68:return 126
break;
case 69:return 124
break;
case 70:return 125
break;
case 71:return 35
break;
case 72:return 14
break;
case 73:return 13
break;
case 74:return 12
break;
case 75:return 11
break;
case 76:return 15
break;
case 77:return 17
break;
case 78:return 18
break;
case 79:return 19
break;
case 80:return 12
break;
case 81:return 36
break;
case 82:return 5
break;
case 83:return 25
break;
case 84:return 24
break;
case 85:return 27
break;
case 86:return 26
break;
case 87:return 6
break;
case 88:return 'INVALID'
break;
case 89:console.log(yy_.yytext);
break;
}
},
rules: [/^(?:\s+)/i,/^(?:[1-9][0-9]{0,1}(,[0-9]{2})*(,[0-9]{3}))/i,/^(?:[1-9][0-9]{0,2}(,[0-9]{3})*(,[0-9]{3}))/i,/^(?:[1-9][0-9]*(\s)[0-9]+(\s)?(\/|\\)(\s)?[0-9]+)/i,/^(?:([0-9]+(\.[0-9]+)?)|(\.[0-9]+))/i,/^(?:(twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(one|ones|two|three|four|five|six|seven|eight|nine))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|ten|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths|half|third|fourth|fifth|sixth|seventh|eighth|ninth|elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths|eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth|tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths|tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth|quarters|quarter|hundredths|hundredth|thousandths|thousandth))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|ten|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths|half|third|fourth|fifth|sixth|seventh|eighth|ninth|elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths|eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth|tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths|tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth|quarters|quarter|hundredths|hundredth|thousandths|thousandth))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine))/i,/^(?:(eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen))/i,/^(?:(twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety))/i,/^(?:(ones))/i,/^(?:(tens))/i,/^(?:(ten))/i,/^(?:(hundreds))/i,/^(?:(hundred))/i,/^(?:(thousands|thousand))/i,/^(?:(lakhs|lakh))/i,/^(?:(crores|crore))/i,/^(?:(millions))/i,/^(?:(million))/i,/^(?:(billions))/i,/^(?:(billion))/i,/^(?:(first|half|third|fourth|fifth|sixth|seventh|eighth|ninth))/i,/^(?:(firsts|halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths))/i,/^(?:(eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth))/i,/^(?:(elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths))/i,/^(?:(tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth))/i,/^(?:(tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths))/i,/^(?:(quarters|quarter))/i,/^(?:(hundredths|hundredth))/i,/^(?:(thousandths|thousandth))/i,/^(?:(metres|metre|m))/i,/^(?:(kilometres|kilometre|km))/i,/^(?:(millimetres|millimetre|mm))/i,/^(?:(centimetres|centimetre|cm))/i,/^(?:(litres|litre|l))/i,/^(?:(kilolitres|kilolitre|kl))/i,/^(?:(millilitres|millimetre|ml))/i,/^(?:(centilitres|centilitre|cl))/i,/^(?:(miles|mile|mi))/i,/^(?:(yards|yard|yd))/i,/^(?:(feet|foot|ft))/i,/^(?:(inches|inch|in))/i,/^(?:(grams|gram|g))/i,/^(?:(kilograms|kilogram|kg))/i,/^(?:(milligrams|milligram|mg))/i,/^(?:(centigrams|centigram|cg))/i,/^(?:(micrograms|microgram|mcg|μg))/i,/^(?:(pounds|pound|lb))/i,/^(?:(ounces|ounce))/i,/^(?:(tons|ton))/i,/^(?:(seconds|second|s))/i,/^(?:(milliseconds|millisecond|ms))/i,/^(?:(microseconds|microsecond|μs))/i,/^(?:(nanosecondsnanosecond|ns))/i,/^(?:(minutes|minute|min))/i,/^(?:(hours|hour|hr))/i,/^(?:(days|day))/i,/^(?:(weeks|week))/i,/^(?:(months|month))/i,/^(?:(years|year))/i,/^(?:(degrees|degree|°))/i,/^(?:(radians|radian|rad))/i,/^(?:(kelvin|degree kelvin|K))/i,/^(?:(degree celcius|celcius|centrigrade|°C))/i,/^(?:(fahrenheit|°F))/i,/^(?:(rupees|rs\.|rs|inr\.|inr))/i,/^(?:(rupee|re\.|re))/i,/^(?:(square|sq\.|sq))/i,/^(?:(cubic|cu\.|cu))/i,/^(?:AND|&)/i,/^(?:\/|\\|UPON|BY|OVER|OUT OF|PER)/i,/^(?:\*|x|×|TIMES|OF)/i,/^(?:-|MINUS)/i,/^(?:\+|PLUS)/i,/^(?:\^)/i,/^(?:!)/i,/^(?:\(|\[|\{)/i,/^(?:\)|\]|\})/i,/^(?:-)/i,/^(?:,)/i,/^(?:\.)/i,/^(?:PI)/i,/^(?:E)/i,/^(?:SQRT)/i,/^(?:%|PERCENT)/i,/^(?:$)/i,/^(?:.)/i,/^(?:.)/i],
conditions: {"INITIAL":{"rules":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89],"inclusive":true}}
};
return lexer;
})();
nParser.lexer = lexer;
function Parser () {
  this.yy = {};
}
Parser.prototype = nParser;nParser.Parser = Parser;
return new Parser;
})();


if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.nParser = nParser;
exports.Parser = nParser.Parser;
exports.parse = function () { return nParser.parse.apply(nParser, arguments); };
exports.main = function commonjsMain(args){if(!args[1]){console.log("Usage: "+args[0]+" FILE");process.exit(1)}var source=require("fs").readFileSync(require("path").normalize(args[1]),"utf8");return exports.nParser.parse(source)};
if (typeof module !== 'undefined' && require.main === module) {
  exports.main(process.argv.slice(1));
}
}var singleDigit = Array("zero","one","two","three","four","five","six","seven","eight","nine");
var doubleDigit = Array("eleven","twelve","thirteen","fourteen","fifteen","sixteen","seventeen","eighteen","nineteen");
var tenDigit = Array("ten", "twenty","thirty","forty","fifty","sixty","seventy","eighty", "ninety");
var fracSingularSingleDigit = Array("first","half","third","fourth","fifth","sixth","seventh","eighth","ninth");
var fracSingularDoubleDigit = Array("eleventh","twelfth","thirteenth","fourteenth","fifteenth","sixteenth","seventeenth","eighteenth","nineteenth");
var fracSingularTenDigit = Array("tenth","twentieth","thirtieth","fortieth","fiftieth","sixtieth","seventieth","eightieth", "ninetieth");
var fracPluralSingleDigit = Array("firsts","halves","thirds","fourths","fifths","sixths","sevenths","eighths","ninths");
var fracPluralDoubleDigit = Array("elevenths","twelfths","thirteenths","fourteenths","fifteenths","sixteenths","seventeenths","eighteenths","nineteenths");
var fracPluralTenDigit = Array("tenths","twentieths","thirtieths","fortieths","fiftieths","sixtieths","seventieths","eightieths", "ninetieths");

/* nParser generated by jison 0.4.13 */
/*
  Returns a Parser object of the following structure:

  Parser: {
    yy: {}
  }

  Parser.prototype: {
    yy: {},
    trace: function(),
    symbols_: {associative list: name ==> number},
    terminals_: {associative list: number ==> name},
    productions_: [...],
    performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate, $$, _$),
    table: [...],
    defaultActions: {...},
    parseError: function(str, hash),
    parse: function(input),

    lexer: {
        EOF: 1,
        parseError: function(str, hash),
        setInput: function(input),
        input: function(),
        unput: function(str),
        more: function(),
        less: function(n),
        pastInput: function(),
        upcomingInput: function(),
        showPosition: function(),
        test_match: function(regex_match_array, rule_index),
        next: function(),
        lex: function(),
        begin: function(condition),
        popState: function(),
        _currentRules: function(),
        topState: function(),
        pushState: function(condition),

        options: {
            ranges: boolean           (optional: true ==> token location info will include a .range[] member)
            flex: boolean             (optional: true ==> flex-like lexing behaviour where the rules are tested exhaustively to find the longest match)
            backtrack_lexer: boolean  (optional: true ==> lexer regexes are tested in order and for each matching regex the action code is invoked; the lexer terminates the scan when a token is returned by the action code)
        },

        performAction: function(yy, yy_, $avoiding_name_collisions, YY_START),
        rules: [...],
        conditions: {associative list: name ==> set},
    }
  }


  token location info (@$, _$, etc.): {
    first_line: n,
    last_line: n,
    first_column: n,
    last_column: n,
    range: [start_number, end_number]       (where the numbers are indexes into the input string, regular zero-based)
  }


  the parseError function receives a 'hash' object with these members for lexer and nParser errors: {
    text:        (matched text)
    token:       (the produced terminal token, if any)
    line:        (yylineno)
  }
  while nParser (grammar) errors will also provide these members, i.e. nParser errors deliver a superset of attributes: {
    loc:         (yylloc)
    expected:    (string describing the set of expected tokens)
    recoverable: (boolean: TRUE when the nParser has a error recovery rule available for this particular error)
  }
*/
var nParser = (function(){
var nParser = {trace: function trace(){},
yy: {},
symbols_: {"error":2,"expressions":3,"e":4,".":5,"EOF":6,"units_final":7,"c_units":8,"other_units_final":9,"money":10,"+":11,"-":12,"*":13,"/":14,"^":15,"NUMBER":16,"!":17,"(":18,")":19,"MFRAC":20,"w":21,"integer":22,"magnitude":23,"E":24,"PI":25,"PERCENT":26,"SQRT":27,"INUMBER":28,"FNUMBER":29,"crore":30,"crores":31,"billion":32,"billions":33,"fracWords":34,"AND":35,",":36,"millions":37,"BILLIONS":38,"million":39,"BILLION":40,"MILLIONS":41,"thousand":42,"thousands":43,"MILLION":44,"lakhs":45,"CRORES":46,"lakh":47,"CRORE":48,"LAKHS":49,"LAKH":50,"hundreds":51,"THOUSANDS":52,"hundred":53,"THOUSAND":54,"tens":55,"HUNDREDS":56,"ten":57,"HUNDRED":58,"one":59,"TEN":60,"DOUBLE_DIGIT":61,"TEN_DIGIT":62,"HYPHEN_DIGIT":63,"ones":64,"TENS":65,"SINGLE_DIGIT":66,"ONES":67,"oneths":68,"oneth":69,"tenth":70,"tenthsGroup":71,"tenDigith":72,"tenDigiths":73,"quarter":74,"hundredths":75,"thousandths":76,"FRAC_HYPHEN_DIGIT":77,"FRAC_SINGULAR_TEN_DIGIT":78,"FRAC_PLURAL_TEN_DIGIT":79,"FRAC_SINGULAR_DOUBLE_DIGIT":80,"FRAC_PLURAL_DOUBLE_DIGIT":81,"FRAC_SINGULAR_SINGLE_DIGIT":82,"FRAC_PLURAL_SINGLE_DIGIT":83,"QUARTER":84,"HUNDREDTH":85,"THOUSANDTH":86,"units":87,"METRE":88,"KILOGRAM":89,"SECOND":90,"KILOMETRE":91,"MILLIMETRE":92,"CENTIMETRE":93,"LITRE":94,"KILOLITRE":95,"MILLILITRE":96,"CENTILITRE":97,"MILE":98,"YARD":99,"FOOT":100,"INCH":101,"GRAM":102,"CENTIGRAM":103,"MILLIGRAM":104,"MICROGRAM":105,"POUND":106,"OUNCE":107,"TON":108,"NANOSECOND":109,"MICROSECOND":110,"MILLISECOND":111,"MINUTE":112,"HOUR":113,"DAY":114,"WEEK":115,"MONTH":116,"YEAR":117,"KELVIN":118,"other_units":119,"CELCIUS":120,"FAHRENHEIT":121,"DEGREE":122,"RADIAN":123,"SQUARE":124,"CUBIC":125,"RUPEE":126,"RUPEES":127,"$accept":0,"$end":1},
terminals_: {2:"error",5:".",6:"EOF",7:"units_final",11:"+",12:"-",13:"*",14:"/",15:"^",16:"NUMBER",17:"!",18:"(",19:")",20:"MFRAC",24:"E",25:"PI",26:"PERCENT",27:"SQRT",28:"INUMBER",29:"FNUMBER",35:"AND",36:",",38:"BILLIONS",40:"BILLION",41:"MILLIONS",44:"MILLION",46:"CRORES",48:"CRORE",49:"LAKHS",50:"LAKH",52:"THOUSANDS",54:"THOUSAND",56:"HUNDREDS",58:"HUNDRED",60:"TEN",61:"DOUBLE_DIGIT",62:"TEN_DIGIT",63:"HYPHEN_DIGIT",65:"TENS",66:"SINGLE_DIGIT",67:"ONES",77:"FRAC_HYPHEN_DIGIT",78:"FRAC_SINGULAR_TEN_DIGIT",79:"FRAC_PLURAL_TEN_DIGIT",80:"FRAC_SINGULAR_DOUBLE_DIGIT",81:"FRAC_PLURAL_DOUBLE_DIGIT",82:"FRAC_SINGULAR_SINGLE_DIGIT",83:"FRAC_PLURAL_SINGLE_DIGIT",84:"QUARTER",85:"HUNDREDTH",86:"THOUSANDTH",88:"METRE",89:"KILOGRAM",90:"SECOND",91:"KILOMETRE",92:"MILLIMETRE",93:"CENTIMETRE",94:"LITRE",95:"KILOLITRE",96:"MILLILITRE",97:"CENTILITRE",98:"MILE",99:"YARD",100:"FOOT",101:"INCH",102:"GRAM",103:"CENTIGRAM",104:"MILLIGRAM",105:"MICROGRAM",106:"POUND",107:"OUNCE",108:"TON",109:"NANOSECOND",110:"MICROSECOND",111:"MILLISECOND",112:"MINUTE",113:"HOUR",114:"DAY",115:"WEEK",116:"MONTH",117:"YEAR",118:"KELVIN",120:"CELCIUS",121:"FAHRENHEIT",122:"DEGREE",123:"RADIAN",124:"SQUARE",125:"CUBIC",126:"RUPEE",127:"RUPEES"},
productions_: [0,[3,3],[3,2],[3,3],[3,2],[3,3],[3,2],[3,2],[3,3],[4,3],[4,3],[4,3],[4,3],[4,3],[4,2],[4,3],[4,4],[4,1],[4,1],[4,1],[22,2],[22,2],[22,1],[23,1],[23,1],[23,1],[23,2],[23,2],[23,4],[23,1],[23,1],[21,1],[21,1],[21,1],[21,1],[21,1],[21,3],[21,3],[33,1],[33,2],[33,2],[33,3],[32,1],[32,1],[32,2],[32,2],[32,2],[32,3],[32,3],[37,2],[37,2],[37,3],[39,1],[39,2],[39,2],[39,2],[39,3],[39,3],[31,1],[31,2],[31,2],[31,3],[30,1],[30,1],[30,2],[30,2],[30,2],[30,3],[30,3],[45,1],[45,2],[45,2],[45,3],[47,1],[47,1],[47,2],[47,2],[47,2],[47,3],[47,3],[43,1],[43,2],[43,2],[43,3],[42,1],[42,1],[42,2],[42,2],[42,2],[42,3],[42,3],[51,1],[51,2],[51,2],[51,3],[53,1],[53,1],[53,2],[53,2],[53,2],[53,3],[53,3],[57,1],[57,1],[57,1],[57,1],[57,2],[57,1],[55,1],[55,2],[55,2],[55,2],[55,3],[59,1],[64,2],[64,2],[64,2],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[34,1],[72,1],[72,2],[72,2],[72,2],[72,2],[72,2],[73,1],[73,2],[73,2],[73,2],[73,2],[73,2],[70,1],[70,2],[70,2],[70,2],[70,2],[70,2],[71,1],[71,2],[71,2],[71,2],[71,2],[71,2],[69,1],[69,2],[69,2],[69,2],[69,2],[69,2],[68,1],[68,2],[68,2],[68,2],[68,2],[68,2],[74,1],[74,2],[74,2],[74,2],[74,2],[74,2],[74,2],[75,1],[75,2],[75,2],[75,2],[75,2],[75,2],[75,2],[76,1],[76,2],[76,2],[76,2],[76,2],[76,2],[76,2],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[87,1],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,2],[119,3],[119,3],[119,4],[119,4],[119,4],[119,4],[119,4],[8,1],[8,2],[8,2],[8,2],[8,4],[8,3],[8,3],[8,5],[8,3],[8,3],[8,5],[8,3],[8,5],[8,3],[8,2],[8,2],[8,3],[8,3],[9,1],[9,3],[10,2],[10,2],[10,2],[10,2]],
performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate /* action[1] */, $$ /* vstack */, _$ /* lstack */
/**/) {
/* this == yyval */

var $0 = $$.length - 1;
switch (yystate) {
case 1:return ($$[$0-2].toString());
break;
case 2:return ($$[$0-1].toString());
break;
case 3: 
    var input_arr = $$[$0-2];
    var return_value = $$[$0-2][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 4: 
    var input_arr = $$[$0-1];
    var return_value = $$[$0-1][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 5: 
    var input_arr = $$[$0-2];
    var return_value = $$[$0-2][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 6: 
    var input_arr = $$[$0-1];
    var return_value = $$[$0-1][0]+" ";
    var unit_array = ['m','kg','s','A','K','mol','cd','radian'];
    var firstEntry = 1;
    for(var i = 1; i < 9; i++)
    {
      if(input_arr[i] != 0)
      {
        if(firstEntry == 1)
        {
          return_value += unit_array[i-1]+"^("+input_arr[i]+")";
          firstEntry = 0;
        }
        else
          return_value += " * "+unit_array[i-1]+"^("+input_arr[i]+")";  
      }
    }
    return return_value.toString();
  
break;
case 7:return $$[$0-1];
break;
case 8:return $$[$0-2];
break;
case 9:this.$ = $$[$0-2]+$$[$0];
break;
case 10:this.$ = $$[$0-2]-$$[$0];
break;
case 11:this.$ = $$[$0-2]*$$[$0];
break;
case 12:this.$ = $$[$0-2]/$$[$0];
break;
case 13:this.$ = Math.pow($$[$0-2], $$[$0]);
break;
case 14:
           if($$[$0-1] % 1 != 0 || $$[$0-1] < 0) throw new Error("error");    
        this.$ = 1;   
        for(var i = $$[$0-1]; i>1; i--)   
        {   
          this.$ = this.$*i;    
        }   
    
        
    
break;
case 15:this.$ = $$[$0-1];
break;
case 16:this.$ = (-1)*Number($$[$0-1]);
break;
case 17:
     var temp = yyleng; 
     var val = 0;
     var val1 = 0;
     var val2 = 0;
     var pos = 0;
     for(; pos < temp; pos++)
      {
      if(yytext[pos] == " ")
        break;
      val = 10*val + Number(yytext[pos]);
      }
     pos++;
     for(; pos <temp; pos++)
     {
       if(yytext[pos] == "/" || yytext[pos] == "\\")
        break;
      val1 = 10*val1 + Number(yytext[pos]);
     }
     pos++;
     for(; pos <temp; pos++)
     {
      val2 = 10*val2 + Number(yytext[pos]);
     }
     this.$ = val + (val1/val2);
    
break;
case 18:this.$ = $$[$0];
break;
case 19:this.$ = $$[$0];
break;
case 20:this.$ = Number($$[$0]);
break;
case 21:this.$ = (-1)*Number($$[$0]);
break;
case 22:this.$ = $$[$0];
break;
case 23:this.$ = Number(yytext);
break;
case 24:this.$ = Math.E;
break;
case 25:this.$ = Math.PI;
break;
case 26:this.$ = Number($$[$0-1])*Math.PI;
break;
case 27:this.$ = Number($$[$0-1])*0.01;
break;
case 28:this.$ = Math.sqrt($$[$0-1]);
break;
case 29:
        var temp = yyleng;
        var val = 0;
        for(var i = 0; i < temp; i++)
        {
        if(yytext[i] == ",")
          continue;
        val = 10*val + Number(yytext[i]);
        }  
        this.$ = val;
       
break;
case 30:
        var temp = yyleng;
        var val = 0;
        for(var i = 0; i < temp; i++)
        {
        if(yytext[i] == ",")
          continue;
        val = 10*val + Number(yytext[i]);
        }  
        this.$ = val;
       
break;
case 31:this.$ = $$[$0];
break;
case 35:this.$ = $$[$0];
break;
case 36:if($$[$0-2] < $$[$0]) throw new Error("Invalid number in words.");
     this.$ = $$[$0-2] + $$[$0];
break;
case 37:if($$[$0-2] < $$[$0]) throw new Error("Invalid number in words.");
     this.$ = $$[$0-2] + $$[$0];
break;
case 39:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000;
       
break;
case 40:
         this.$ = $$[$0-1]*1000000000;
      
break;
case 41:
         this.$ = $$[$0-2]*1000000000 + $$[$0];
      
break;
case 43:this.$ = 100000000;
break;
case 44:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000;
      
break;
case 45:this.$ = $$[$0-1]*1000000000;
break;
case 46:this.$ = 1000000000 + $$[$0];
break;
case 47:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000000 + $$[$0];
      
break;
case 48:this.$ = $$[$0-2]*1000000000 + $$[$0];
break;
case 49:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000;
       
break;
case 50:
         this.$ = $$[$0-1]*1000000;
      
break;
case 51:
         this.$ = $$[$0-2]*1000000 + $$[$0];
      
break;
case 52:this.$ = 100000;
break;
case 53:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000;
      
break;
case 54:this.$ = $$[$0-1]*1000000;
break;
case 55:this.$ = 1000000 + $$[$0];
break;
case 56:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000000 + $$[$0];
      
break;
case 57:this.$ = $$[$0-2]*1000000 + $$[$0];
break;
case 58:this.$ = $$[$0];
break;
case 59:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000;
       
break;
case 60:
         this.$ = $$[$0-1]*10000000;
      
break;
case 61:
         this.$ = $$[$0-2]*10000000 + $$[$0];
      
break;
case 62:this.$ = $$[$0];
break;
case 63:this.$ = 10000000;
break;
case 64:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000;
      
break;
case 65:this.$ = $$[$0-1]*100000;
break;
case 66:this.$ = 10000000 + $$[$0];
break;
case 67:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*10000000 + $$[$0];
      
break;
case 68:this.$ = $$[$0-2]*10000000 + $$[$0];
break;
case 69:this.$ = $$[$0];
break;
case 70:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000;
       
break;
case 71:
         this.$ = $$[$0-1]*100000;
      
break;
case 72:
         this.$ = $$[$0-2]*100000 + $$[$0];
      
break;
case 73:this.$ = $$[$0];
break;
case 74:this.$ = 100000;
break;
case 75:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000;
      
break;
case 76:this.$ = $$[$0-1]*100000;
break;
case 77:this.$ = 100000 + $$[$0];
break;
case 78:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100000 + $$[$0];
      
break;
case 79:this.$ = $$[$0-2]*100000 + $$[$0];
break;
case 80:this.$ = $$[$0];
break;
case 81:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000;
       
break;
case 82:
         this.$ = $$[$0-1]*1000;
      
break;
case 83:
         this.$ = $$[$0-2]*1000 + $$[$0];
      
break;
case 84:this.$ = $$[$0];
break;
case 85:this.$ = 1000;
break;
case 86:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000;
       
break;
case 87:this.$ = $$[$0-1]*1000;
break;
case 88:this.$ = 1000 + $$[$0];
break;
case 89:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*1000 + $$[$0];
       
break;
case 90:this.$ = $$[$0-2]*1000+$$[$0];
break;
case 91:this.$ = $$[$0];
break;
case 92:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100;
       
break;
case 93:
         this.$ = $$[$0-1]*100;
      
break;
case 94:
         this.$ = $$[$0-2]*100 + $$[$0];
      
break;
case 95:this.$ = $$[$0];
break;
case 96:this.$ = 100;
break;
case 97:
       var num = Number($$[$0-1]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100;
       
break;
case 98:this.$ = $$[$0-1]*100;
break;
case 99:this.$ = 100+$$[$0];
break;
case 100:
       var num = Number($$[$0-2]);
       if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
       this.$ = num*100 + $$[$0];
       
break;
case 101:this.$ = $$[$0-2]*100+$$[$0];
break;
case 102:this.$ = $$[$0];
break;
case 103:this.$ = 10;
break;
case 104:
     var str = $$[$0];
     str = str.toLowerCase();
     this.$ = doubleDigit.indexOf(str)+11;
     
break;
case 105:var str = $$[$0];    
   str = str.toLowerCase();   
  this.$ = (tenDigit.indexOf(str)+1)*10;
     
break;
case 106:
    var str = $$[$0-1];   
  str = str.toLowerCase();    
  this.$ = (tenDigit.indexOf(str)+1)*10 + $$[$0];
     
break;
case 107:
    var str = $$[$0];
    str = str.toLowerCase();    
    var n = str.split("-");   
    this.$ = (tenDigit.indexOf(n[0])+1)*10 + singleDigit.indexOf(n[1]);
     
break;
case 108:this.$ = $$[$0];
break;
case 109:
      var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num*10;
   
break;
case 110:
      var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num*10;
   
break;
case 111:
     this.$ = $$[$0-1]*10;
    
break;
case 112:
     this.$ = $$[$0-2]*10 + $$[$0];
    
break;
case 113: var str = $$[$0];   
   str = str.toLowerCase();
     this.$ = singleDigit.indexOf(str);
break;
case 114:
     var num = Number($$[$0-1]);
      if(num%1 != 0 || num < 0) throw new Error("Invalid entry in words");
      this.$ = num;
    
break;
case 115: if($$[$0] != 1) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]);
break;
case 116: this.$ = $$[$0-1];       
break;
case 126:var str = $$[$0];
       str = str.toLowerCase();
       var n = str.split("-");
       var index = -1;
       var numerator = 1;
       var denominator = 1;
       var loopCount=0;
       while(index == -1)
       {
       loopCount++;
        index = singleDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  index;
         break;
         }
        index = doubleDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  index+11;
         break;
         }
         index = tenDigit.indexOf(n[0]);
        if(index != -1)
         {
         numerator =  (index+1)*10;
         break;
         }
         if(loopCount==20)
        break;
       }
       if(n[1] == "quarters"|"quarter")
       denominator = 4;
       else if(n[1] == "hundredths"|"hundredth")
       denominator = 100;
       else if(n[1] == "thousandths"|"thousandth")
       denominator = 1000;
       index = -1;
       loopCount=0;
       while(index == -1 && denominator == 1)
      {
        loopCount++;
        index = fracSingularSingleDigit.indexOf(n[1]);
        if(index != -1)
        {
          denominator = (index+1);
          break;
        }
        index = fracSingularDoubleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  index+11;
         break;
         }
         index = fracSingularTenDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  (index+1)*10;
         break;
         }
        index = fracPluralSingleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator = (index+1);
         break;
         }
        index = fracPluralDoubleDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  index+11;
         break;
         }
         index = fracPluralTenDigit.indexOf(n[1]);
        if(index != -1)
         {
         denominator =  (index+1)*10;
         break;
         }
         if(loopCount==20)
        break;
       } 
       //if(numerator >= denominator) throw new Error("Invalid entry in words.");       
       this.$ = Number(numerator/denominator);
      
break;
case 127:
      var str = $$[$0];   
    str = str.toLowerCase();
      this.$ = Number(0.1/(fracSingularTenDigit.indexOf(str)+1));
       
break;
case 128: var str = $$[$0];   
     str = str.toLowerCase();
       var index = fracSingularTenDigit.indexOf(str);
       if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
       this.$ = Number($$[$0-1]*0.1/(index+1));
       
break;
case 129: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/((index+1)*10));
     
break;
case 130: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100/((index+1)*10));
     
break;
case 131: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(1000/((index+1)*10));
     
break;
case 132: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100000/((index+1)*10));
     
break;
case 133:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(0.1/(fracPluralTenDigit.indexOf(str)+1)); 
break;
case 134: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.1/(index+1));
     
break;
case 135: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/((index+1)*10));
     
break;
case 136: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(100/((index+1)*10));
     
break;
case 137: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 138: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralTenDigit.indexOf(str);
     this.$ = Number(100000/((index+1)*10));
     
break;
case 139:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracSingularDoubleDigit.indexOf(str)+11)); 
break;
case 140: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 141: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 142: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularTenDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 143: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 144: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularDoubleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 145:var str = $$[$0];    
    str = str.toLowerCase();
     this.$ = Number(1/(fracPluralDoubleDigit.indexOf(str)+11)); 
break;
case 146: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 147: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+11));
     
break;
case 148: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 149: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 150: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralDoubleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 151:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracSingularSingleDigit.indexOf(str)+1)); 
break;
case 152: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 153: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 154: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 155: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 156: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracSingularSingleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 157:var str = $$[$0];    
   str = str.toLowerCase();
     this.$ = Number(1/(fracPluralSingleDigit.indexOf(str)+1)); 
break;
case 158: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 159: 
     var str = $$[$0];    
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/(index+1));
     
break;
case 160: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(100/(index+1));
     
break;
case 161: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(1000/(index+1));
     
break;
case 162: var str = $$[$0];   
   str = str.toLowerCase();
     var index = fracPluralSingleDigit.indexOf(str);
     this.$ = Number(100000/(index+1));
     
break;
case 163: this.$ = Number(1/4); 
break;
case 164: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/4);
     
break;
case 165:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]/4);
     
break;
case 166: 
     this.$ = Number(25);
     
break;
case 167: 
      this.$ = Number(250);
     
break;
case 168:  this.$ = Number(25000);
break;
case 169:  this.$ = Number(2500000);
break;
case 170: this.$ = Number(0.01); 
break;
case 171: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.01);
     
break;
case 172:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.01);
     
break;
case 173: 
     this.$ = Number(1);
     
break;
case 174: 
      this.$ = Number(10);
     
break;
case 175:  this.$ = Number(1000);
break;
case 176:  this.$ = Number(100000);
break;
case 177: this.$ = Number(0.001); 
break;
case 178: 
     if($$[$0-1]%1 != 0 || $$[$0-1] < 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.001);
     
break;
case 179:     
     if($$[$0-1] > 20 && $$[$0-1]%10 != 0) throw new Error("Invalid entry in words");
     this.$ = Number($$[$0-1]*0.001);
     
break;
case 180: 
     this.$ = Number(0.1);
     
break;
case 181: 
      this.$ = Number(1);
     
break;
case 182:  this.$ = Number(100);
break;
case 183:  this.$ = Number(10000);
break;
case 184:this.$ = [1, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 185:this.$ = [1, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 186:this.$ = [1, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 187:this.$ = [1e3, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 188:this.$ = [1e-3, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 189:this.$ = [1e-2, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 190:this.$ = [1e-3, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 191:this.$ = [1, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 192:this.$ = [1e-6, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 193:this.$ = [1e-5, 3, 0, 0, 0, 0, 0, 0, 0];
break;
case 194:this.$ = [1609.34, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 195:this.$ = [0.9144, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 196:this.$ = [0.3048, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 197:this.$ = [0.0254, 1, 0, 0, 0, 0, 0, 0, 0];
break;
case 198:this.$ = [1e-3, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 199:this.$ = [1e-5, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 200:this.$ = [1e-6, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 201:this.$ = [1e-9, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 202:this.$ = [0.45, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 203:this.$ = [0.028, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 204:this.$ = [1e3, 0, 1, 0, 0, 0, 0, 0, 0];
break;
case 205:this.$ = [1e-9, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 206:this.$ = [1e-6, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 207:this.$ = [1e-3, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 208:this.$ = [60, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 209:this.$ = [36e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 210:this.$ = [864e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 211:this.$ = [6048e2, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 212:this.$ = [2.63e+6, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 213:this.$ = [3.156e+7, 0, 0, 1, 0, 0, 0, 0, 0];
break;
case 214:this.$ = [1, 0, 0, 0, 0, 1, 0, 0, 0];
break;
case 215:
          var magnitude = (Number($$[$0-1])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 216:
          var magnitude = ((-1)*Number($$[$0-1])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 217:
          var magnitude = Number($$[$0-1]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 218:
          var magnitude = (-1)*Number($$[$0-2]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 219:
          this.$ = [(Number($$[$0-1])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 220:
          this.$ = [((-1)*Number($$[$0-1])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 221:
          this.$ = [Number($$[$0-1]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 222:
          this.$ = [(-1)*Number($$[$0-1]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 223:
          this.$ = [Number(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 224:
          this.$ = [(-1)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 225:
          this.$ = [(Math.PI)*Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 226:
          this.$ = [(-1)*(Math.PI)*Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 227:
          var magnitude = (Number($$[$0-2])-32)/1.8 + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 228:
          var magnitude = Number($$[$0-2]) + 273.15;
          this.$ = [magnitude, 0, 0, 0, 0, 1, 0, 0, 0];
        
break;
case 229:
          this.$ = [(Number($$[$0-2])/180)*(Math.PI) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 230:
          this.$ = [Number($$[$0-2]) , 0, 0, 0, 0, 0, 0, 0, 1];
        
break;
case 231:
      this.$ = $$[$0];
     
break;
case 232:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 233:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 234:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-1])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 235:
      var arr1 = $$[$0];
      arr1[0] = Number($$[$0-2])*Number(arr1[0]);
      this.$ = arr1;
     
break;
case 236:
      var arr1 = $$[$0-2];
      var arr2 = $$[$0];
      var res_arr = [(arr1[0]*arr2[0])];
      for(var i = 1; i < 9; i++)
      {
         res_arr[i] = arr1[i] + arr2[i];
      }
      this.$ = res_arr;
     
break;
case 237:
      var res_arr = $$[$0-2];
      var factor = Number($$[$0]);
      res_arr[0] = res_arr[0]*factor;
      this.$ = res_arr;
     
break;
case 238:
      var res_arr = $$[$0-4];
      var factor = Number($$[$0-1]);
      res_arr[0] = res_arr[0]*factor;
      this.$ = res_arr;
     
break;
case 239:
      var arr1 = $$[$0-2];
      var arr2 = $$[$0];
      var res_arr = [(arr1[0]/arr2[0])];
      for(var i = 1; i < 9; i++)
      {
         res_arr[i] = arr1[i] - arr2[i];
      }
      this.$ = res_arr;
    
break;
case 240:
      var res_arr = $$[$0-2];
      var factor = Number($$[$0]);
      if(factor == 0)
        throw new Error("Division by 0!");
      res_arr[0] = res_arr[0]/factor;
      this.$ = res_arr;
     
break;
case 241:
      var res_arr = $$[$0-4];
      var factor = Number($$[$0-1]);
      if(factor == 0)
        throw new Error("Division by 0!");
      res_arr[0] = res_arr[0]/factor;
      this.$ = res_arr;
     
break;
case 242:
      var arr1 = $$[$0-2];

      var power = Number($$[$0]);
      var res_arr = [Math.pow(arr1[0],power)];;
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
            res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
      console.log("fe"+res_arr);
     
break;
case 243:
      var arr1 = $$[$0-4];
      var power = Number($$[$0-1]);
      var res_arr = [Math.pow(arr1[0],power)];;
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
            res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
      console.log("fe"+res_arr);
     
break;
case 244:
      this.$ = $$[$0-1];
    
break;
case 245:
      var arr1 = $$[$0];
      var power = Number(2);
      var res_arr = [Math.pow(arr1[0],power)];
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
          res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
    
break;
case 246:
      var arr1 = $$[$0];
      var power = Number(3);
      var res_arr = [Math.pow(arr1[0],power)];
      for(var i = 1; i < 9; i++)
      {
        if(arr1[i]< 0)
          res_arr[i] = Math.abs(arr1[i])*power*(-1);
        else
          res_arr[i] = arr1[i]*power;
      }
      this.$ = res_arr;
    
break;
case 247:
         var arr1 = $$[$0-2];
         var arr2 = $$[$0];
         for(var i = 1; i < 9; i++)
         {
          if(arr1[i] != arr2[i])
            throw new Error("Addition cannot be performed on different units!");
         }
         this.$ = arr1;
         this.$[0] = arr1[0] + arr2[0];
       
break;
case 248:
       var arr1 = $$[$0-2];
       var arr2 = $$[$0];
       for(var i = 1; i < 9; i++)
       {
        if(arr1[i] != arr2[i])
          throw new Error("Subtraction cannot be performed on different units!");
       }
       this.$ = arr1;
       this.$[0] = arr1[0] - arr2[0];
     
break;
case 249:
            this.$ = $$[$0];
          
break;
case 250:
            this.$ = $$[$0-1];
          
break;
case 251:
          if(Number($$[$0]) == 1)
            this.$ = "Re. "+($$[$0]).toString();
          else
            throw new Error("");
          
break;
case 252:this.$ = "Rs. "+($$[$0]).toString();
break;
case 253:
          if(Number($$[$0-1]) == 1)
            this.$ = "Re. "+ ($$[$0-1]).toString();
          else
            throw new Error("");
          
break;
case 254:this.$ = "Rs. "+ ($$[$0-1]).toString();
break;
}
},
table: [{3:1,4:2,7:[1,3],8:4,9:5,10:6,11:[1,27],12:[1,9],16:[1,7],18:[1,8],20:[1,10],21:11,22:12,23:21,24:[1,60],25:[1,59],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],119:18,124:[1,16],125:[1,17],126:[1,19],127:[1,20]},{1:[3]},{5:[1,104],6:[1,105],11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110]},{5:[1,111]},{5:[1,113],6:[1,112],11:[1,117],12:[1,118],13:[1,114],14:[1,115],15:[1,116]},{6:[1,119]},{5:[1,121],6:[1,120]},{5:[2,23],6:[2,23],8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],17:[1,122],18:[1,158],19:[2,23],25:[1,128],26:[1,129],28:[1,156],29:[1,157],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],120:[1,124],121:[1,125],122:[1,126],123:[1,127],124:[1,16],125:[1,17],126:[2,23],127:[2,23]},{4:159,8:160,9:161,11:[1,27],12:[1,9],16:[1,7],18:[1,8],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,59],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],119:18,124:[1,16],125:[1,17]},{16:[1,165],18:[1,163],23:164,24:[1,60],25:[1,166],27:[1,61],28:[1,167],29:[1,168]},{5:[2,17],6:[2,17],11:[2,17],12:[2,17],13:[2,17],14:[2,17],15:[2,17],19:[2,17]},{5:[2,18],6:[2,18],11:[2,18],12:[2,18],13:[2,18],14:[2,18],15:[2,18],19:[2,18],35:[1,169],36:[1,170]},{5:[2,19],6:[2,19],11:[2,19],12:[2,19],13:[2,19],14:[2,19],15:[2,19],19:[2,19]},{5:[2,231],6:[2,231],11:[2,231],12:[2,231],13:[2,231],14:[2,231],15:[2,231],19:[2,231]},{5:[2,29],6:[2,29],8:171,11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],16:[1,155],18:[1,158],19:[2,29],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17],126:[2,29],127:[2,29]},{5:[2,30],6:[2,30],8:172,11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],16:[1,155],18:[1,158],19:[2,30],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17],126:[2,30],127:[2,30]},{8:173,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:174,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{6:[2,249],19:[2,249]},{16:[1,176],23:175,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{16:[1,176],23:178,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,22],6:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],126:[1,179],127:[1,180]},{5:[2,31],6:[2,31],11:[2,31],12:[2,31],13:[2,31],14:[2,31],15:[2,31],19:[2,31],35:[2,31],36:[2,31],46:[1,181],49:[1,182],52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{5:[2,32],6:[2,32],11:[2,32],12:[2,32],13:[2,32],14:[2,32],15:[2,32],19:[2,32],35:[2,32],36:[2,32]},{5:[2,33],6:[2,33],11:[2,33],12:[2,33],13:[2,33],14:[2,33],15:[2,33],19:[2,33],35:[2,33],36:[2,33]},{5:[2,34],6:[2,34],11:[2,34],12:[2,34],13:[2,34],14:[2,34],15:[2,34],19:[2,34],35:[2,34],36:[2,34]},{5:[2,35],6:[2,35],11:[2,35],12:[2,35],13:[2,35],14:[2,35],15:[2,35],19:[2,35],35:[2,35],36:[2,35]},{16:[1,176],23:187,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,184],6:[2,184],11:[2,184],12:[2,184],13:[2,184],14:[2,184],15:[2,184],19:[2,184]},{5:[2,185],6:[2,185],11:[2,185],12:[2,185],13:[2,185],14:[2,185],15:[2,185],19:[2,185]},{5:[2,186],6:[2,186],11:[2,186],12:[2,186],13:[2,186],14:[2,186],15:[2,186],19:[2,186]},{5:[2,187],6:[2,187],11:[2,187],12:[2,187],13:[2,187],14:[2,187],15:[2,187],19:[2,187]},{5:[2,188],6:[2,188],11:[2,188],12:[2,188],13:[2,188],14:[2,188],15:[2,188],19:[2,188]},{5:[2,189],6:[2,189],11:[2,189],12:[2,189],13:[2,189],14:[2,189],15:[2,189],19:[2,189]},{5:[2,190],6:[2,190],11:[2,190],12:[2,190],13:[2,190],14:[2,190],15:[2,190],19:[2,190]},{5:[2,191],6:[2,191],11:[2,191],12:[2,191],13:[2,191],14:[2,191],15:[2,191],19:[2,191]},{5:[2,192],6:[2,192],11:[2,192],12:[2,192],13:[2,192],14:[2,192],15:[2,192],19:[2,192]},{5:[2,193],6:[2,193],11:[2,193],12:[2,193],13:[2,193],14:[2,193],15:[2,193],19:[2,193]},{5:[2,194],6:[2,194],11:[2,194],12:[2,194],13:[2,194],14:[2,194],15:[2,194],19:[2,194]},{5:[2,195],6:[2,195],11:[2,195],12:[2,195],13:[2,195],14:[2,195],15:[2,195],19:[2,195]},{5:[2,196],6:[2,196],11:[2,196],12:[2,196],13:[2,196],14:[2,196],15:[2,196],19:[2,196]},{5:[2,197],6:[2,197],11:[2,197],12:[2,197],13:[2,197],14:[2,197],15:[2,197],19:[2,197]},{5:[2,198],6:[2,198],11:[2,198],12:[2,198],13:[2,198],14:[2,198],15:[2,198],19:[2,198]},{5:[2,199],6:[2,199],11:[2,199],12:[2,199],13:[2,199],14:[2,199],15:[2,199],19:[2,199]},{5:[2,200],6:[2,200],11:[2,200],12:[2,200],13:[2,200],14:[2,200],15:[2,200],19:[2,200]},{5:[2,201],6:[2,201],11:[2,201],12:[2,201],13:[2,201],14:[2,201],15:[2,201],19:[2,201]},{5:[2,202],6:[2,202],11:[2,202],12:[2,202],13:[2,202],14:[2,202],15:[2,202],19:[2,202]},{5:[2,203],6:[2,203],11:[2,203],12:[2,203],13:[2,203],14:[2,203],15:[2,203],19:[2,203]},{5:[2,204],6:[2,204],11:[2,204],12:[2,204],13:[2,204],14:[2,204],15:[2,204],19:[2,204]},{5:[2,205],6:[2,205],11:[2,205],12:[2,205],13:[2,205],14:[2,205],15:[2,205],19:[2,205]},{5:[2,206],6:[2,206],11:[2,206],12:[2,206],13:[2,206],14:[2,206],15:[2,206],19:[2,206]},{5:[2,207],6:[2,207],11:[2,207],12:[2,207],13:[2,207],14:[2,207],15:[2,207],19:[2,207]},{5:[2,208],6:[2,208],11:[2,208],12:[2,208],13:[2,208],14:[2,208],15:[2,208],19:[2,208]},{5:[2,209],6:[2,209],11:[2,209],12:[2,209],13:[2,209],14:[2,209],15:[2,209],19:[2,209]},{5:[2,210],6:[2,210],11:[2,210],12:[2,210],13:[2,210],14:[2,210],15:[2,210],19:[2,210]},{5:[2,211],6:[2,211],11:[2,211],12:[2,211],13:[2,211],14:[2,211],15:[2,211],19:[2,211]},{5:[2,212],6:[2,212],11:[2,212],12:[2,212],13:[2,212],14:[2,212],15:[2,212],19:[2,212]},{5:[2,213],6:[2,213],11:[2,213],12:[2,213],13:[2,213],14:[2,213],15:[2,213],19:[2,213]},{5:[2,214],6:[2,214],11:[2,214],12:[2,214],13:[2,214],14:[2,214],15:[2,214],19:[2,214]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25],123:[1,188],126:[2,25],127:[2,25]},{5:[2,24],6:[2,24],11:[2,24],12:[2,24],13:[2,24],14:[2,24],15:[2,24],19:[2,24],126:[2,24],127:[2,24]},{18:[1,189]},{5:[2,62],6:[2,62],11:[2,62],12:[2,62],13:[2,62],14:[2,62],15:[2,62],19:[2,62],35:[2,62],36:[2,62],46:[2,62],48:[1,190],49:[2,62],52:[2,62],56:[2,62],65:[2,62],67:[2,62]},{5:[2,63],6:[2,63],11:[2,63],12:[2,63],13:[2,63],14:[2,63],15:[2,63],16:[1,197],19:[2,63],35:[2,63],36:[2,63],42:195,46:[2,63],47:191,49:[2,63],50:[1,196],52:[2,63],53:94,54:[1,198],56:[2,63],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,63],66:[1,102],67:[2,63],84:[1,192],85:[1,193],86:[1,194]},{5:[2,58],6:[2,58],11:[2,58],12:[2,58],13:[2,58],14:[2,58],15:[2,58],19:[2,58],35:[2,58],36:[2,58]},{5:[2,42],6:[2,42],11:[2,42],12:[2,42],13:[2,42],14:[2,42],15:[2,42],19:[2,42],35:[2,42],36:[2,42],38:[1,202],40:[1,201]},{5:[2,43],6:[2,43],11:[2,43],12:[2,43],13:[2,43],14:[2,43],15:[2,43],16:[1,204],19:[2,43],35:[2,43],36:[2,43],39:203,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,38],6:[2,38],11:[2,38],12:[2,38],13:[2,38],14:[2,38],15:[2,38],19:[2,38],35:[2,38],36:[2,38]},{5:[2,117],6:[2,117],11:[2,117],12:[2,117],13:[2,117],14:[2,117],15:[2,117],19:[2,117],35:[2,117],36:[2,117]},{5:[2,118],6:[2,118],11:[2,118],12:[2,118],13:[2,118],14:[2,118],15:[2,118],19:[2,118],35:[2,118],36:[2,118]},{5:[2,119],6:[2,119],11:[2,119],12:[2,119],13:[2,119],14:[2,119],15:[2,119],19:[2,119],35:[2,119],36:[2,119]},{5:[2,120],6:[2,120],11:[2,120],12:[2,120],13:[2,120],14:[2,120],15:[2,120],19:[2,120],35:[2,120],36:[2,120]},{5:[2,121],6:[2,121],11:[2,121],12:[2,121],13:[2,121],14:[2,121],15:[2,121],19:[2,121],35:[2,121],36:[2,121]},{5:[2,122],6:[2,122],11:[2,122],12:[2,122],13:[2,122],14:[2,122],15:[2,122],19:[2,122],35:[2,122],36:[2,122]},{5:[2,123],6:[2,123],11:[2,123],12:[2,123],13:[2,123],14:[2,123],15:[2,123],19:[2,123],35:[2,123],36:[2,123]},{5:[2,124],6:[2,124],11:[2,124],12:[2,124],13:[2,124],14:[2,124],15:[2,124],19:[2,124],35:[2,124],36:[2,124]},{5:[2,125],6:[2,125],11:[2,125],12:[2,125],13:[2,125],14:[2,125],15:[2,125],19:[2,125],35:[2,125],36:[2,125]},{5:[2,126],6:[2,126],11:[2,126],12:[2,126],13:[2,126],14:[2,126],15:[2,126],19:[2,126],35:[2,126],36:[2,126]},{5:[2,73],6:[2,73],11:[2,73],12:[2,73],13:[2,73],14:[2,73],15:[2,73],19:[2,73],35:[2,73],36:[2,73],41:[1,208],44:[1,207],46:[2,73],48:[2,73],49:[2,73],50:[1,206],52:[2,73],56:[2,73],65:[2,73],67:[2,73]},{5:[2,74],6:[2,74],11:[2,74],12:[2,74],13:[2,74],14:[2,74],15:[2,74],16:[1,219],19:[2,74],35:[2,74],36:[2,74],42:209,46:[2,74],48:[2,74],49:[2,74],52:[2,74],53:94,54:[1,198],56:[2,74],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,74],66:[1,102],67:[2,74],78:[1,214],79:[1,215],80:[1,212],81:[1,213],82:[1,211],83:[1,210],84:[1,216],85:[1,217],86:[1,218]},{5:[2,69],6:[2,69],11:[2,69],12:[2,69],13:[2,69],14:[2,69],15:[2,69],19:[2,69],35:[2,69],36:[2,69]},{5:[2,52],6:[2,52],11:[2,52],12:[2,52],13:[2,52],14:[2,52],15:[2,52],16:[1,219],19:[2,52],35:[2,52],36:[2,52],38:[2,52],40:[2,52],42:220,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,157],6:[2,157],11:[2,157],12:[2,157],13:[2,157],14:[2,157],15:[2,157],19:[2,157],35:[2,157],36:[2,157]},{5:[2,95],6:[2,95],11:[2,95],12:[2,95],13:[2,95],14:[2,95],15:[2,95],19:[2,95],35:[2,95],36:[2,95],41:[2,95],44:[2,95],46:[2,95],48:[2,95],49:[2,95],50:[2,95],52:[2,95],54:[2,95],56:[2,95],58:[1,230],65:[2,95],67:[2,95],78:[1,225],79:[1,226],80:[1,223],81:[1,224],82:[1,222],83:[1,221],84:[1,227],85:[1,228],86:[1,229]},{5:[2,96],6:[2,96],11:[2,96],12:[2,96],13:[2,96],14:[2,96],15:[2,96],19:[2,96],35:[2,96],36:[2,96],41:[2,96],44:[2,96],46:[2,96],48:[2,96],49:[2,96],50:[2,96],52:[2,96],54:[2,96],56:[2,96],57:240,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,96],66:[1,102],67:[2,96],78:[1,235],79:[1,236],80:[1,233],81:[1,234],82:[1,232],83:[1,231],84:[1,237],85:[1,238],86:[1,239]},{5:[2,85],6:[2,85],11:[2,85],12:[2,85],13:[2,85],14:[2,85],15:[2,85],16:[1,251],19:[2,85],35:[2,85],36:[2,85],41:[2,85],44:[2,85],46:[2,85],48:[2,85],49:[2,85],50:[2,85],52:[2,85],53:250,56:[2,85],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,85],66:[1,102],67:[2,85],78:[1,245],79:[1,246],80:[1,243],81:[1,244],82:[1,242],83:[1,241],84:[1,247],85:[1,248],86:[1,249]},{5:[2,151],6:[2,151],11:[2,151],12:[2,151],13:[2,151],14:[2,151],15:[2,151],19:[2,151],35:[2,151],36:[2,151]},{5:[2,139],6:[2,139],11:[2,139],12:[2,139],13:[2,139],14:[2,139],15:[2,139],19:[2,139],35:[2,139],36:[2,139]},{5:[2,145],6:[2,145],11:[2,145],12:[2,145],13:[2,145],14:[2,145],15:[2,145],19:[2,145],35:[2,145],36:[2,145]},{5:[2,127],6:[2,127],11:[2,127],12:[2,127],13:[2,127],14:[2,127],15:[2,127],19:[2,127],35:[2,127],36:[2,127]},{5:[2,133],6:[2,133],11:[2,133],12:[2,133],13:[2,133],14:[2,133],15:[2,133],19:[2,133],35:[2,133],36:[2,133]},{5:[2,163],6:[2,163],11:[2,163],12:[2,163],13:[2,163],14:[2,163],15:[2,163],19:[2,163],35:[2,163],36:[2,163]},{5:[2,170],6:[2,170],11:[2,170],12:[2,170],13:[2,170],14:[2,170],15:[2,170],19:[2,170],35:[2,170],36:[2,170]},{5:[2,177],6:[2,177],11:[2,177],12:[2,177],13:[2,177],14:[2,177],15:[2,177],19:[2,177],35:[2,177],36:[2,177]},{5:[2,84],6:[2,84],11:[2,84],12:[2,84],13:[2,84],14:[2,84],15:[2,84],19:[2,84],35:[2,84],36:[2,84],38:[2,84],40:[2,84],41:[2,84],44:[2,84],46:[2,84],48:[2,84],49:[2,84],50:[2,84],52:[2,84],54:[1,252],56:[2,84],65:[2,84],67:[2,84]},{5:[2,80],6:[2,80],11:[2,80],12:[2,80],13:[2,80],14:[2,80],15:[2,80],19:[2,80],35:[2,80],36:[2,80]},{5:[2,102],6:[2,102],11:[2,102],12:[2,102],13:[2,102],14:[2,102],15:[2,102],19:[2,102],35:[2,102],36:[2,102],38:[2,102],40:[2,102],41:[2,102],44:[2,102],46:[2,102],48:[2,102],49:[2,102],50:[2,102],52:[2,102],54:[2,102],56:[2,102],58:[2,102],65:[2,102],67:[2,102],78:[2,102],79:[2,102],80:[2,102],81:[2,102],82:[2,102],83:[2,102],84:[2,102],85:[2,102],86:[2,102]},{5:[2,103],6:[2,103],11:[2,103],12:[2,103],13:[2,103],14:[2,103],15:[2,103],19:[2,103],35:[2,103],36:[2,103],38:[2,103],40:[2,103],41:[2,103],44:[2,103],46:[2,103],48:[2,103],49:[2,103],50:[2,103],52:[2,103],54:[2,103],56:[2,103],58:[2,103],65:[2,103],67:[2,103],78:[2,103],79:[2,103],80:[2,103],81:[2,103],82:[2,103],83:[2,103],84:[2,103],85:[2,103],86:[2,103]},{5:[2,104],6:[2,104],11:[2,104],12:[2,104],13:[2,104],14:[2,104],15:[2,104],19:[2,104],35:[2,104],36:[2,104],38:[2,104],40:[2,104],41:[2,104],44:[2,104],46:[2,104],48:[2,104],49:[2,104],50:[2,104],52:[2,104],54:[2,104],56:[2,104],58:[2,104],65:[2,104],67:[2,104],78:[2,104],79:[2,104],80:[2,104],81:[2,104],82:[2,104],83:[2,104],84:[2,104],85:[2,104],86:[2,104]},{5:[2,105],6:[2,105],11:[2,105],12:[2,105],13:[2,105],14:[2,105],15:[2,105],19:[2,105],35:[2,105],36:[2,105],38:[2,105],40:[2,105],41:[2,105],44:[2,105],46:[2,105],48:[2,105],49:[2,105],50:[2,105],52:[2,105],54:[2,105],56:[2,105],58:[2,105],59:253,65:[2,105],66:[1,102],67:[2,105],78:[2,105],79:[2,105],80:[2,105],81:[2,105],82:[2,105],83:[2,105],84:[2,105],85:[2,105],86:[2,105]},{5:[2,107],6:[2,107],11:[2,107],12:[2,107],13:[2,107],14:[2,107],15:[2,107],19:[2,107],35:[2,107],36:[2,107],38:[2,107],40:[2,107],41:[2,107],44:[2,107],46:[2,107],48:[2,107],49:[2,107],50:[2,107],52:[2,107],54:[2,107],56:[2,107],58:[2,107],65:[2,107],67:[2,107],78:[2,107],79:[2,107],80:[2,107],81:[2,107],82:[2,107],83:[2,107],84:[2,107],85:[2,107],86:[2,107]},{5:[2,91],6:[2,91],11:[2,91],12:[2,91],13:[2,91],14:[2,91],15:[2,91],19:[2,91],35:[2,91],36:[2,91]},{5:[2,113],6:[2,113],11:[2,113],12:[2,113],13:[2,113],14:[2,113],15:[2,113],19:[2,113],35:[2,113],36:[2,113],38:[2,113],40:[2,113],41:[2,113],44:[2,113],46:[2,113],48:[2,113],49:[2,113],50:[2,113],52:[2,113],54:[2,113],56:[2,113],58:[2,113],65:[2,113],67:[2,113],78:[2,113],79:[2,113],80:[2,113],81:[2,113],82:[2,113],83:[2,113],84:[2,113],85:[2,113],86:[2,113]},{5:[2,108],6:[2,108],11:[2,108],12:[2,108],13:[2,108],14:[2,108],15:[2,108],19:[2,108],35:[2,108],36:[2,108]},{6:[1,254]},{1:[2,2]},{4:255,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:259,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:260,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:261,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{4:262,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{6:[1,263]},{1:[2,4]},{6:[1,264]},{8:265,11:[1,27],12:[1,269],16:[1,268],18:[1,267],22:266,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:270,11:[1,27],12:[1,269],16:[1,268],18:[1,272],22:271,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{11:[1,27],12:[1,269],16:[1,176],18:[1,274],22:273,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{8:275,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:276,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{1:[2,6]},{1:[2,7]},{6:[1,277]},{5:[2,14],6:[2,14],11:[2,14],12:[2,14],13:[2,14],14:[2,14],15:[2,14],19:[2,14]},{5:[2,232],6:[2,232],11:[2,232],12:[2,232],13:[2,232],14:[2,232],15:[1,116],19:[2,232]},{6:[2,215],19:[2,215]},{6:[2,217],19:[2,217]},{6:[2,219],19:[2,219]},{6:[2,221],19:[2,221]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26],123:[1,278],126:[2,26],127:[2,26]},{5:[2,27],6:[2,27],11:[2,27],12:[2,27],13:[2,27],14:[2,27],15:[2,27],19:[2,27],126:[2,27],127:[2,27]},{5:[2,64],6:[2,64],11:[2,64],12:[2,64],13:[2,64],14:[2,64],15:[2,64],16:[1,197],19:[2,64],35:[2,64],36:[2,64],42:195,46:[2,64],47:279,49:[2,64],50:[1,196],52:[2,64],53:94,54:[1,198],56:[2,64],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,64],66:[1,102],67:[2,64]},{5:[2,59],6:[2,59],11:[2,59],12:[2,59],13:[2,59],14:[2,59],15:[2,59],19:[2,59],35:[2,59],36:[2,59]},{5:[2,44],6:[2,44],11:[2,44],12:[2,44],13:[2,44],14:[2,44],15:[2,44],16:[1,204],19:[2,44],35:[2,44],36:[2,44],39:280,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,39],6:[2,39],11:[2,39],12:[2,39],13:[2,39],14:[2,39],15:[2,39],19:[2,39],35:[2,39],36:[2,39]},{5:[2,75],6:[2,75],11:[2,75],12:[2,75],13:[2,75],14:[2,75],15:[2,75],16:[1,219],19:[2,75],35:[2,75],36:[2,75],42:281,46:[2,75],48:[2,75],49:[2,75],52:[2,75],53:94,54:[1,198],56:[2,75],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,75],66:[1,102],67:[2,75]},{5:[2,70],6:[2,70],11:[2,70],12:[2,70],13:[2,70],14:[2,70],15:[2,70],19:[2,70],35:[2,70],36:[2,70]},{5:[2,53],6:[2,53],11:[2,53],12:[2,53],13:[2,53],14:[2,53],15:[2,53],16:[1,219],19:[2,53],35:[2,53],36:[2,53],38:[2,53],40:[2,53],42:282,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,49],6:[2,49],11:[2,49],12:[2,49],13:[2,49],14:[2,49],15:[2,49],19:[2,49],35:[2,49],36:[2,49]},{5:[2,158],6:[2,158],11:[2,158],12:[2,158],13:[2,158],14:[2,158],15:[2,158],19:[2,158],35:[2,158],36:[2,158]},{5:[2,152],6:[2,152],11:[2,152],12:[2,152],13:[2,152],14:[2,152],15:[2,152],19:[2,152],35:[2,152],36:[2,152]},{5:[2,140],6:[2,140],11:[2,140],12:[2,140],13:[2,140],14:[2,140],15:[2,140],19:[2,140],35:[2,140],36:[2,140]},{5:[2,146],6:[2,146],11:[2,146],12:[2,146],13:[2,146],14:[2,146],15:[2,146],19:[2,146],35:[2,146],36:[2,146]},{5:[2,128],6:[2,128],11:[2,128],12:[2,128],13:[2,128],14:[2,128],15:[2,128],19:[2,128],35:[2,128],36:[2,128]},{5:[2,134],6:[2,134],11:[2,134],12:[2,134],13:[2,134],14:[2,134],15:[2,134],19:[2,134],35:[2,134],36:[2,134]},{5:[2,164],6:[2,164],11:[2,164],12:[2,164],13:[2,164],14:[2,164],15:[2,164],19:[2,164],35:[2,164],36:[2,164]},{5:[2,171],6:[2,171],11:[2,171],12:[2,171],13:[2,171],14:[2,171],15:[2,171],19:[2,171],35:[2,171],36:[2,171]},{5:[2,178],6:[2,178],11:[2,178],12:[2,178],13:[2,178],14:[2,178],15:[2,178],19:[2,178],35:[2,178],36:[2,178]},{5:[2,86],6:[2,86],11:[2,86],12:[2,86],13:[2,86],14:[2,86],15:[2,86],16:[1,251],19:[2,86],35:[2,86],36:[2,86],38:[2,86],40:[2,86],41:[2,86],44:[2,86],46:[2,86],48:[2,86],49:[2,86],50:[2,86],52:[2,86],53:283,56:[2,86],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,86],66:[1,102],67:[2,86]},{5:[2,81],6:[2,81],11:[2,81],12:[2,81],13:[2,81],14:[2,81],15:[2,81],19:[2,81],35:[2,81],36:[2,81]},{5:[2,97],6:[2,97],11:[2,97],12:[2,97],13:[2,97],14:[2,97],15:[2,97],19:[2,97],35:[2,97],36:[2,97],38:[2,97],40:[2,97],41:[2,97],44:[2,97],46:[2,97],48:[2,97],49:[2,97],50:[2,97],52:[2,97],54:[2,97],56:[2,97],57:284,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,97],66:[1,102],67:[2,97]},{5:[2,92],6:[2,92],11:[2,92],12:[2,92],13:[2,92],14:[2,92],15:[2,92],19:[2,92],35:[2,92],36:[2,92]},{5:[2,109],6:[2,109],11:[2,109],12:[2,109],13:[2,109],14:[2,109],15:[2,109],19:[2,109],35:[2,109],36:[2,109]},{5:[2,110],6:[2,110],11:[2,110],12:[2,110],13:[2,110],14:[2,110],15:[2,110],19:[2,110],35:[2,110],36:[2,110]},{5:[2,114],6:[2,114],11:[2,114],12:[2,114],13:[2,114],14:[2,114],15:[2,114],19:[2,114],35:[2,114],36:[2,114]},{5:[2,115],6:[2,115],11:[2,115],12:[2,115],13:[2,115],14:[2,115],15:[2,115],19:[2,115],35:[2,115],36:[2,115]},{8:123,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:171,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{8:172,16:[1,155],18:[1,158],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{4:285,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,288]},{11:[1,117],12:[1,118],13:[1,114],14:[1,115],15:[1,116],19:[1,289]},{19:[1,290]},{5:[2,22],6:[2,22],11:[2,22],12:[2,22],13:[2,22],14:[2,22],15:[2,22],19:[2,22]},{4:291,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,21],6:[2,21],11:[2,21],12:[2,21],13:[2,21],14:[2,21],15:[2,21],19:[2,21]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],19:[2,23],25:[1,296],26:[1,129],120:[1,292],121:[1,293],122:[1,294],123:[1,295]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25],123:[1,297]},{5:[2,29],6:[2,29],11:[2,29],12:[2,29],13:[2,29],14:[2,29],15:[2,29],19:[2,29]},{5:[2,30],6:[2,30],11:[2,30],12:[2,30],13:[2,30],14:[2,30],15:[2,30],19:[2,30]},{16:[1,299],21:298,30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{16:[1,299],21:300,30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,233],6:[2,233],11:[2,233],12:[2,233],13:[2,233],14:[2,233],15:[1,116],19:[2,233]},{5:[2,234],6:[2,234],11:[2,234],12:[2,234],13:[2,234],14:[2,234],15:[1,116],19:[2,234]},{5:[2,245],6:[2,245],11:[2,245],12:[2,245],13:[2,245],14:[2,245],15:[2,245],19:[2,245]},{5:[2,246],6:[2,246],11:[2,246],12:[2,246],13:[2,246],14:[2,246],15:[2,246],19:[2,246]},{5:[2,251],6:[2,251]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],19:[2,23],25:[1,301],26:[1,129]},{5:[2,25],6:[2,25],11:[2,25],12:[2,25],13:[2,25],14:[2,25],15:[2,25],19:[2,25]},{5:[2,252],6:[2,252]},{5:[2,253],6:[2,253]},{5:[2,254],6:[2,254]},{5:[2,60],6:[2,60],11:[2,60],12:[2,60],13:[2,60],14:[2,60],15:[2,60],16:[1,303],19:[2,60],30:304,35:[2,60],36:[2,60],42:195,43:80,45:302,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,71],6:[2,71],11:[2,71],12:[2,71],13:[2,71],14:[2,71],15:[2,71],16:[1,307],19:[2,71],30:308,35:[2,71],36:[2,71],42:195,43:306,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,82],6:[2,82],11:[2,82],12:[2,82],13:[2,82],14:[2,82],15:[2,82],16:[1,310],19:[2,82],30:311,35:[2,82],36:[2,82],42:195,47:62,48:[1,305],50:[1,196],51:309,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,93],6:[2,93],11:[2,93],12:[2,93],13:[2,93],14:[2,93],15:[2,93],16:[1,313],19:[2,93],30:314,35:[2,93],36:[2,93],42:195,47:62,48:[1,305],50:[1,196],53:94,54:[1,198],55:312,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,111],6:[2,111],11:[2,111],12:[2,111],13:[2,111],14:[2,111],15:[2,111],16:[1,316],19:[2,111],30:317,35:[2,111],36:[2,111],42:195,47:62,48:[1,305],50:[1,196],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:315,66:[1,102]},{5:[2,116],6:[2,116],11:[2,116],12:[2,116],13:[2,116],14:[2,116],15:[2,116],19:[2,116],35:[2,116],36:[2,116]},{5:[2,20],6:[2,20],11:[2,20],12:[2,20],13:[2,20],14:[2,20],15:[2,20],19:[2,20]},{6:[2,223],19:[2,223]},{4:318,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,65],6:[2,65],11:[2,65],12:[2,65],13:[2,65],14:[2,65],15:[2,65],16:[1,197],19:[2,65],35:[2,65],36:[2,65],42:195,46:[2,65],47:319,49:[2,65],50:[1,196],52:[2,65],53:94,54:[1,198],56:[2,65],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,65],66:[1,102],67:[2,65]},{5:[2,66],6:[2,66],11:[2,66],12:[2,66],13:[2,66],14:[2,66],15:[2,66],19:[2,66],35:[2,66],36:[2,66],46:[2,66],49:[2,66],52:[2,66],56:[2,66],65:[2,66],67:[2,66]},{5:[2,169],6:[2,169],11:[2,169],12:[2,169],13:[2,169],14:[2,169],15:[2,169],19:[2,169],35:[2,169],36:[2,169]},{5:[2,176],6:[2,176],11:[2,176],12:[2,176],13:[2,176],14:[2,176],15:[2,176],19:[2,176],35:[2,176],36:[2,176]},{5:[2,183],6:[2,183],11:[2,183],12:[2,183],13:[2,183],14:[2,183],15:[2,183],19:[2,183],35:[2,183],36:[2,183]},{5:[2,73],6:[2,73],11:[2,73],12:[2,73],13:[2,73],14:[2,73],15:[2,73],19:[2,73],35:[2,73],36:[2,73],46:[2,73],48:[2,73],49:[2,73],50:[1,206],52:[2,73],56:[2,73],65:[2,73],67:[2,73]},{5:[2,74],6:[2,74],11:[2,74],12:[2,74],13:[2,74],14:[2,74],15:[2,74],16:[1,219],19:[2,74],35:[2,74],36:[2,74],42:209,46:[2,74],48:[2,74],49:[2,74],52:[2,74],53:94,54:[1,198],56:[2,74],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,74],66:[1,102],67:[2,74]},{50:[1,134],54:[1,147],58:[1,149]},{5:[2,85],6:[2,85],11:[2,85],12:[2,85],13:[2,85],14:[2,85],15:[2,85],16:[1,251],19:[2,85],35:[2,85],36:[2,85],38:[2,85],40:[2,85],41:[2,85],44:[2,85],46:[2,85],48:[2,85],49:[2,85],50:[2,85],52:[2,85],53:250,56:[2,85],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,85],66:[1,102],67:[2,85]},{5:[2,95],6:[2,95],11:[2,95],12:[2,95],13:[2,95],14:[2,95],15:[2,95],19:[2,95],35:[2,95],36:[2,95],38:[2,95],40:[2,95],41:[2,95],44:[2,95],46:[2,95],48:[2,95],49:[2,95],50:[2,95],52:[2,95],54:[2,95],56:[2,95],58:[1,230],65:[2,95],67:[2,95]},{5:[2,96],6:[2,96],11:[2,96],12:[2,96],13:[2,96],14:[2,96],15:[2,96],19:[2,96],35:[2,96],36:[2,96],38:[2,96],40:[2,96],41:[2,96],44:[2,96],46:[2,96],48:[2,96],49:[2,96],50:[2,96],52:[2,96],54:[2,96],56:[2,96],57:240,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,96],66:[1,102],67:[2,96]},{5:[2,45],6:[2,45],11:[2,45],12:[2,45],13:[2,45],14:[2,45],15:[2,45],16:[1,204],19:[2,45],35:[2,45],36:[2,45],39:320,42:205,44:[1,81],53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,40],6:[2,40],11:[2,40],12:[2,40],13:[2,40],14:[2,40],15:[2,40],16:[1,322],19:[2,40],35:[2,40],36:[2,40],37:321,42:323,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,46],6:[2,46],11:[2,46],12:[2,46],13:[2,46],14:[2,46],15:[2,46],19:[2,46],35:[2,46],36:[2,46]},{44:[1,136],54:[1,147],58:[1,149]},{44:[1,207]},{5:[2,76],6:[2,76],11:[2,76],12:[2,76],13:[2,76],14:[2,76],15:[2,76],16:[1,219],19:[2,76],35:[2,76],36:[2,76],42:324,46:[2,76],48:[2,76],49:[2,76],52:[2,76],53:94,54:[1,198],56:[2,76],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,76],66:[1,102],67:[2,76]},{5:[2,54],6:[2,54],11:[2,54],12:[2,54],13:[2,54],14:[2,54],15:[2,54],16:[1,219],19:[2,54],35:[2,54],36:[2,54],38:[2,54],40:[2,54],42:325,53:94,54:[1,198],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],66:[1,102]},{5:[2,50],6:[2,50],11:[2,50],12:[2,50],13:[2,50],14:[2,50],15:[2,50],16:[1,307],19:[2,50],30:308,35:[2,50],36:[2,50],42:195,43:326,47:62,48:[1,305],50:[1,196],51:95,53:94,54:[1,198],55:101,57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102]},{5:[2,77],6:[2,77],11:[2,77],12:[2,77],13:[2,77],14:[2,77],15:[2,77],19:[2,77],35:[2,77],36:[2,77],46:[2,77],48:[2,77],49:[2,77],52:[2,77],56:[2,77],65:[2,77],67:[2,77]},{5:[2,162],6:[2,162],11:[2,162],12:[2,162],13:[2,162],14:[2,162],15:[2,162],19:[2,162],35:[2,162],36:[2,162]},{5:[2,156],6:[2,156],11:[2,156],12:[2,156],13:[2,156],14:[2,156],15:[2,156],19:[2,156],35:[2,156],36:[2,156]},{5:[2,144],6:[2,144],11:[2,144],12:[2,144],13:[2,144],14:[2,144],15:[2,144],19:[2,144],35:[2,144],36:[2,144]},{5:[2,150],6:[2,150],11:[2,150],12:[2,150],13:[2,150],14:[2,150],15:[2,150],19:[2,150],35:[2,150],36:[2,150]},{5:[2,132],6:[2,132],11:[2,132],12:[2,132],13:[2,132],14:[2,132],15:[2,132],19:[2,132],35:[2,132],36:[2,132]},{5:[2,138],6:[2,138],11:[2,138],12:[2,138],13:[2,138],14:[2,138],15:[2,138],19:[2,138],35:[2,138],36:[2,138]},{5:[2,168],6:[2,168],11:[2,168],12:[2,168],13:[2,168],14:[2,168],15:[2,168],19:[2,168],35:[2,168],36:[2,168]},{5:[2,175],6:[2,175],11:[2,175],12:[2,175],13:[2,175],14:[2,175],15:[2,175],19:[2,175],35:[2,175],36:[2,175]},{5:[2,182],6:[2,182],11:[2,182],12:[2,182],13:[2,182],14:[2,182],15:[2,182],19:[2,182],35:[2,182],36:[2,182]},{54:[1,147],58:[1,149]},{5:[2,55],6:[2,55],11:[2,55],12:[2,55],13:[2,55],14:[2,55],15:[2,55],19:[2,55],35:[2,55],36:[2,55],38:[2,55],40:[2,55]},{5:[2,159],6:[2,159],11:[2,159],12:[2,159],13:[2,159],14:[2,159],15:[2,159],19:[2,159],35:[2,159],36:[2,159]},{5:[2,153],6:[2,153],11:[2,153],12:[2,153],13:[2,153],14:[2,153],15:[2,153],19:[2,153],35:[2,153],36:[2,153]},{5:[2,141],6:[2,141],11:[2,141],12:[2,141],13:[2,141],14:[2,141],15:[2,141],19:[2,141],35:[2,141],36:[2,141]},{5:[2,147],6:[2,147],11:[2,147],12:[2,147],13:[2,147],14:[2,147],15:[2,147],19:[2,147],35:[2,147],36:[2,147]},{5:[2,129],6:[2,129],11:[2,129],12:[2,129],13:[2,129],14:[2,129],15:[2,129],19:[2,129],35:[2,129],36:[2,129]},{5:[2,135],6:[2,135],11:[2,135],12:[2,135],13:[2,135],14:[2,135],15:[2,135],19:[2,135],35:[2,135],36:[2,135]},{5:[2,165],6:[2,165],11:[2,165],12:[2,165],13:[2,165],14:[2,165],15:[2,165],19:[2,165],35:[2,165],36:[2,165]},{5:[2,172],6:[2,172],11:[2,172],12:[2,172],13:[2,172],14:[2,172],15:[2,172],19:[2,172],35:[2,172],36:[2,172]},{5:[2,179],6:[2,179],11:[2,179],12:[2,179],13:[2,179],14:[2,179],15:[2,179],19:[2,179],35:[2,179],36:[2,179]},{5:[2,98],6:[2,98],11:[2,98],12:[2,98],13:[2,98],14:[2,98],15:[2,98],19:[2,98],35:[2,98],36:[2,98],38:[2,98],40:[2,98],41:[2,98],44:[2,98],46:[2,98],48:[2,98],49:[2,98],50:[2,98],52:[2,98],54:[2,98],56:[2,98],57:327,59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,98],66:[1,102],67:[2,98]},{5:[2,160],6:[2,160],11:[2,160],12:[2,160],13:[2,160],14:[2,160],15:[2,160],19:[2,160],35:[2,160],36:[2,160]},{5:[2,154],6:[2,154],11:[2,154],12:[2,154],13:[2,154],14:[2,154],15:[2,154],19:[2,154],35:[2,154],36:[2,154]},{5:[2,142],6:[2,142],11:[2,142],12:[2,142],13:[2,142],14:[2,142],15:[2,142],19:[2,142],35:[2,142],36:[2,142]},{5:[2,148],6:[2,148],11:[2,148],12:[2,148],13:[2,148],14:[2,148],15:[2,148],19:[2,148],35:[2,148],36:[2,148]},{5:[2,130],6:[2,130],11:[2,130],12:[2,130],13:[2,130],14:[2,130],15:[2,130],19:[2,130],35:[2,130],36:[2,130]},{5:[2,136],6:[2,136],11:[2,136],12:[2,136],13:[2,136],14:[2,136],15:[2,136],19:[2,136],35:[2,136],36:[2,136]},{5:[2,166],6:[2,166],11:[2,166],12:[2,166],13:[2,166],14:[2,166],15:[2,166],19:[2,166],35:[2,166],36:[2,166]},{5:[2,173],6:[2,173],11:[2,173],12:[2,173],13:[2,173],14:[2,173],15:[2,173],19:[2,173],35:[2,173],36:[2,173]},{5:[2,180],6:[2,180],11:[2,180],12:[2,180],13:[2,180],14:[2,180],15:[2,180],19:[2,180],35:[2,180],36:[2,180]},{5:[2,99],6:[2,99],11:[2,99],12:[2,99],13:[2,99],14:[2,99],15:[2,99],19:[2,99],35:[2,99],36:[2,99],38:[2,99],40:[2,99],41:[2,99],44:[2,99],46:[2,99],48:[2,99],49:[2,99],50:[2,99],52:[2,99],54:[2,99],56:[2,99],65:[2,99],67:[2,99]},{5:[2,161],6:[2,161],11:[2,161],12:[2,161],13:[2,161],14:[2,161],15:[2,161],19:[2,161],35:[2,161],36:[2,161]},{5:[2,155],6:[2,155],11:[2,155],12:[2,155],13:[2,155],14:[2,155],15:[2,155],19:[2,155],35:[2,155],36:[2,155]},{5:[2,143],6:[2,143],11:[2,143],12:[2,143],13:[2,143],14:[2,143],15:[2,143],19:[2,143],35:[2,143],36:[2,143]},{5:[2,149],6:[2,149],11:[2,149],12:[2,149],13:[2,149],14:[2,149],15:[2,149],19:[2,149],35:[2,149],36:[2,149]},{5:[2,131],6:[2,131],11:[2,131],12:[2,131],13:[2,131],14:[2,131],15:[2,131],19:[2,131],35:[2,131],36:[2,131]},{5:[2,137],6:[2,137],11:[2,137],12:[2,137],13:[2,137],14:[2,137],15:[2,137],19:[2,137],35:[2,137],36:[2,137]},{5:[2,167],6:[2,167],11:[2,167],12:[2,167],13:[2,167],14:[2,167],15:[2,167],19:[2,167],35:[2,167],36:[2,167]},{5:[2,174],6:[2,174],11:[2,174],12:[2,174],13:[2,174],14:[2,174],15:[2,174],19:[2,174],35:[2,174],36:[2,174]},{5:[2,181],6:[2,181],11:[2,181],12:[2,181],13:[2,181],14:[2,181],15:[2,181],19:[2,181],35:[2,181],36:[2,181]},{5:[2,88],6:[2,88],11:[2,88],12:[2,88],13:[2,88],14:[2,88],15:[2,88],19:[2,88],35:[2,88],36:[2,88],38:[2,88],40:[2,88],41:[2,88],44:[2,88],46:[2,88],48:[2,88],49:[2,88],50:[2,88],52:[2,88],56:[2,88],65:[2,88],67:[2,88]},{58:[1,149]},{5:[2,87],6:[2,87],11:[2,87],12:[2,87],13:[2,87],14:[2,87],15:[2,87],16:[1,251],19:[2,87],35:[2,87],36:[2,87],38:[2,87],40:[2,87],41:[2,87],44:[2,87],46:[2,87],48:[2,87],49:[2,87],50:[2,87],52:[2,87],53:328,56:[2,87],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,87],66:[1,102],67:[2,87]},{5:[2,106],6:[2,106],11:[2,106],12:[2,106],13:[2,106],14:[2,106],15:[2,106],19:[2,106],35:[2,106],36:[2,106],38:[2,106],40:[2,106],41:[2,106],44:[2,106],46:[2,106],48:[2,106],49:[2,106],50:[2,106],52:[2,106],54:[2,106],56:[2,106],58:[2,106],65:[2,106],67:[2,106],78:[2,106],79:[2,106],80:[2,106],81:[2,106],82:[2,106],83:[2,106],84:[2,106],85:[2,106],86:[2,106]},{1:[2,1]},{5:[2,9],6:[2,9],11:[2,9],12:[2,9],13:[1,108],14:[1,109],15:[1,110],19:[2,9]},{5:[2,23],6:[2,23],11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],17:[1,122],19:[2,23],25:[1,301],26:[1,129],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146]},{4:329,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{16:[1,176],18:[1,163],23:164,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,10],6:[2,10],11:[2,10],12:[2,10],13:[1,108],14:[1,109],15:[1,110],19:[2,10]},{5:[2,11],6:[2,11],11:[2,11],12:[2,11],13:[2,11],14:[2,11],15:[1,110],19:[2,11]},{5:[2,12],6:[2,12],11:[2,12],12:[2,12],13:[2,12],14:[2,12],15:[1,110],19:[2,12]},{5:[2,13],6:[2,13],11:[2,13],12:[2,13],13:[2,13],14:[2,13],15:[2,13],19:[2,13]},{1:[2,3]},{1:[2,5]},{5:[2,236],6:[2,236],11:[2,236],12:[2,236],13:[2,236],14:[2,236],15:[1,116],19:[2,236]},{5:[2,237],6:[2,237],11:[2,237],12:[2,237],13:[2,237],14:[2,237],15:[2,237],19:[2,237]},{4:330,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,23],6:[2,23],8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],18:[1,158],19:[2,23],25:[1,301],26:[1,129],28:[1,156],29:[1,157],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{16:[1,176],23:164,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168]},{5:[2,239],6:[2,239],11:[2,239],12:[2,239],13:[2,239],14:[2,239],15:[1,116],19:[2,239]},{5:[2,240],6:[2,240],11:[2,240],12:[2,240],13:[2,240],14:[2,240],15:[2,240],19:[2,240]},{4:331,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,242],6:[2,242],11:[2,242],12:[2,242],13:[2,242],14:[2,242],15:[2,242],19:[2,242]},{4:332,11:[1,27],12:[1,258],16:[1,256],18:[1,257],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,167],29:[1,168],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93]},{5:[2,247],6:[2,247],11:[2,247],12:[2,247],13:[1,114],14:[1,115],15:[1,116],19:[2,247]},{5:[2,248],6:[2,248],11:[2,248],12:[2,248],13:[1,114],14:[1,115],15:[1,116],19:[2,248]},{1:[2,8]},{6:[2,225],19:[2,225]},{5:[2,67],6:[2,67],11:[2,67],12:[2,67],13:[2,67],14:[2,67],15:[2,67],19:[2,67],35:[2,67],36:[2,67],46:[2,67],49:[2,67],52:[2,67],56:[2,67],65:[2,67],67:[2,67]},{5:[2,47],6:[2,47],11:[2,47],12:[2,47],13:[2,47],14:[2,47],15:[2,47],19:[2,47],35:[2,47],36:[2,47]},{5:[2,78],6:[2,78],11:[2,78],12:[2,78],13:[2,78],14:[2,78],15:[2,78],19:[2,78],35:[2,78],36:[2,78],46:[2,78],48:[2,78],49:[2,78],52:[2,78],56:[2,78],65:[2,78],67:[2,78]},{5:[2,56],6:[2,56],11:[2,56],12:[2,56],13:[2,56],14:[2,56],15:[2,56],19:[2,56],35:[2,56],36:[2,56],38:[2,56],40:[2,56]},{5:[2,89],6:[2,89],11:[2,89],12:[2,89],13:[2,89],14:[2,89],15:[2,89],19:[2,89],35:[2,89],36:[2,89],38:[2,89],40:[2,89],41:[2,89],44:[2,89],46:[2,89],48:[2,89],49:[2,89],50:[2,89],52:[2,89],56:[2,89],65:[2,89],67:[2,89]},{5:[2,100],6:[2,100],11:[2,100],12:[2,100],13:[2,100],14:[2,100],15:[2,100],19:[2,100],35:[2,100],36:[2,100],38:[2,100],40:[2,100],41:[2,100],44:[2,100],46:[2,100],48:[2,100],49:[2,100],50:[2,100],52:[2,100],54:[2,100],56:[2,100],65:[2,100],67:[2,100]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,333]},{8:123,11:[2,23],12:[2,23],13:[2,23],14:[2,23],15:[2,23],16:[1,155],17:[1,122],18:[1,158],19:[2,23],25:[1,301],26:[1,129],28:[1,156],29:[1,157],38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{4:334,8:160,11:[1,27],12:[1,258],16:[1,286],18:[1,287],20:[1,10],21:11,22:12,23:162,24:[1,60],25:[1,177],27:[1,61],28:[1,14],29:[1,15],30:22,31:23,32:24,33:25,34:26,37:67,39:65,40:[1,66],42:78,43:80,44:[1,81],45:64,47:62,48:[1,63],50:[1,79],51:95,53:94,54:[1,85],55:101,57:83,58:[1,84],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],64:103,66:[1,102],68:68,69:69,70:70,71:71,72:72,73:73,74:74,75:75,76:76,77:[1,77],78:[1,89],79:[1,90],80:[1,87],81:[1,88],82:[1,86],83:[1,82],84:[1,91],85:[1,92],86:[1,93],87:13,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],124:[1,16],125:[1,17]},{5:[2,15],6:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58],120:[1,336],121:[1,337],122:[1,338],123:[1,339]},{5:[2,244],6:[2,244],11:[2,244],12:[2,244],13:[2,244],14:[2,244],15:[2,244],19:[2,244]},{6:[2,250],19:[2,250]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,340]},{6:[2,216],19:[2,216]},{6:[2,218],19:[2,218]},{6:[2,220],19:[2,220]},{6:[2,222],19:[2,222]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26],123:[1,341]},{6:[2,224],19:[2,224]},{5:[2,36],6:[2,36],11:[2,36],12:[2,36],13:[2,36],14:[2,36],15:[2,36],19:[2,36],35:[2,36],36:[2,36]},{38:[1,133],40:[1,132],41:[1,137],44:[1,136],46:[1,131],48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153],78:[1,142],79:[1,143],80:[1,140],81:[1,141],82:[1,139],83:[1,138],84:[1,144],85:[1,145],86:[1,146]},{5:[2,37],6:[2,37],11:[2,37],12:[2,37],13:[2,37],14:[2,37],15:[2,37],19:[2,37],35:[2,37],36:[2,37]},{5:[2,26],6:[2,26],11:[2,26],12:[2,26],13:[2,26],14:[2,26],15:[2,26],19:[2,26]},{5:[2,61],6:[2,61],11:[2,61],12:[2,61],13:[2,61],14:[2,61],15:[2,61],19:[2,61],35:[2,61],36:[2,61]},{48:[1,130],49:[1,135],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{49:[1,182],52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{16:[1,197],42:195,47:191,49:[2,63],50:[1,196],52:[2,63],53:94,54:[1,198],56:[2,63],57:199,58:[1,200],59:96,60:[1,97],61:[1,98],62:[1,99],63:[1,100],65:[2,63],66:[1,102],67:[2,63]},{5:[2,72],6:[2,72],11:[2,72],12:[2,72],13:[2,72],14:[2,72],15:[2,72],19:[2,72],35:[2,72],36:[2,72]},{48:[1,130],50:[1,134],52:[1,148],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{52:[1,183],56:[1,184],65:[1,185],67:[1,186]},{5:[2,83],6:[2,83],11:[2,83],12:[2,83],13:[2,83],14:[2,83],15:[2,83],19:[2,83],35:[2,83],36:[2,83]},{48:[1,130],50:[1,134],54:[1,147],56:[1,150],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{56:[1,184],65:[1,185],67:[1,186]},{5:[2,94],6:[2,94],11:[2,94],12:[2,94],13:[2,94],14:[2,94],15:[2,94],19:[2,94],35:[2,94],36:[2,94]},{48:[1,130],50:[1,134],54:[1,147],58:[1,149],60:[1,152],65:[1,151],66:[1,154],67:[1,153]},{65:[1,185],67:[1,186]},{5:[2,112],6:[2,112],11:[2,112],12:[2,112],13:[2,112],14:[2,112],15:[2,112],19:[2,112],35:[2,112],36:[2,112]},{48:[1,130],50:[1,134],54:[1,147],58:[1,149],66:[1,154],67:[1,153]},{67:[1,186]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,342]},{5:[2,68],6:[2,68],11:[2,68],12:[2,68],13:[2,68],14:[2,68],15:[2,68],19:[2,68],35:[2,68],36:[2,68],46:[2,68],49:[2,68],52:[2,68],56:[2,68],65:[2,68],67:[2,68]},{5:[2,48],6:[2,48],11:[2,48],12:[2,48],13:[2,48],14:[2,48],15:[2,48],19:[2,48],35:[2,48],36:[2,48]},{5:[2,41],6:[2,41],11:[2,41],12:[2,41],13:[2,41],14:[2,41],15:[2,41],19:[2,41],35:[2,41],36:[2,41]},{41:[1,137],54:[1,147],58:[1,149]},{41:[1,208]},{5:[2,79],6:[2,79],11:[2,79],12:[2,79],13:[2,79],14:[2,79],15:[2,79],19:[2,79],35:[2,79],36:[2,79],46:[2,79],48:[2,79],49:[2,79],52:[2,79],56:[2,79],65:[2,79],67:[2,79]},{5:[2,57],6:[2,57],11:[2,57],12:[2,57],13:[2,57],14:[2,57],15:[2,57],19:[2,57],35:[2,57],36:[2,57],38:[2,57],40:[2,57]},{5:[2,51],6:[2,51],11:[2,51],12:[2,51],13:[2,51],14:[2,51],15:[2,51],19:[2,51],35:[2,51],36:[2,51]},{5:[2,101],6:[2,101],11:[2,101],12:[2,101],13:[2,101],14:[2,101],15:[2,101],19:[2,101],35:[2,101],36:[2,101],38:[2,101],40:[2,101],41:[2,101],44:[2,101],46:[2,101],48:[2,101],49:[2,101],50:[2,101],52:[2,101],54:[2,101],56:[2,101],65:[2,101],67:[2,101]},{5:[2,90],6:[2,90],11:[2,90],12:[2,90],13:[2,90],14:[2,90],15:[2,90],19:[2,90],35:[2,90],36:[2,90],38:[2,90],40:[2,90],41:[2,90],44:[2,90],46:[2,90],48:[2,90],49:[2,90],50:[2,90],52:[2,90],56:[2,90],65:[2,90],67:[2,90]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,343]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,344]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,345]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,346]},{87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{11:[1,106],12:[1,107],13:[1,108],14:[1,109],15:[1,110],19:[1,347]},{5:[2,235],6:[2,235],11:[2,235],12:[2,235],13:[2,235],14:[2,235],15:[2,235],19:[2,235]},{6:[2,227],19:[2,227]},{6:[2,228],19:[2,228]},{6:[2,229],19:[2,229]},{6:[2,230],19:[2,230]},{5:[2,16],6:[2,16],11:[2,16],12:[2,16],13:[2,16],14:[2,16],15:[2,16],19:[2,16]},{6:[2,226],19:[2,226]},{5:[2,28],6:[2,28],11:[2,28],12:[2,28],13:[2,28],14:[2,28],15:[2,28],19:[2,28],126:[2,28],127:[2,28]},{5:[2,15],6:[2,15],11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15]},{5:[2,238],6:[2,238],11:[2,238],12:[2,238],13:[2,238],14:[2,238],15:[2,238],19:[2,238],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{5:[2,241],6:[2,241],11:[2,241],12:[2,241],13:[2,241],14:[2,241],15:[2,241],19:[2,241],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]},{5:[2,243],6:[2,243],11:[2,243],12:[2,243],13:[2,243],14:[2,243],15:[2,243],19:[2,243]},{11:[2,15],12:[2,15],13:[2,15],14:[2,15],15:[2,15],19:[2,15],87:335,88:[1,28],89:[1,29],90:[1,30],91:[1,31],92:[1,32],93:[1,33],94:[1,34],95:[1,35],96:[1,36],97:[1,37],98:[1,38],99:[1,39],100:[1,40],101:[1,41],102:[1,42],103:[1,43],104:[1,44],105:[1,45],106:[1,46],107:[1,47],108:[1,48],109:[1,49],110:[1,50],111:[1,51],112:[1,52],113:[1,53],114:[1,54],115:[1,55],116:[1,56],117:[1,57],118:[1,58]}],
defaultActions: {105:[2,2],112:[2,4],119:[2,6],120:[2,7],254:[2,1],263:[2,3],264:[2,5],277:[2,8]},
parseError: function parseError(str,hash){if(hash.recoverable){this.trace(str)}else{throw new Error(str)}},
parse: function parse(input) {
    var self = this, stack = [0], vstack = [null], lstack = [], table = this.table, yytext = '', yylineno = 0, yyleng = 0, recovering = 0, TERROR = 2, EOF = 1;
    var args = lstack.slice.call(arguments, 1);
    this.lexer.setInput(input);
    this.lexer.yy = this.yy;
    this.yy.lexer = this.lexer;
    this.yy.nParser = this;
    if (typeof this.lexer.yylloc == 'undefined') {
        this.lexer.yylloc = {};
    }
    var yyloc = this.lexer.yylloc;
    lstack.push(yyloc);
    var ranges = this.lexer.options && this.lexer.options.ranges;
    if (typeof this.yy.parseError === 'function') {
        this.parseError = this.yy.parseError;
    } else {
        this.parseError = Object.getPrototypeOf(this).parseError;
    }
    function popStack(n) {
        stack.length = stack.length - 2 * n;
        vstack.length = vstack.length - n;
        lstack.length = lstack.length - n;
    }
    function lex() {
        var token;
        token = self.lexer.lex() || EOF;
        if (typeof token !== 'number') {
            token = self.symbols_[token] || token;
        }
        return token;
    }
    var symbol, preErrorSymbol, state, action, a, r, yyval = {}, p, len, newState, expected;
    while (true) {
        state = stack[stack.length - 1];
        if (this.defaultActions[state]) {
            action = this.defaultActions[state];
        } else {
            if (symbol === null || typeof symbol == 'undefined') {
                symbol = lex();
            }
            action = table[state] && table[state][symbol];
        }
                    if (typeof action === 'undefined' || !action.length || !action[0]) {
                var errStr = '';
                expected = [];
                for (p in table[state]) {
                    if (this.terminals_[p] && p > TERROR) {
                        expected.push('\'' + this.terminals_[p] + '\'');
                    }
                }
                if (this.lexer.showPosition) {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ':\n' + this.lexer.showPosition() + '\nExpecting ' + expected.join(', ') + ', got \'' + (this.terminals_[symbol] || symbol) + '\'';
                } else {
                    errStr = 'Parse error on line ' + (yylineno + 1) + ': Unexpected ' + (symbol == EOF ? 'end of input' : '\'' + (this.terminals_[symbol] || symbol) + '\'');
                }
                this.parseError(errStr, {
                    text: this.lexer.match,
                    token: this.terminals_[symbol] || symbol,
                    line: this.lexer.yylineno,
                    loc: yyloc,
                    expected: expected
                });
            }
        if (action[0] instanceof Array && action.length > 1) {
            throw new Error('Parse Error: multiple actions possible at state: ' + state + ', token: ' + symbol);
        }
        switch (action[0]) {
        case 1:
            stack.push(symbol);
            vstack.push(this.lexer.yytext);
            lstack.push(this.lexer.yylloc);
            stack.push(action[1]);
            symbol = null;
            if (!preErrorSymbol) {
                yyleng = this.lexer.yyleng;
                yytext = this.lexer.yytext;
                yylineno = this.lexer.yylineno;
                yyloc = this.lexer.yylloc;
                if (recovering > 0) {
                    recovering--;
                }
            } else {
                symbol = preErrorSymbol;
                preErrorSymbol = null;
            }
            break;
        case 2:
            len = this.productions_[action[1]][1];
            yyval.$ = vstack[vstack.length - len];
            yyval._$ = {
                first_line: lstack[lstack.length - (len || 1)].first_line,
                last_line: lstack[lstack.length - 1].last_line,
                first_column: lstack[lstack.length - (len || 1)].first_column,
                last_column: lstack[lstack.length - 1].last_column
            };
            if (ranges) {
                yyval._$.range = [
                    lstack[lstack.length - (len || 1)].range[0],
                    lstack[lstack.length - 1].range[1]
                ];
            }
            r = this.performAction.apply(yyval, [
                yytext,
                yyleng,
                yylineno,
                this.yy,
                action[1],
                vstack,
                lstack
            ].concat(args));
            if (typeof r !== 'undefined') {
                return r;
            }
            if (len) {
                stack = stack.slice(0, -1 * len * 2);
                vstack = vstack.slice(0, -1 * len);
                lstack = lstack.slice(0, -1 * len);
            }
            stack.push(this.productions_[action[1]][0]);
            vstack.push(yyval.$);
            lstack.push(yyval._$);
            newState = table[stack[stack.length - 2]][stack[stack.length - 1]];
            stack.push(newState);
            break;
        case 3:
            return true;
        }
    }
    return true;
}};
/* generated by jison-lex 0.2.1 */
var lexer = (function(){
var lexer = {

EOF:1,

parseError:function parseError(str,hash){if(this.yy.nParser){this.yy.nParser.parseError(str,hash)}else{throw new Error(str)}},

// resets the lexer, sets new input
setInput:function (input){this._input=input;this._more=this._backtrack=this.done=false;this.yylineno=this.yyleng=0;this.yytext=this.matched=this.match="";this.conditionStack=["INITIAL"];this.yylloc={first_line:1,first_column:0,last_line:1,last_column:0};if(this.options.ranges){this.yylloc.range=[0,0]}this.offset=0;return this},

// consumes and returns one char from the input
input:function (){var ch=this._input[0];this.yytext+=ch;this.yyleng++;this.offset++;this.match+=ch;this.matched+=ch;var lines=ch.match(/(?:\r\n?|\n).*/g);if(lines){this.yylineno++;this.yylloc.last_line++}else{this.yylloc.last_column++}if(this.options.ranges){this.yylloc.range[1]++}this._input=this._input.slice(1);return ch},

// unshifts one char (or a string) into the input
unput:function (ch){var len=ch.length;var lines=ch.split(/(?:\r\n?|\n)/g);this._input=ch+this._input;this.yytext=this.yytext.substr(0,this.yytext.length-len-1);this.offset-=len;var oldLines=this.match.split(/(?:\r\n?|\n)/g);this.match=this.match.substr(0,this.match.length-1);this.matched=this.matched.substr(0,this.matched.length-1);if(lines.length-1){this.yylineno-=lines.length-1}var r=this.yylloc.range;this.yylloc={first_line:this.yylloc.first_line,last_line:this.yylineno+1,first_column:this.yylloc.first_column,last_column:lines?(lines.length===oldLines.length?this.yylloc.first_column:0)+oldLines[oldLines.length-lines.length].length-lines[0].length:this.yylloc.first_column-len};if(this.options.ranges){this.yylloc.range=[r[0],r[0]+this.yyleng-len]}this.yyleng=this.yytext.length;return this},

// When called from action, caches matched text and appends it on next action
more:function (){this._more=true;return this},

// When called from action, signals the lexer that this rule fails to match the input, so the next matching rule (regex) should be tested instead.
reject:function (){if(this.options.backtrack_lexer){this._backtrack=true}else{return this.parseError("Lexical error on line "+(this.yylineno+1)+". You can only invoke reject() in the lexer when the lexer is of the backtracking persuasion (options.backtrack_lexer = true).\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})}return this},

// retain first n characters of the match
less:function (n){this.unput(this.match.slice(n))},

// displays already matched input, i.e. for error messages
pastInput:function (){var past=this.matched.substr(0,this.matched.length-this.match.length);return(past.length>20?"...":"")+past.substr(-20).replace(/\n/g,"")},

// displays upcoming input, i.e. for error messages
upcomingInput:function (){var next=this.match;if(next.length<20){next+=this._input.substr(0,20-next.length)}return(next.substr(0,20)+(next.length>20?"...":"")).replace(/\n/g,"")},

// displays the character position where the lexing error occurred, i.e. for error messages
showPosition:function (){var pre=this.pastInput();var c=new Array(pre.length+1).join("-");return pre+this.upcomingInput()+"\n"+c+"^"},

// test the lexed token: return FALSE when not a match, otherwise return token
test_match:function (match,indexed_rule){var token,lines,backup;if(this.options.backtrack_lexer){backup={yylineno:this.yylineno,yylloc:{first_line:this.yylloc.first_line,last_line:this.last_line,first_column:this.yylloc.first_column,last_column:this.yylloc.last_column},yytext:this.yytext,match:this.match,matches:this.matches,matched:this.matched,yyleng:this.yyleng,offset:this.offset,_more:this._more,_input:this._input,yy:this.yy,conditionStack:this.conditionStack.slice(0),done:this.done};if(this.options.ranges){backup.yylloc.range=this.yylloc.range.slice(0)}}lines=match[0].match(/(?:\r\n?|\n).*/g);if(lines){this.yylineno+=lines.length}this.yylloc={first_line:this.yylloc.last_line,last_line:this.yylineno+1,first_column:this.yylloc.last_column,last_column:lines?lines[lines.length-1].length-lines[lines.length-1].match(/\r?\n?/)[0].length:this.yylloc.last_column+match[0].length};this.yytext+=match[0];this.match+=match[0];this.matches=match;this.yyleng=this.yytext.length;if(this.options.ranges){this.yylloc.range=[this.offset,this.offset+=this.yyleng]}this._more=false;this._backtrack=false;this._input=this._input.slice(match[0].length);this.matched+=match[0];token=this.performAction.call(this,this.yy,this,indexed_rule,this.conditionStack[this.conditionStack.length-1]);if(this.done&&this._input){this.done=false}if(token){return token}else if(this._backtrack){for(var k in backup){this[k]=backup[k]}return false}return false},

// return next match in input
next:function (){if(this.done){return this.EOF}if(!this._input){this.done=true}var token,match,tempMatch,index;if(!this._more){this.yytext="";this.match=""}var rules=this._currentRules();for(var i=0;i<rules.length;i++){tempMatch=this._input.match(this.rules[rules[i]]);if(tempMatch&&(!match||tempMatch[0].length>match[0].length)){match=tempMatch;index=i;if(this.options.backtrack_lexer){token=this.test_match(tempMatch,rules[i]);if(token!==false){return token}else if(this._backtrack){match=false;continue}else{return false}}else if(!this.options.flex){break}}}if(match){token=this.test_match(match,rules[index]);if(token!==false){return token}return false}if(this._input===""){return this.EOF}else{return this.parseError("Lexical error on line "+(this.yylineno+1)+". Unrecognized text.\n"+this.showPosition(),{text:"",token:null,line:this.yylineno})}},

// return next match that has a token
lex:function lex(){var r=this.next();if(r){return r}else{return this.lex()}},

// activates a new lexer condition state (pushes the new lexer condition state onto the condition stack)
begin:function begin(condition){this.conditionStack.push(condition)},

// pop the previously active lexer condition state off the condition stack
popState:function popState(){var n=this.conditionStack.length-1;if(n>0){return this.conditionStack.pop()}else{return this.conditionStack[0]}},

// produce the lexer rule set which is active for the currently active lexer condition state
_currentRules:function _currentRules(){if(this.conditionStack.length&&this.conditionStack[this.conditionStack.length-1]){return this.conditions[this.conditionStack[this.conditionStack.length-1]].rules}else{return this.conditions["INITIAL"].rules}},

// return the currently active lexer condition state; when an index argument is provided it produces the N-th previous condition state, if available
topState:function topState(n){n=this.conditionStack.length-1-Math.abs(n||0);if(n>=0){return this.conditionStack[n]}else{return"INITIAL"}},

// alias for begin(condition)
pushState:function pushState(condition){this.begin(condition)},

// return the number of states currently on the stack
stateStackSize:function stateStackSize(){return this.conditionStack.length},
options: {"flex":true,"case-insensitive":true},
performAction: function anonymous(yy,yy_,$avoiding_name_collisions,YY_START
/**/) {

var YYSTATE=YY_START;
switch($avoiding_name_collisions) {
case 0:/* skip whitespace */
break;
case 1:return 28
break;
case 2:return 29
break;
case 3:return 20
break;
case 4:return 16
break;
case 5:return 63
break;
case 6:return 77
break;
case 7:return 77
break;
case 8:return 66
break;
case 9:return 61
break;
case 10:return 62 
break;
case 11:return 67
break;
case 12:return 65                                                                                                                                     
break;
case 13:return 60
break;
case 14:return 56
break;
case 15:return 58
break;
case 16:return 54
break;
case 17:return 50
break;
case 18:return 48
break;
case 19:return 41
break;
case 20:return 44
break;
case 21:return 38
break;
case 22:return 40
break;
case 23:return 82
break;
case 24:return 83
break;
case 25:return 80
break;
case 26:return 81
break;
case 27:return 78
break;
case 28:return 79
break;
case 29:return 84
break;
case 30:return 85
break;
case 31:return 86
break;
case 32:return 88
break;
case 33:return 91
break;
case 34:return 92
break;
case 35:return 93
break;
case 36:return 94
break;
case 37:return 95
break;
case 38:return 96
break;
case 39:return 97
break;
case 40:return 98
break;
case 41:return 99
break;
case 42:return 100
break;
case 43:return 101
break;
case 44:return 102
break;
case 45:return 89
break;
case 46:return 104
break;
case 47:return 103
break;
case 48:return 105
break;
case 49:return 106
break;
case 50:return 107
break;
case 51:return 108
break;
case 52:return 90
break;
case 53:return 111
break;
case 54:return 110
break;
case 55:return 109
break;
case 56:return 112
break;
case 57:return 113
break;
case 58:return 114
break;
case 59:return 115
break;
case 60:return 116
break;
case 61:return 117
break;
case 62:return 122
break;
case 63:return 123
break;
case 64:return 118
break;
case 65:return 120
break;
case 66:return 121
break;
case 67:return 127
break;
case 68:return 126
break;
case 69:return 124
break;
case 70:return 125
break;
case 71:return 35
break;
case 72:return 14
break;
case 73:return 13
break;
case 74:return 12
break;
case 75:return 11
break;
case 76:return 15
break;
case 77:return 17
break;
case 78:return 18
break;
case 79:return 19
break;
case 80:return 12
break;
case 81:return 36
break;
case 82:return 5
break;
case 83:return 25
break;
case 84:return 24
break;
case 85:return 27
break;
case 86:return 26
break;
case 87:return 6
break;
case 88:return 'INVALID'
break;
case 89:console.log(yy_.yytext);
break;
}
},
rules: [/^(?:\s+)/i,/^(?:[1-9][0-9]{0,1}(,[0-9]{2})*(,[0-9]{3}))/i,/^(?:[1-9][0-9]{0,2}(,[0-9]{3})*(,[0-9]{3}))/i,/^(?:[1-9][0-9]*(\s)[0-9]+(\s)?(\/|\\)(\s)?[0-9]+)/i,/^(?:([0-9]+(\.[0-9]+)?)|(\.[0-9]+))/i,/^(?:(twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(one|ones|two|three|four|five|six|seven|eight|nine))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|ten|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths|half|third|fourth|fifth|sixth|seventh|eighth|ninth|elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths|eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth|tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths|tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth|quarters|quarter|hundredths|hundredth|thousandths|thousandth))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|ten|twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety)(\s)*(-)(\s)*(halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths|half|third|fourth|fifth|sixth|seventh|eighth|ninth|elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths|eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth|tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths|tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth|quarters|quarter|hundredths|hundredth|thousandths|thousandth))/i,/^(?:(zero|one|two|three|four|five|six|seven|eight|nine))/i,/^(?:(eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen))/i,/^(?:(twenty|thirty|forty|fifty|sixty|seventy|eighty|ninety))/i,/^(?:(ones))/i,/^(?:(tens))/i,/^(?:(ten))/i,/^(?:(hundreds))/i,/^(?:(hundred))/i,/^(?:(thousands|thousand))/i,/^(?:(lakhs|lakh))/i,/^(?:(crores|crore))/i,/^(?:(millions))/i,/^(?:(million))/i,/^(?:(billions))/i,/^(?:(billion))/i,/^(?:(first|half|third|fourth|fifth|sixth|seventh|eighth|ninth))/i,/^(?:(firsts|halves|thirds|fourths|fifths|sixths|sevenths|eighths|ninths))/i,/^(?:(eleventh|twelfth|thirteenth|fourteenth|fifteenth|sixteenth|seventeenth|eighteenth|nineteenth))/i,/^(?:(elevenths|twelfths|thirteenths|fourteenths|fifteenths|sixteenths|seventeenths|eighteenths|nineteenths))/i,/^(?:(tenth|twentieth|thirtieth|fortieth|fiftieth|sixtieth|seventieth|eightieth|ninetieth))/i,/^(?:(tenths|twentieths|thirtieths|fortieths|fiftieths|sixtieths|seventieths|eightieths|ninetieths))/i,/^(?:(quarters|quarter))/i,/^(?:(hundredths|hundredth))/i,/^(?:(thousandths|thousandth))/i,/^(?:(metres|metre|m))/i,/^(?:(kilometres|kilometre|km))/i,/^(?:(millimetres|millimetre|mm))/i,/^(?:(centimetres|centimetre|cm))/i,/^(?:(litres|litre|l))/i,/^(?:(kilolitres|kilolitre|kl))/i,/^(?:(millilitres|millimetre|ml))/i,/^(?:(centilitres|centilitre|cl))/i,/^(?:(miles|mile|mi))/i,/^(?:(yards|yard|yd))/i,/^(?:(feet|foot|ft))/i,/^(?:(inches|inch|in))/i,/^(?:(grams|gram|g))/i,/^(?:(kilograms|kilogram|kg))/i,/^(?:(milligrams|milligram|mg))/i,/^(?:(centigrams|centigram|cg))/i,/^(?:(micrograms|microgram|mcg|μg))/i,/^(?:(pounds|pound|lb))/i,/^(?:(ounces|ounce))/i,/^(?:(tons|ton))/i,/^(?:(seconds|second|s))/i,/^(?:(milliseconds|millisecond|ms))/i,/^(?:(microseconds|microsecond|μs))/i,/^(?:(nanosecondsnanosecond|ns))/i,/^(?:(minutes|minute|min))/i,/^(?:(hours|hour|hr))/i,/^(?:(days|day))/i,/^(?:(weeks|week))/i,/^(?:(months|month))/i,/^(?:(years|year))/i,/^(?:(degrees|degree|°))/i,/^(?:(radians|radian|rad))/i,/^(?:(kelvin|degree kelvin|K))/i,/^(?:(degree celcius|celcius|centrigrade|°C))/i,/^(?:(fahrenheit|°F))/i,/^(?:(rupees|rs\.|rs|inr\.|inr))/i,/^(?:(rupee|re\.|re))/i,/^(?:(square|sq\.|sq))/i,/^(?:(cubic|cu\.|cu))/i,/^(?:AND|&)/i,/^(?:\/|\\|UPON|BY|OVER|OUT OF|PER)/i,/^(?:\*|x|×|TIMES|OF)/i,/^(?:-|MINUS)/i,/^(?:\+|PLUS)/i,/^(?:\^)/i,/^(?:!)/i,/^(?:\(|\[|\{)/i,/^(?:\)|\]|\})/i,/^(?:-)/i,/^(?:,)/i,/^(?:\.)/i,/^(?:PI)/i,/^(?:E)/i,/^(?:SQRT)/i,/^(?:%|PERCENT)/i,/^(?:$)/i,/^(?:.)/i,/^(?:.)/i],
conditions: {"INITIAL":{"rules":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89],"inclusive":true}}
};
return lexer;
})();
nParser.lexer = lexer;
function Parser () {
  this.yy = {};
}
Parser.prototype = nParser;nParser.Parser = Parser;
return new Parser;
})();


if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.nParser = nParser;
exports.Parser = nParser.Parser;
exports.parse = function () { return nParser.parse.apply(nParser, arguments); };
exports.main = function commonjsMain(args){if(!args[1]){console.log("Usage: "+args[0]+" FILE");process.exit(1)}var source=require("fs").readFileSync(require("path").normalize(args[1]),"utf8");return exports.nParser.parse(source)};
if (typeof module !== 'undefined' && require.main === module) {
  exports.main(process.argv.slice(1));
}
}