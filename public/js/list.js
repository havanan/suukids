"use_strict"
$(document).ready(function() {

    // BOOTSTRAP TABLE - LIST
    // =================================================================
    // Require Bootstrap Table
    // =================================================================
    var $table = $('.table-list'),
        $remove = $('#btn-delete-row');

    $table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function() {
        $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
    });

    var $titleDelete = $table.data("delete-title");
    var $messageDelete = $table.data("delete-message");
    var $successMessageDelete = $table.data("delete-success-message");
    var $urlRemove = $table.data("url-delete");
    $remove.click(function() {
        postDataAjax('remove');
    });
    var postDataAjax = function($type) {
        $title = "";
        $message = "";
        $url = "";
        $successMessage = "";
        switch ($type) {
            case 'remove':
                $title = $titleDelete;
                $message = $messageDelete;
                $url = $urlRemove;
                $successMessage = $successMessageDelete;
                break;
        }
        Swal.fire({
                title: $title,
                text: $message,
                icon: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, do it!",
                closeOnConfirm: false
            },
            function() {
                var ids = $.map($table.bootstrapTable('getSelections'), function(row) {
                    return parseInt(row.id)
                });
                if ($url === undefined || $url === "") {
                    swal("Error!", "Some error have been detected on the server.", "error");
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: $url,
                    data: {
                        _token: window.Laravel.csrfToken,
                        multiple_id: ids,
                        post_id: (postId) ? postId : null,
                        user_id: (userId) ? userId : null,
                    },
                    success: function(response) {
                        disabledAllAction();
                        if ($type === "remove") {
                            $table.bootstrapTable('remove', {
                                field: 'id',
                                values: ids
                            });
                        }
                        $table.bootstrapTable('refresh');
                        swal("Successful!", $successMessage, "success");
                    },
                    error: function(e) {
                        console.log(e);
                        swal("Error!", "Some error have been detected on the server.", "error");
                        return;
                    }
                });
            });
    }

    $('#export').click(function() {
        $table.tableExport({
            type: 'csv',
            escape: false,
            exportDataType: 'all',
            refreshOptions: {
                exportDataType: 'all'
            }
        });
    });
    var disabledAllAction = function() {
        $remove.prop('disabled', true);
    }

});


//# sourceMappingURL=list.js.map
