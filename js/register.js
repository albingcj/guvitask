$(document).ready(function () {
    if (localStorage.getItem('userEmail')) {
        window.location.href = "profile.html";
    }
});
$(document).ready(function () {
    $("#registerForm").submit(function (e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: "php/register.php",
            data: form.serialize(),
            success: function (res) {
                // console.log(res);
                res = JSON.parse(res);
                // console.log(res);
                if (res.status == 200) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                    }).then(function () {
                        window.location.href = "login.html";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: res.message,
                        text: "Registration Failed",
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire("Error!", "Could not process your request!", "error");
                console.log("Error:", error);
            },
        });
    });
});
