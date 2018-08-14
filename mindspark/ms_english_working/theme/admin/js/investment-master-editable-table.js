var EditableTable = function () {

    return {

        //main function to initiate the module
        init: function () {
            function restoreRow(oTable, nRow) {
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);

                for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                    oTable.fnUpdate(aData[i], nRow, i, false);
                }

                oTable.fnDraw();
            }

            function editRow(oTable, nRow) {
                
                var aData = oTable.fnGetData(nRow);
                var jqTds = $('>td', nRow);
                var InvCatSelBox = getSelectedCategory(aData[0]);
                
                jqTds[0].innerHTML = InvCatSelBox;
                jqTds[1].innerHTML = '<input type="text" placeholder="Investment Type" name="investment_title" class="form-control small rowfield" value="' + aData[1] + '">';
                jqTds[2].innerHTML = '<input type="text" placeholder="Investment Description" name="investment_description" class="form-control small rowfield" value="' + aData[2] + '">';
                jqTds[3].innerHTML = '<a class="edit" href="">Save</a>';
                jqTds[4].innerHTML = '<a class="cancel" href="">Cancel</a>';
                jqTds[5].innerHTML = aData[5];
            }

            function saveRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 3, false);
                oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 4, false);
                oTable.fnUpdate('<input type="hidden" name="investment_type_id" class="rowfield" value="0">', nRow, 5, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
                oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 3, false);
                oTable.fnDraw();
            }

            function getSelectedCategory(selectedVal) {

                var invCatSelect = "";
                
                if(selectedVal == "" || selectedVal == undefined) {
                   selectedVal = "";  
                }
                
                invCatSelect = '<select class="form-control" name="investment_category"><option value="">Select Category</option>';
                if(selectedVal == "80C") {
                    invCatSelect += '<option value="80C" selected>80C</option>';
                } else {
                    invCatSelect += '<option value="80C">80C</option>';
                }
                if(selectedVal == "80D") {
                    invCatSelect += '<option value="80D" selected>80D</option>';
                } else {
                    invCatSelect += '<option value="80D">80D</option>';
                }
                if(selectedVal == "80E") {
                    invCatSelect += '<option value="80E" selected>80E</option>';
                } else {
                    invCatSelect += '<option value="80E">80E</option>';
                }
                if(selectedVal == "80G") {
                    invCatSelect += '<option value="80G" selected>80G</option>';
                } else {
                    invCatSelect += '<option value="80G">80G</option>';
                }
                if(selectedVal == "80U") {
                    invCatSelect += '<option value="80U" selected>80U</option>';
                } else {
                    invCatSelect += '<option value="80U">80U</option>';
                }
                invCatSelect += '</select>';

                return invCatSelect;
            }

            var oTable = $('#editable-sample').dataTable({
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                // set the initial value
                "iDisplayLength": 5,
                "sDom": "<'row'<'col-lg-6'l><'col-lg-6'f>r>t<'row'<'col-lg-6'i><'col-lg-6'p>>",
                "sPaginationType": "bootstrap",
                "oLanguage": {
                    "sLengthMenu": "_MENU_ records per page",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "aoColumnDefs": [{
                        'bSortable': true,
                        'aTargets': [0]
                    }
                ]
            });

            jQuery('#editable-sample_wrapper .dataTables_filter input').addClass("form-control medium"); // modify table search input
            jQuery('#editable-sample_wrapper .dataTables_length select').addClass("form-control xsmall"); // modify table per page dropdown

            var nEditing = null;

            $('#editable-sample_new').click(function (e) {
                e.preventDefault();
                var aiNew = oTable.fnAddData(['', '', '', '<a class="edit" href="">Edit</a>', '<a class="cancel" data-mode="new" href="">Cancel</a>', '<input type="hidden" name="investment_type_id" id="investment_type_id" class="rowfield" value="0">'
                ]);
                var nRow = oTable.fnGetNodes(aiNew[0]);
                editRow(oTable, nRow);
                nEditing = nRow;
            });

            $('#editable-sample a.delete').live('click', function (e) {
                e.preventDefault();

                if (confirm("Are you sure to delete this row ?") == false) {
                    return;
                }
                var nRow = $(this).parents('tr')[0];
                var jqHid = $(nRow).find('[name=investment_type_id]').val();
                
                $.post(window.location.pathname+'/delete_investment_records/'+jqHid,'',function(out){
                    if (out.indexOf("Error:") >= 0) {
                        $("#resulterr").show("slow");
                        $('#resulterr strong').html(out);
                    } else {
                        $("#result").show("slow");
                        $('#result strong').html(out);
                    }
                    setTimeout(function(){window.location.reload()},3000);
                });

                oTable.fnDeleteRow(nRow);
//                alert("Deleted! Do not forget to do some ajax to sync with backend :)");
            });

            $('#editable-sample a.cancel').live('click', function (e) {
                e.preventDefault();
                if ($(this).attr("data-mode") == "new") {
                    var nRow = $(this).parents('tr')[0];
                    oTable.fnDeleteRow(nRow);
                } else {
                    restoreRow(oTable, nEditing);
                    nEditing = null;
                }
            });

            $('#editable-sample a.edit').live('click', function (e) {
                e.preventDefault();

                /* Get the row as a parent of the link that was clicked on */
                var nRow = $(this).parents('tr')[0];
                if (nEditing !== null && nEditing != nRow) {
                    /* Currently editing - but not this row - restore the old before continuing to edit mode */
                    restoreRow(oTable, nEditing);
                    editRow(oTable, nRow);
                    nEditing = nRow;
                } else if (nEditing == nRow && this.innerHTML == "Save") {
                    
                    /* Editing this row and want to save it */

                    /*
                     * Collect Data from Text Boxes and check for validations
                     */
                    
                    var jqCname = $(nRow).find('[name=investment_title]').val();
                    var jqDesc = $(nRow).find('[name=investment_description]').val();
                    var jqInvCat = $(nRow).find('[name=investment_category]').val();
                    var jqHid = $(nRow).find('[name=investment_type_id]').val();
                    
                    if(jqCname == "" || /[^a-zA-Z0-9 \&\-\_]+/.test(jqCname))
                    {
                        $(nRow).find('[name=investment_title]').attr('style','border:1px solid #FF0000;');
                        $("#resulterr").show("slow");
                        setTimeout(function(){
                            $("#resulterr").hide("slow");
                            $('#resulterr strong').html('');
                            $(nRow).find('[name=investment_title]').attr('style','border:1px solid #C2C2C2;');
                        },5000);
                        return false;
                    }
                    else
                    {
                        $('#resulterr').html("Title should not contains special characters (i.e. #, $, %, !)");
                        /*
                         * Serialize Textbox Values and send in Post Request
                         */
                        var data = $.trim($(nRow).find('[name=investment_title]').serialize()) + '&' + $.trim($(nRow).find('[name=investment_description]').serialize()) + '&' + $.trim($(nRow).find('[name=investment_category]').serialize());
                        
                        $.post(window.location.pathname+'/manage_investment_records/'+jqHid,data,function(out){
                            if (out.indexOf("Error:") >= 0) {
                                $("#resulterr").show("slow");
                                $('#resulterr strong').html(out);
                            } else {
                                $("#result").show("slow");
                                $('#result strong').html(out);
                            }
                        });

                        saveRow(oTable, nEditing);
                        nEditing = null;
                        setTimeout(function(){window.location.reload()},3000);
                    }
//                    alert("Updated! Do not forget to do some ajax to sync with backend :)");
                } else {
                    /* No edit in progress - let's start one */
                    editRow(oTable, nRow);
                    nEditing = nRow;
                }
            });
        }

    };
    
    

}();

