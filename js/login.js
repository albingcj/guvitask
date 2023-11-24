// $(document).ready(function () {
//     $('#login').submit(function (e) {
//         e.preventDefault(); // Prevent the form from submitting in the traditional way

//         // Get form data
//         var formData = {
//             userEmail: $('input[name=userEmail]').val(),
//             userPwd: $('input[name=userPwd]').val()
//         };

//         // Send the data to login.php for processing
//         $.ajax({
//             type: 'POST',
//             url: 'login.php',
//             data: formData,
//             dataType: 'json', // Expect JSON response
//             success: function (response) {
//                 if (response.success) {
//                     // Redirect to a success page or perform other actions
//                     window.location.href = 'dashboard.html';
//                 } else {
//                     // Display an error message
//                     alert('Login failed. Please check your credentials.');
//                 }
//             },
//             error: function (error) {
//                 console.error('Error during login:', error);
//             }
//         });
//     });
// });
$("#login").submit(function (e) {
    e.preventDefault();

    var form = $(this);

    $.ajax({
        type: "POST",
        url: "php/login.php",
        data: form.serialize(), // Serialize the form data
        dataType: 'json', // Expect JSON response
        success: function (res) {
            // res = JSON.parse(res);
            console.log(res);
            if (res.status == 200) {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: res.message,
                }).then(function () {
                    window.location.href = "profile.html";
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: res.message,
                    text: "Login Failed",
                });
            }
        },
        error: function (error) {
            // Handle error
            console.error('Error:', error);

            // Optionally, update your UI to indicate an error
            // For example, display an error message to the user
        }
    });
});
