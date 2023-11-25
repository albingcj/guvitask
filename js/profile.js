function toggleVisibility() {
    $('#updForm').toggleClass('d-none');
    $('#detTable').toggleClass('d-none');
}

function fetchPro() {
    $.ajax({
        type: 'GET',
        url: 'php/profile.php',
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (response) {
            $('.viewName').text(response.name);
            $('.viewMail').text(response.mail);
            $('.viewNum').text(response.mobile_number);
            $('#viewAdd').text(response.address);
            $('#viewSta').text(response.state);
            $('#viewPin').text(response.pincode);
            $('#viewDob').text(response.date_of_birth);
            $('#viewGen').text(response.gender);
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
        $.ajax({
            type: "POST",
            url: "php/profile.php",
            data: form.serialize(),
            success: function (res) {
                console.log(res);
                fetchPro();
                toggleVisibility();
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
    $("#logout").click(function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: 'php/profile.php', // Adjust the path based on your file structure
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
