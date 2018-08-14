//updated version 1.1 - 
//Urmi - updated on 10/3/2014- added negative number checking for blanks e.g. blank1 = -1 wasn't supported earlier
//Also added few alerts to check for condition validity.

var MAX_ATTEMPT = 10;
var varValues = Array();
var ano = 1;

var parser = (function(){
var parser = {trace: function trace(){},
yy: {},
inputValues: function inputValues(input,attemptNo){varValues = input.split("|"); ano = attemptNo; if(ano > MAX_ATTEMPT) throw new Error("Attempt number has reached its limit")},
symbols_: {"error":2,"expressions":3,"conditions":4,"EOF":5,"AND":6,"OR":7,"(":8,")":9,"condition":10,"NUMBER":11,"var":12,"=":13,"VAL":14,"!=":15,">":16,"<":17,"attemptno":18,"CONTAINS":19,"NOT_CONTAINS":20,"len":21,"range":22,"round":23,"paren":24,"BLANK":25,"ATTEMPTNO":26,"LENGTH":27,"RANGE":28,",":29,"ROUND":30,"PARENCHECK":31,"$accept":0,"$end":1},
terminals_: {2:"error",5:"EOF",6:"AND",7:"OR",8:"(",9:")",11:"NUMBER",13:"=",14:"VAL",15:"!=",16:">",17:"<",19:"CONTAINS",20:"NOT_CONTAINS",25:"BLANK",26:"ATTEMPTNO",27:"LENGTH",28:"RANGE",29:",",30:"ROUND",31:"PARENCHECK"},
productions_: [0,[3,2],[4,3],[4,3],[4,3],[4,1],[4,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[10,3],[12,1],[18,1],[21,4],[22,8],[23,6],[24,4]],
performAction: function anonymous(yytext, yyleng, yylineno, yy, yystate /* action[1] */, $$ /* vstack */, _$ /* lstack */
/**/) {
/* this == yyval */

var $0 = $$.length - 1;
switch (yystate) {
case 1:return $$[$0-1];
break;
case 2:this.$ = $$[$0-2] && $$[$0];
break;
case 3:this.$ = $$[$0-2] || $$[$0];
break;
case 4:this.$ = $$[$0-1];
break;
case 5:this.$ = $$[$0];
break;
case 6:var num = Number($$[$0-1]); if(num == 1) this.$ = 1; else if(num == 0) this.$ = 0;
break;
case 7:
		var blank = $$[$0-2];
        var str = $$[$0];
        var val = str.slice(1,str.length-1);
        this.$ = (blank.toUpperCase() == val.toUpperCase())?1:0;
       
break;
case 8:	
		var blank = $$[$0-2];
        var str = $$[$0];
        var val = str.slice(1,str.length-1);
        this.$ = (blank.toUpperCase() == val.toUpperCase())?0:1;
       
break;
case 9:this.$ = (Number($$[$0-2]) == Number($$[$0]))?1:0;
break;
case 10:this.$ = (Number($$[$0-2]) == Number($$[$0]))? 0:1;
break;
case 11:this.$ = (Number($$[$0-2]) > Number($$[$0]))? 1: 0;
break;
case 12:this.$ = (Number($$[$0-2]) < Number($$[$0]))? 1: 0;
break;
case 13:this.$ = ($$[$0-2] == Number($$[$0]))?1:0;
if(Number($$[$0]) > MAX_ATTEMPT || Number($$[$0])<0 || Number($$[$0])%1 != 0) throw new Error("attemptNo should be a whole number between 1 and "+MAX_ATTEMPT+".");
break;
case 14:this.$ = ($$[$0-2] == Number($$[$0]))? 0:1;
if(Number($$[$0]) > MAX_ATTEMPT || Number($$[$0])<0 || Number($$[$0])%1 != 0) throw new Error("This condition will always be true! Note that attemptNo can only be a whole number between 1 and "+MAX_ATTEMPT

+".");
break;
case 15:this.$ = (Number($$[$0-2]) > Number($$[$0]))? 1: 0;
        if(Number($$[$0]) > MAX_ATTEMPT) throw new Error("'attemptNo > "+Number($$[$0])+"' will never be true as "+MAX_ATTEMPT+" is the maximum number of attempts possible.");
break;
case 16:this.$ = (Number($$[$0-2]) < Number($$[$0]))? 1: 0;
if(Number($$[$0]) <= 1) throw new Error("'attemptNo < "+Number($$[$0])+"' will never be true as is the minimum number of attempts is 1.");
break;
case 17:
         var str = $$[$0-2]; 
         var str1 = $$[$0];
         var val = str1.slice(1,str1.length-1);
         val = val.replace(/\\/,"\\\\");
         val = val.replace(/\[/,"\\\[");
         val = val.replace(/\]/,"\\\]");
         val = val.replace(/\(/,"\\\(");
         val = val.replace(/\)/,"\\\)");
         val = val.replace(/\{/,"\\\{");
         val = val.replace(/\}/,"\\\}");
         val = val.replace(/\./,"\\\.");
         val = val.replace(/\+/,"\\\+");
         val = val.replace(/\-/,"\\\-");
         val = val.replace(/\*/,"\\\*");
         val = val.replace(/\?/,"\\\?");
         val = val.replace(/\^/,"\\\^");
         val = val.replace(/\|/,"\\\|");
         val = val.replace(/\$/,"\\\$");

         var result = str.toUpperCase().search(val.toUpperCase());
         this.$ = (result == -1)?0:1;
         
break;
case 18:
         var str = $$[$0-2]; 
         var str1 = $$[$0];
         var val = str1.slice(1,str1.length-1);
         val = val.replace(/\\/,"\\\\");
         val = val.replace(/\[/,"\\\[");
         val = val.replace(/\]/,"\\\]");
         val = val.replace(/\(/,"\\\(");
         val = val.replace(/\)/,"\\\)");
         val = val.replace(/\{/,"\\\{");
         val = val.replace(/\}/,"\\\}");
         val = val.replace(/\./,"\\\.");
         val = val.replace(/\+/,"\\\+");
         val = val.replace(/\-/,"\\\-");
         val = val.replace(/\*/,"\\\*");
         val = val.replace(/\?/,"\\\?");
         val = val.replace(/\^/,"\\\^");
         val = val.replace(/\|/,"\\\|");
         val = val.replace(/\$/,"\\\$");
         var result = str.toUpperCase().search(val.toUpperCase());
         this.$ = (result == -1)?1:0;
         
break;
case 19:this.$ = ($$[$0-2] == Number($$[$0]))?1:0;
break;
case 20:this.$ = ($$[$0-2] == Number($$[$0]))?0:1;
break;
case 21:this.$ = ($$[$0-2] > Number($$[$0]))?1:0;
break;
case 22:this.$ = ($$[$0-2] < Number($$[$0]))?1:0;
break;
case 23:this.$ = ($$[$0-2] == Number($$[$0]))?1:0;
       if(Number($$[$0]) != 0 && Number($$[$0]) != 1) throw new Error("range() will return only 0 or 1."); 
break;
case 24:this.$ = ($$[$0-2] == Number($$[$0]))?0:1;
        if(Number($$[$0]) != 0 && Number($$[$0]) != 1) throw new Error("range() will return only 0 or 1."); 
break;
case 25:this.$ = ($$[$0-2] == Number($$[$0]))?1:0;
break;
case 26:this.$ = ($$[$0-2] == Number($$[$0]))?0:1;
break;
case 27:this.$ = ($$[$0-2] == Number($$[$0]))?1:0;
        if(Number($$[$0]) != 0 && Number($$[$0]) != 1) throw new Error("parenCheck() will return only 0 or 1."); 
break;
case 28:this.$ = ($$[$0-2] == Number($$[$0]))?0:1;
        if(Number($$[$0]) != 0 && Number($$[$0]) != 1) throw new Error("parenCheck() will return only 0 or 1."); 
break;
case 29:
        var str = $$[$0];
        var n = Number(str.charAt(str.length-1)) - 1;
        if(typeof varValues[n] == 'undefined')
         throw new Error("Blank number "+(n+1)+" does not exist in the question!");
        else
        this.$ = varValues[n];
      
break;
case 30:this.$ = ano;
break;
case 31:
          var str = $$[$0-1];
          this.$ = Number(str.length);
        
break;
case 32:
          var num = Number($$[$0-5]);
          var correct = Number($$[$0-3]);
          var tol = Number($$[$0-1]);
          if((num <= correct+tol) && (num >= correct-tol))
            this.$ = 1;
          else
            this.$ = 0;
        
break;
case 33:
          var str = Number($$[$0-3]);
          var value = Math.pow(10,Number($$[$0-1]));
          if(Number($$[$0-1]) %1 != 0)
           throw new Error("Invalid 2nd argument in the function round()! Enter a whole number.");
          else
          this.$ = Math.round(Number(str)*value)/value;
        
break;
case 34:
          var str = $$[$0-1];
          var p = 0;
          for(var i = 0; i < str.length; i++)
           {
              if(str[i] == "(")
                p++;
              else if (str[i] == ")")
                p--;
              
              if(p < 0)
                break;
           }
          if(p==0) 
            this.$ = 1;
          else
            this.$ = 0;
        
break;
}
},
table: [{3:1,4:2,8:[1,3],10:4,12:5,18:6,21:7,22:8,23:9,24:10,25:[1,11],26:[1,12],27:[1,13],28:[1,14],30:[1,15],31:[1,16]},{1:[3]},{5:[1,17],6:[1,18],7:[1,19]},{4:20,8:[1,3],10:4,11:[1,21],12:5,18:6,21:7,22:8,23:9,24:10,25:[1,11],26:[1,12],27:[1,13],28:[1,14],30:[1,15],31:[1,16]},{5:[2,5],6:[2,5],7:[2,5],9:[2,5]},{13:[1,22],15:[1,23],16:[1,24],17:[1,25],19:[1,26],20:[1,27]},{13:[1,28],15:[1,29],16:[1,30],17:[1,31]},{13:[1,32],15:[1,33],16:[1,34],17:[1,35]},{13:[1,36],15:[1,37]},{13:[1,38],15:[1,39]},{13:[1,40],15:[1,41]},{9:[2,29],13:[2,29],15:[2,29],16:[2,29],17:[2,29],19:[2,29],20:[2,29],29:[2,29]},{13:[2,30],15:[2,30],16:[2,30],17:[2,30]},{8:[1,42]},{8:[1,43]},{8:[1,44]},{8:[1,45]},{1:[2,1]},{4:46,8:[1,3],10:4,12:5,18:6,21:7,22:8,23:9,24:10,25:[1,11],26:[1,12],27:[1,13],28:[1,14],30:[1,15],31:[1,16]},{4:47,8:[1,3],10:4,12:5,18:6,21:7,22:8,23:9,24:10,25:[1,11],26:[1,12],27:[1,13],28:[1,14],30:[1,15],31:[1,16]},{6:[1,18],7:[1,19],9:[1,48]},{9:[1,49]},{11:[1,51],14:[1,50]},{11:[1,53],14:[1,52]},{11:[1,54]},{11:[1,55]},{14:[1,56]},{14:[1,57]},{11:[1,58]},{11:[1,59]},{11:[1,60]},{11:[1,61]},{11:[1,62]},{11:[1,63]},{11:[1,64]},{11:[1,65]},{11:[1,66]},{11:[1,67]},{11:[1,68]},{11:[1,69]},{11:[1,70]},{11:[1,71]},{12:72,25:[1,11]},{12:73,25:[1,11]},{12:74,25:[1,11]},{12:75,25:[1,11]},{5:[2,2],6:[2,2],7:[2,2],9:[2,2]},{5:[2,3],6:[2,3],7:[2,3],9:[2,3]},{5:[2,4],6:[2,4],7:[2,4],9:[2,4]},{5:[2,6],6:[2,6],7:[2,6],9:[2,6]},{5:[2,7],6:[2,7],7:[2,7],9:[2,7]},{5:[2,9],6:[2,9],7:[2,9],9:[2,9]},{5:[2,8],6:[2,8],7:[2,8],9:[2,8]},{5:[2,10],6:[2,10],7:[2,10],9:[2,10]},{5:[2,11],6:[2,11],7:[2,11],9:[2,11]},{5:[2,12],6:[2,12],7:[2,12],9:[2,12]},{5:[2,17],6:[2,17],7:[2,17],9:[2,17]},{5:[2,18],6:[2,18],7:[2,18],9:[2,18]},{5:[2,13],6:[2,13],7:[2,13],9:[2,13]},{5:[2,14],6:[2,14],7:[2,14],9:[2,14]},{5:[2,15],6:[2,15],7:[2,15],9:[2,15]},{5:[2,16],6:[2,16],7:[2,16],9:[2,16]},{5:[2,19],6:[2,19],7:[2,19],9:[2,19]},{5:[2,20],6:[2,20],7:[2,20],9:[2,20]},{5:[2,21],6:[2,21],7:[2,21],9:[2,21]},{5:[2,22],6:[2,22],7:[2,22],9:[2,22]},{5:[2,23],6:[2,23],7:[2,23],9:[2,23]},{5:[2,24],6:[2,24],7:[2,24],9:[2,24]},{5:[2,25],6:[2,25],7:[2,25],9:[2,25]},{5:[2,26],6:[2,26],7:[2,26],9:[2,26]},{5:[2,27],6:[2,27],7:[2,27],9:[2,27]},{5:[2,28],6:[2,28],7:[2,28],9:[2,28]},{9:[1,76]},{29:[1,77]},{29:[1,78]},{9:[1,79]},{13:[2,31],15:[2,31],16:[2,31],17:[2,31]},{11:[1,80]},{11:[1,81]},{13:[2,34],15:[2,34]},{29:[1,82]},{9:[1,83]},{11:[1,84]},{13:[2,33],15:[2,33]},{9:[1,85]},{13:[2,32],15:[2,32]}],
defaultActions: {17:[2,1]},
parseError: function parseError(str,hash){if(hash.recoverable){this.trace(str)}else{throw new Error(str)}},
parse: function parse(input) {
    var self = this, stack = [0], vstack = [null], lstack = [], table = this.table, yytext = '', yylineno = 0, yyleng = 0, recovering = 0, TERROR = 2, EOF = 1;
    var args = lstack.slice.call(arguments, 1);
    this.lexer.setInput(input);
    this.lexer.yy = this.yy;
    this.yy.lexer = this.lexer;
    this.yy.parser = this;
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

parseError:function parseError(str,hash){if(this.yy.parser){this.yy.parser.parseError(str,hash)}else{throw new Error(str)}},

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
case 1:return 25
break;
case 2:return 26
break;
case 3:return 11
break;
case 4:return 14
break;
case 5:return 13
break;
case 6:return 15
break;
case 7:return 16
break;
case 8:return 17
break;
case 9:return 8
break;
case 10:return 9
break;
case 11:return 29
break;
case 12:return 19
break;
case 13:return 20
break;
case 14:return 6
break;
case 15:return 7
break;
case 16:return 27
break;
case 17:return 28
break;
case 18:return 30
break;
case 19:return 31
break;
case 20:return 5
break;
case 21:return 'INVALID'
break;
case 22:console.log(yy_.yytext);
break;
}
},
rules: [/^(?:\s+)/i,/^(?:blank[1-6])/i,/^(?:attemptNo)/i,/^(?:(-)?[0-9]+(\.[0-9]+)?\b)/i,/^(?:(")[^\"]*("))/i,/^(?:=)/i,/^(?:!=)/i,/^(?:>)/i,/^(?:<)/i,/^(?:\()/i,/^(?:\))/i,/^(?:,)/i,/^(?:CONTAINS)/i,/^(?:NOT_CONTAINS)/i,/^(?:AND)/i,/^(?:OR)/i,/^(?:LENGTH)/i,/^(?:RANGE)/i,/^(?:ROUND)/i,/^(?:PARENCHECK)/i,/^(?:$)/i,/^(?:.)/i,/^(?:.)/i],
conditions: {"INITIAL":{"rules":[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22],"inclusive":true}}
};
return lexer;
})();
parser.lexer = lexer;
function Parser () {
  this.yy = {};
}
Parser.prototype = parser;parser.Parser = Parser;
return new Parser;
})();


if (typeof require !== 'undefined' && typeof exports !== 'undefined') {
exports.parser = parser;
exports.Parser = parser.Parser;
exports.parse = function () { return parser.parse.apply(parser, arguments); };
exports.main = function commonjsMain(args){if(!args[1]){console.log("Usage: "+args[0]+" FILE");process.exit(1)}var source=require("fs").readFileSync(require("path").normalize(args[1]),"utf8");return exports.parser.parse(source)};
if (typeof module !== 'undefined' && require.main === module) {
  exports.main(process.argv.slice(1));
}
}