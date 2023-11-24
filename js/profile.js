function toggleVisibility() {
    $('#updForm').toggleClass('d-none');
    $('#detTable').toggleClass('d-none');
}


$(document).ready(function () {

    $("#update").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            type: "POST",
            url: "php/profile.php",
            data: form.serialize(),
            success: function (res) {
                console.log(res);
                // Handle the response accordingly
            },
            error: function (xhr, status, error) {
                console.log("Error:", error);
            },
        });
    });
});

