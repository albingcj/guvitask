$(document).ready(function () {
    $("#login").submit(function (e) {
        e.preventDefault();

        var form = $(this);

        $.ajax({
            type: "POST",
            url: "php/login.php",
            data: form.serialize(),
            success: function (res) {
                res = JSON.parse(res);
                // console.log(res);
                if (res.status == 200) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: res.message,
                    }).then(function () {

                        localStorage.setItem('userEmail', res.email);
                        localStorage.setItem('userPassword', res.password);

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
            error: function (xhr, status, error) {
                Swal.fire("Error!", "Could not process your request!", "error");
                console.log("Error:", error);
            },
        });
    });
});
