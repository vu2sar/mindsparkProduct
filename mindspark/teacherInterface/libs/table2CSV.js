jQuery.fn.table2CSV = function(options) {
    var options = jQuery.extend({
        separator: ',',
        header: [],
        delivery: 'popup' // popup, value
    },
    options);

    var csvData = [];
    var headerArr = [];
    var el = this;

    //header
    var numCols = options.header.length;
    var tmpRow = []; // construct header avalible array

    if (numCols > 0) {
        for (var i = 0; i < numCols; i++) {
            tmpRow[tmpRow.length] = formatData(options.header[i]);
        }
    } else {
        $(el).filter(':visible').find('th').each(function() {
            if ($(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData($(this).html());
        });
    }

    row2CSV(tmpRow);

    // actual data
    $(el).find('tr').each(function() {
        var tmpRow = [];
        $(this).filter(':visible').find('td').each(function() {
            if ($(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData($(this).html());
        });
        row2CSV(tmpRow);
    });
    if (options.delivery == 'popup') {
        var mydata = csvData.join('\n');
        return saveAsCSVFile(mydata);
    } else {
        var mydata = csvData.join('\n');
        return mydata;
    }

    function row2CSV(tmpRow) {
        var tmp = tmpRow.join('') // to remove any blank rows
        // alert(tmp);
        if (tmpRow.length > 0 && tmp != '') {
            var mystr = tmpRow.join(options.separator);
            csvData[csvData.length] = mystr;
        }
    }
    function formatData(input) {
        // replace " with “
        var regexp = new RegExp(/["]/g);
        var output = input.replace(regexp, "“");
        //HTML
        var regexp = new RegExp(/\<[^\<]+\>/g);
        var output = output.replace(regexp, "");
        if (output == "") return '';
        return '"' + output + '"';
    }
    function popup(data) {
        var generator = window.open('', 'csv', 'height=400,width=600');
        generator.document.write('<html><head><title>CSV</title>');
        generator.document.write('</head><body >');
        generator.document.write('<textArea cols=70 rows=15 wrap="off" >');
        generator.document.write(data);
        generator.document.write('</textArea>');
        generator.document.write('</body></html>');
        generator.document.close();
        return true;
    }

    function saveAsCSVFile(textToWrite)
    {      

    //  create a new Blob (html5 magic) that conatins the data from your form feild
        var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
    // Specify the name of the file to be saved
        var fileNameToSaveAs = "topic-report.csv";
        
    // Optionally allow the user to choose a file name by providing 
    // an imput field in the HTML and using the collected data here
    // var fileNameToSaveAs = txtFileName.text;

    // create a link for our script to 'click'
        var downloadLink = document.createElement("a");
    //  supply the name of the file (from the var above).
    // you could create the name here but using a var
    // allows more flexability later.
        downloadLink.download = fileNameToSaveAs;
    // provide text for the link. This will be hidden so you
    // can actually use anything you want.
        downloadLink.innerHTML = "My Hidden Link";
        
    // allow our code to work in webkit & Gecko based browsers
    // without the need for a if / else block.
        window.URL = window.URL || window.webkitURL;
              
    // Create the link Object.
        downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
    // when link is clicked call a function to remove it from
    // the DOM in case user wants to save a second file.
        downloadLink.onclick = destroyClickedElement;
    // make sure the link is hidden.
        downloadLink.style.display = "none";
    // add the link to the DOM
        document.body.appendChild(downloadLink);
        
    // click the new link
        downloadLink.click();
    }

    function destroyClickedElement(event)
    {
    // remove the link from the DOM
        document.body.removeChild(event.target);
    }
};