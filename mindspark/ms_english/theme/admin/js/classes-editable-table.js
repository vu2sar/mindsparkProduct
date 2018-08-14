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
                jqTds[0].innerHTML = '<input type="text" name="class_name" class="form-control small rowfield" value="' + aData[0] + '">';
                jqTds[1].innerHTML = '<a class="edit" href="">Save</a>';
                jqTds[2].innerHTML = '<a class="cancel" href="">Cancel</a>';
                jqTds[3].innerHTML = aData[3];
            }

            function saveRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 1, false);
                oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 2, false);
                oTable.fnUpdate('<input type="hidden" name="hid_class_id" class="rowfield" value="0">', nRow, 3, false);
                oTable.fnDraw();
            }

            function cancelEditRow(oTable, nRow) {
                var jqInputs = $('input', nRow);
                oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
                oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 1, false);
                oTable.fnDraw();
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
                var aiNew = oTable.fnAddData(['', '<a class="edit" href="">Edit</a>', '<a class="cancel" data-mode="new" href="">Cancel</a>', '<input type="hidden" name="hid_class_id" class="rowfield" value="0">'
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
                var jqHid = $(nRow).find('[name=hid_class_id]').val();
                
                $.post(window.location.pathname+'/delete_class/'+jqHid,'',function(out){
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

                    var jqCname = $(nRow).find('[name=class_name]').val();
                    var jqHid = $(nRow).find('[name=hid_class_id]').val();
                    
                    if(jqCname == "" || /[^a-zA-Z 0-9]+/.test(jqCname))
                    {
                        $(nRow).find('[name=class_name]').attr('style','border:1px solid #FF0000;');
                        $("#resulterr").show("slow");
                        $('#resulterr strong').html("Class Name should only contains Alpha-Numeric and should not be blank.");
                        setTimeout(function(){
                            $("#resulterr").hide("slow");
                            $('#resulterr strong').html('');
                            $(nRow).find('[name=class_name]').attr('style','border:1px solid #C2C2C2;');
                        },5000);
                        return false;
                    }
                    else
                    {
                        $('#resulterr').html("");
                        var data = 'class_name='+$.trim(jqCname);

                        $.post(window.location.pathname+'/manage_class/'+jqHid,data,function(out){
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