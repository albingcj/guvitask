function toggleVisibility() {
    $('#updForm').toggleClass('d-none');
    $('#detTable').toggleClass('d-none');
}

function fetchPro() {
    $.ajax({
        type: 'GET',
        url: 'php/profile.php',
        dataType: 'json',
        success: function (response) {
            $('.viewName').text(response.name);
            $('.viewMail').text(response.mail);
            $('.viewNum').text(response.mobile_number);
            $('#viewAdd').text(response.address);
            $('#viewSta').text(response.state);
            $('#viewPin').text(response.pincode);
            $('#viewDob').text(response.date_of_birth);
            $('#viewGen').text(response.gender);
            $('#profileImage').attr('src', response.image);
            // console.log(response.image);

        },
        error: function (xhr, status, error) {
            console.error('Error fetching user profile:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please Login!',
            }).then(function () {
                window.location.href = "login.html";
            });

        },
        complete: function () {
        }
    });
}

$(document).ready(function () {
    fetchPro();
    $("#update").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        var formData = {
            action: 'update',
            profName: form.find('[name="profName"]').val(),
            profNum: form.find('[name="profNum"]').val(),
            profAdd: form.find('[name="profAdd"]').val(),
            profSta: form.find('[name="profSta"]').val(),
            profPin: form.find('[name="profPin"]').val(),
            profDate: form.find('[name="profDate"]').val(),
            profGen: form.find('[name="profGen"]').val()
        };
        $.ajax({
            type: "POST",
            url: "php/profile.php",
            // data: {
            //     action: 'update',
            //     formData: form.serialize()
            // },
            data : formData,
            success: function (res) {
                console.log(res);
                fetchPro();
                toggleVisibility();
            },
            error: function (xhr, status, error) {
                console.log("Error:", error);
                alert('Failed to update profile. Please try again.');
            }
        });
    });


});
$(document).ready(function () {
    $("#logout").click(function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out",
            icon: "warning",
            showCancelButton: true,
            cancelButtonColor: "#3085d6",
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'php/profile.php',
                    dataType: 'json',
                    data: { action: 'logout' },
                    success: function (res) {
                        // console.log(res.message);

                        // console.log(1 + localStorage.getItem('userEmail'));
                        // console.log(1 + localStorage.getItem('userPassword'));

                        localStorage.removeItem('userEmail');
                        localStorage.removeItem('userPassword');

                        // console.log(2 + localStorage.getItem('userEmail'));
                        // console.log(2 + localStorage.getItem('userPassword'));

                        window.location.href = 'index.html';
                    },
                    error: function (xhr, status, error) {
                        console.error('Error during logout:', error);
                    }
                });
            }
        });
    });
});

$(document).ready(function () {
    $("#changePass").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: "php/profile.php",
            data: {
                action: 'updatePass',
                currPass: $('#currPass').val(),
                updPass1: $('#updPass1').val(),
                updPass2: $('#updPass2').val(),
                formData: form.serialize()
            },
            success: function (res) {
                res = JSON.parse(res);
                console.log(res);
                console.log(res.message);
                if (res.status == 200) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                    }).then(function () {

                        localStorage.removeItem('userPassword');
                        localStorage.setItem('userPassword', res.password);
                        window.location.href = "profile.html";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: res.message,
                        text: "Try Again",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("Error:", error);
                alert('Failed to update profile. Please try again.');
            },
            complete: function () {
            }
        });
    });
});

$(document).ready(function () {

    $("#upload-img").submit(function (e) {
        e.preventDefault();

        var fileCollection = $("#image-upload")[0];
        var file = fileCollection.files[0];

        if (file) {
            var formData = new FormData();
            formData.append('profilepic', file);
            formData.append('action', 'updatePic');

            $.ajax({
                type: "POST",
                url: "php/profile.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function (res) {
                    console.log(res);
                    fetchPro();
                },
                error: function (xhr, status, error) {
                    console.log("Error:", error);
                    alert('Failed to update profile. Please try again.');
                },
                complete: function () {
                }
            });
        } else {
            Swal.fire({
                title: "Please select a file",
                text: "Please try again!",
                icon: "warning",
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Ok!"
            })
        }
    });

});
