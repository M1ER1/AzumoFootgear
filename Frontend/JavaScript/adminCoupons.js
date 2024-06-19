$(document).ready(function() {
    // Event handler for coupon creation form submission
    $("#createCouponForm").on("submit", function(e) {
        e.preventDefault(); // Prevent default form submission

        const formData = $(this).serialize(); // Serialize the form data

        // AJAX request to create a new coupon
        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=create_coupon", // Endpoint to create a coupon
            type: "POST",
            data: formData, // Send serialized form data
            success: function(response) {
                alert("Coupon created successfully!"); // Alert success message
                loadCoupons(); // Reload coupons after creation
            },
            error: function(xhr, status, error) {
                console.error("Failed to create coupon:", error);
                alert("Failed to create coupon: " + xhr.responseJSON.error); // Alert error message
            }
        });
    });

    // Function to load and display all coupons
    function loadCoupons() {
        $.ajax({
            url: "../../Backend/Handler/RequestHandler.php?resource=coupons", // Endpoint to get all coupons
            type: "GET",
            dataType: "json",
            success: function(response) {
                let content = "";
                $.each(response, function(index, coupon) {
                    // Generate HTML for each coupon
                    content += `<tr>
                        <td>${coupon.code}</td>
                        <td>${coupon.value}</td>
                        <td>${coupon.expiryDate}</td>
                        <td>${coupon.isUsed ? 'Yes' : 'No'}</td>
                    </tr>`;
                });
                $("#couponsTable tbody").html(content); // Append coupon HTML to the table
            },
            error: function(xhr, status, error) {
                console.error("Failed to load coupons:", error);
                alert("Failed to load coupons."); // Alert error message
            }
        });
    }

    // Initial load of coupons when the document is ready
    loadCoupons();
});
