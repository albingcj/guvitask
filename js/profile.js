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
            // Show loading spinner or other loading indicator
        },
        success: function (response) {
            // Update HTML elements with retrieved data
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

            // Handle specific error cases
            if (status === 'timeout') {
                alert('The request timed out. Please try again.');
            } else if (status === 'error' && xhr.status === 500) {
                alert('Internal server error. Please try again later.');
            } else {
                alert('An error occurred. Please try again.');
            }
        },
        complete: function () {
            // Hide loading spinner or other loading indicator
        }
    });
}

$(document).ready(function () {
    // profile update part
    fetchPro();

    $("#update").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: "php/profile.php", // Use consistent URL
            data: form.serialize(),
            success: function (res) {
                console.log(res);
                // Reload the detTable with updated data
                fetchPro();
                // Handle the response accordingly
            },
            error: function (xhr, status, error) {
                console.log("Error:", error);
                // Handle specific error cases
                alert('Failed to update profile. Please try again.');
            },
            complete: function () {
                // Hide loading spinner or other loading indicator
            }
        });
    });

    // logout part
    $("#logout").submit(function (e){
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "php/logout.php",
            success: function (res) {
                res = JSON.parse(res);
                console.log(res);
                if (res.status == 200) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                    }).then(function () {
                        // Clear local storage
                        localStorage.clear();

                        // Redirect to the login page
                        window.location.href = "login.html";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: res.message,
                        text: "Logout Failed",
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire("Error!", "Could not process your request!", "error");
                console.log("Error:", error);
            },
        });
    })
});
