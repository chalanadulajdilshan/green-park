jQuery(document).ready(function () {
    // Create Service Type
    $("#create").click(function (event) {
        event.preventDefault();

        if (!$("#code").val() || $("#code").val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter a Ref No",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        } else if (!$("#name").val() || $("#name").val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter a Service Name",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        } else if (!$("#price").val() || $("#price").val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter a Price",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        } else {
            $(".someBlock").preloader();

            var formData = new FormData($("#form-data")[0]);
            formData.append("create", true);

            $.ajax({
                url: "ajax/php/service-type.php",
                type: "POST",
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    $(".someBlock").preloader("remove");

                    if (result.status === "success") {
                        swal({
                            title: "Success!",
                            text: "Service Type added Successfully!",
                            type: "success",
                            timer: 2000,
                            showConfirmButton: false,
                        });

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    } else if (result.status === "error") {
                        swal({
                            title: "Error!",
                            text: "Something went wrong.",
                            type: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                },
            });
        }
        return false;
    });

    // Update Service Type
    $("#update").click(function (event) {
        event.preventDefault();

        if (!$("#code").val() || $("#code").val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter a Ref No",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        } else if (!$("#name").val() || $("#name").val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter a Service Name",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        } else if (!$("#price").val() || $("#price").val().length === 0) {
            swal({
                title: "Error!",
                text: "Please enter a Price",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        } else {
            $(".someBlock").preloader();

            var formData = new FormData($("#form-data")[0]);
            formData.append("update", true);

            $.ajax({
                url: "ajax/php/service-type.php",
                type: "POST",
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function (result) {
                    $(".someBlock").preloader("remove");

                    if (result.status == "success") {
                        swal({
                            title: "Success!",
                            text: "Service Type updated Successfully!",
                            type: "success",
                            timer: 2500,
                            showConfirmButton: false,
                        });

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    } else if (result.status === "error") {
                        swal({
                            title: "Error!",
                            text: "Something went wrong.",
                            type: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                },
            });
        }
        return false;
    });

    // Delete Service Type
    $(document).on("click", ".delete-service-type", function (e) {
        e.preventDefault();

        var id = $("#id").val();
        var name = $("#name").val();

        if (!name || name === "") {
            swal({
                title: "Error!",
                text: "Please select a Service Type first.",
                type: "error",
                timer: 2000,
                showConfirmButton: false,
            });
            return;
        }

        swal(
            {
                title: "Are you sure?",
                text: "Do you want to delete '" + name + "' Service Type?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                closeOnConfirm: false,
            },
            function (isConfirm) {
                if (isConfirm) {
                    $(".someBlock").preloader();

                    $.ajax({
                        url: "ajax/php/service-type.php",
                        type: "POST",
                        data: {
                            id: id,
                            delete: true,
                        },
                        dataType: "json",
                        success: function (response) {
                            $(".someBlock").preloader("remove");

                            if (response.status === "success") {
                                swal({
                                    title: "Deleted!",
                                    text: "Service Type has been deleted.",
                                    type: "success",
                                    timer: 2000,
                                    showConfirmButton: false,
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
                                    showConfirmButton: false,
                                });
                            }
                        },
                    });
                }
            }
        );
    });

    // New button - reset form
    $("#new").click(function (e) {
        e.preventDefault();
        $("#form-data")[0].reset();
        $("#id").prop("selectedIndex", 0);
        $("#create").show();
        $("#update").hide();
    });

    // Modal click - select service type
    $(document).on("click", ".select-service", function () {
        $("#id").val($(this).data("id"));
        $("#code").val($(this).data("code"));
        $("#name").val($(this).data("name"));
        $("#price").val($(this).data("price"));

        $("#create").hide();
        $("#update").show();
        $(".bs-example-modal-xl").modal("hide");
    });
});
