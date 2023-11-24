function toggleVisibility() {
    $('#updForm').toggleClass('d-none');
    $('#detTable').toggleClass('d-none');
}


function fetchUserProfile() {
    // Assuming you have a PHP script to handle the Ajax request (e.g., fetch_profile.php)
    $.ajax({
        type: 'GET',
        url: 'php/profile.php',
        dataType: 'json', // Expect JSON response
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
        error: function (error) {
            console.error('Error fetching user profile:', error);
        }
    });
}



$(document).ready(function () {


    fetchUserProfile();

    $("#update").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: "php/profile.php",
            data: form.serialize(),
            success: function (res) {
                console.log(res);
                //reload the detTable with updated data
                fetchUserProfile();
                // Handle the response accordingly
            },
            error: function (xhr, status, error) {
                console.log("Error:", error);
            },
        });
    });
});

