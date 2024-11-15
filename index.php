<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Mobile OTP Verification</h1>

    <!-- OTP Request Form -->
    <form id="otpForm" onsubmit="return false;">
        <div class="mb-3">
            <label for="phone" class="form-label">Enter your Phone Number</label>
            <input type="text" id="phone" class="form-control" maxlength="10" placeholder="Enter 10-digit mobile number" required pattern="\d{10}">
        </div>
        <button type="button" id="sendOTPButton" class="btn btn-primary" onclick="sendOTP()">Send OTP</button>

        <!-- OTP Verification Form (hidden initially) -->
        <div id="verifyOTPSection" style="display: none;">
            <div class="mb-3 mt-3">
                <label for="otp" class="form-label">Enter OTP</label>
                <input type="text" id="otp" class="form-control" maxlength="4" placeholder="Enter OTP" required pattern="\d{4}">
            </div>
            <button type="button" class="btn btn-secondary" onclick="verifyOTP()">Verify OTP</button>
            <div id="otpStatus" style="display: none; color: green;">OTP Verified!</div>
            <div id="otpFailedStatus" style="display: none; color: red;">Invalid OTP. Please try again.</div>
        </div>
    </form>
</div>

<script>
let generatedOTP = null; // Store generated OTP

// Function to send OTP to the user's phone number
function sendOTP() {
    const phone = document.getElementById('phone').value;

    if (phone.length === 10) {
        // Send OTP request to the backend API
        fetch('send_otp.php', {
            method: 'POST',
            body: JSON.stringify({ phone: phone }),
            headers: { 'Content-Type': 'application/json' },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('OTP sent successfully! Please enter the OTP.');
                generatedOTP = data.otp; // Save the generated OTP for verification
                document.getElementById('verifyOTPSection').style.display = 'block'; // Show OTP verification section
                document.getElementById('sendOTPButton').disabled = true; // Disable send OTP button
            } else {
                alert(data.message); // Show error message
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
    } else {
        alert('Please enter a valid 10-digit phone number.');
    }
}

// Function to verify the entered OTP
function verifyOTP() {
    const enteredOTP = document.getElementById('otp').value;
    
    // Send OTP for verification to the server
    fetch('send_otp.php', {
        method: 'POST',
        body: JSON.stringify({ otp: enteredOTP }),  // Send entered OTP for verification
        headers: { 'Content-Type': 'application/json' },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('otpStatus').style.display = 'inline';
            document.getElementById('otpFailedStatus').style.display = 'none';
            alert('OTP verified successfully!');
        } else {
            document.getElementById('otpStatus').style.display = 'none';
            document.getElementById('otpFailedStatus').style.display = 'inline';
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}
</script>


</body>
</html>
