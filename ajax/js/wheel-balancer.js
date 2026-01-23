jQuery(document).ready(function () {

    // Create Wheel Balancer
    $("#create").click(function (event) {
        event.preventDefault();

        if (!$('#name').val() || $('#name').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter wheel balancer name",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });

        } else {
            $('.someBlock').preloader();

            var formData = new FormData($("#form-data")[0]);
            formData.append('create', true);

            $.ajax({
                url: "ajax/php/wheel-balancer.php",
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    $('.someBlock').preloader('remove');

                    if (result.status === 'success') {
                        swal({
                            title: "Success!",
                            text: "Wheel Balancer added successfully!",
                            type: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 2000);

                    } else {
                        swal({
                            title: "Error!",
                            text: "Something went wrong.",
                            type: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }
        return false;
    });

    // Update Wheel Balancer
    $("#update").click(function (event) {
        event.preventDefault();

        if (!$('#name').val() || $('#name').val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter wheel balancer name",
                type: 'error',
                timer: 2000,
                showConfirmButton: false
            });

        } else {
            $('.someBlock').preloader();

            var formData = new FormData($("#form-data")[0]);
            formData.append('update', true);

            $.ajax({
                url: "ajax/php/wheel-balancer.php",
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function (result) {
                    $('.someBlock').preloader('remove');

                    if (result.status == 'success') {
                        swal({
                            title: "Success!",
                            text: "Wheel Balancer updated successfully!",
                            type: 'success',
                            timer: 2500,
                            showConfirmButton: false
                        });

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 2000);

                    } else {
                        swal({
                            title: "Error!",
                            text: "Something went wrong.",
                            type: 'error',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }
        return false;
    });

    // Reset Form
    $("#new").click(function (e) {
        e.preventDefault();
        $('#form-data')[0].reset();
        $("#create").show();
        $("#update").hide();
        // Re-generate the next code based on current last ID if possible or just reload
        window.location.reload();
    });

    // Select from Modal
    $(document).on('click', '.select-wheel-balancer', function () {
        $('#wheel_balancer_id').val($(this).data('id'));
        $('#code').val($(this).data('code'));
        $('#name').val($(this).data('name'));
        $('#remark').val($(this).data('remark'));

        if ($(this).data('status') == 1) {
            $('#is_active').prop('checked', true);
        } else {
            $('#is_active').prop('checked', false);
        }

        $("#create").hide();
        $("#update").show();
        $('#wheelBalancerModal').modal('hide');
    });

    // Delete Wheel Balancer
    $(document).on('click', '.delete-wheel-balancer', function (e) {
        e.preventDefault();

        var id = $('#wheel_balancer_id').val();
        var name = $('#name').val();

        if (!id || id === "") {
            swal({
                title: "Error!",
                text: "Please select a wheel balancer first.",
                type: "error",
                timer: 2000,
                showConfirmButton: false
            });
            return;
        }

        swal({
            title: "Are you sure?",
            text: "Do you want to delete wheel balancer '" + name + "'?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function (isConfirm) {
            if (isConfirm) {
                $('.someBlock').preloader();

                $.ajax({
                    url: 'ajax/php/wheel-balancer.php',
                    type: 'POST',
                    data: { id: id, delete: true },
                    dataType: 'JSON',
                    success: function (response) {
                        $('.someBlock').preloader('remove');

                        if (response.status === 'success') {
                            swal({
                                title: "Deleted!",
                                text: "Wheel Balancer has been deleted.",
                                type: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            swal({
                                title: "Error!",
                                text: "Something went wrong.",
                                type: "error",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    }
                });
            }
        });
    });

});
