
function displayTable(tableData, theadId, tbodyId, tfootId) {
  tfootId = (typeof tfootId === "undefined") ? "" : tfootId; //tfootId is an optional parameter
  displayTableColumns(tableData.tcolumns, theadId);
  displayTableRows(tableData, tbodyId);
  if(tfootId != "") {
    displayTableColumns(tableData.tcolumns, tfootId);          
  }
}

function displayTableRows(tableData, tbodyId) {
  var r = new Array();
  var j = -1;
  for (var i = 0; i < tableData.trows.length; i++) {
    r[++j] = '<tr>' ;
    for(var k = 0; k < tableData.tcolumns.length; k++) {
      r[++j] = '<td>';
      r[++j] = tableData.trows[i][tableData.tcolumns[k].cid];
      r[++j] = '</td>';
    }
    r[++j] = '</tr>';
  };
  $("#"+tbodyId).html(r.join(''));
  return false;
}

function displayTableColumns(data, theadId) {
  // console.log("thead = " + theadId);
  //TODO Add support for classes?
  var r = new Array();
  var j = -1;
  r[++j] = '<tr>';
  for (var i = 0; i < data.length; i++) {
    r[++j] = '<th data-field="';
    r[++j] = data[i]['cid'];
    r[++j] = '">';
    r[++j] = data[i]['cname'];
    r[++j] = '</th>';
  };
  r[++j] = '</tr>';
  $("#"+theadId).html(r.join(''));
  return false;
}

function formatDate (input) {
  var datePart = input.match(/\d+/g),
  year = datePart[0], // get only two digits
  month = datePart[1], day = datePart[2];
  return day+'/'+month+'/'+year;
}

function formatDateForSubmit (input) {
  if(input == "") {
    return "";
  }
  
  var datePart = input.split("-"),
  day = datePart[0], // get only two digits
  month = datePart[1], 
  year = datePart[2];
  return year+'-'+month+'-'+day;
}

//Rename as format to display
function firstToUpperCase( str ) {
  str = str.replace(/([A-Z])/g, ' $1');
  return str.substr(0, 1).toUpperCase() + str.substr(1);
}

function getFromRequest(name){
   if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
      return decodeURIComponent(name[1]);
    else 
      return "";
}

function getValueFromSerializedArray(key, dataString) {
  for(var i=0; i<dataString.length; i++) {
    if(dataString[i].name == key) {
      return dataString[i].value;
    }
  }
  return "";
}

function getDate(offset) {
  offset = (typeof offset === "undefined") ? 0 : offset; 
  var d = new Date();
  d.setDate(d.getDate() + offset);
  var month = '' + (d.getMonth() + 1),
    day = '' + (d.getDate()),
    year = d.getFullYear();

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return [year, month, day].join('-');
}

function showModalMessage(msg) {
      var dialog = $( "#printLoading" ).dialog({
        autoOpen: true,
        height: 300,
        width: 300,
        modal: true,
        dialogClass: "no-close"
      });

      dialog.dialog( "open" );
  
}