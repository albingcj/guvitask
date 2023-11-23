$(document).ready(function () {
    $("#registerForm").submit(function (e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: "POST",
            url: "php/register.php",
            data: form.serialize(),
            success: function (res) {
                console.log(res);
                try {
                    res = JSON.parse(res);
                    console.log(res);
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
                } catch (error) {
                    console.log("Error parsing JSON:", error);
                    Swal.fire("Error!", "Could not process your request!", "error");
                }
            },
            error: function (xhr, status, error) {
                Swal.fire("Error!", "Could not process your request!", "error");
                console.log("Error:", error);
            },
        });
    });
});
