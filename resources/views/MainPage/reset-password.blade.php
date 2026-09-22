<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reset Password - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        {{-- =========================================================
             HEADER
             ========================================================= --}}

        <div class="text-center mb-8">

            <h1
                id="pageTitle"
                class="text-2xl font-bold text-gray-900"
            >
                Verify OTP
            </h1>

            <p
                id="pageDescription"
                class="text-gray-500 mt-2"
            >
                Enter the 6-digit code sent to your email.
            </p>

            <p
                id="emailDisplay"
                class="text-sm font-medium text-gray-700 mt-3"
            ></p>

        </div>


        {{-- =========================================================
             STEP 1 — VERIFY OTP
             ========================================================= --}}

        <form id="verifyOtpForm">

            @csrf

            <div class="mb-5">

                <label
                    for="otp"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    OTP Code
                </label>

                <input
                    type="text"
                    id="otp"
                    name="otp"
                    inputmode="numeric"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    autocomplete="one-time-code"
                    placeholder="000000"
                    required
                    class="w-full px-4 py-3 text-center text-xl tracking-[0.5em] border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                >

            </div>

            <div class="flex items-center justify-between mt-3 text-sm">
                <span id="otpTimer" class="text-gray-500">
                    Resend OTP in 10:00
                </span>

                <button
                    type="button"
                    id="resendOtpBtn"
                    class="text-gray-400 cursor-not-allowed"
                    disabled
                >
                    Resend OTP
                </button>
            </div>

            <div
                id="otpMessage"
                class="hidden mb-5 text-sm rounded-lg p-3"
            ></div>

            <button
                type="submit"
                id="verifyButton"
                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition"
            >
                Verify OTP
            </button>

        </form>


        {{-- =========================================================
             STEP 2 — NEW PASSWORD
             ========================================================= --}}

        <form
            id="resetPasswordForm"
            class="hidden"
        >

            @csrf

            <div class="mb-4">

                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    New Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Enter new password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                >

            </div>


            <div class="mb-5">

                <label
                    for="password_confirmation"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Confirm New Password
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Confirm new password"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                >

            </div>


            <div
                id="resetMessage"
                class="hidden mb-5 text-sm rounded-lg p-3"
            ></div>


            <button
                type="submit"
                id="resetButton"
                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition"
            >
                Reset Password
            </button>

        </form>

    </div>


    <script>

        // =========================================================
        // COMMON ELEMENTS
        // =========================================================

        const verifyOtpForm =
            document.getElementById('verifyOtpForm');

        const resetPasswordForm =
            document.getElementById('resetPasswordForm');

        const verifyButton =
            document.getElementById('verifyButton');

        const resetButton =
            document.getElementById('resetButton');

        const otpMessage =
            document.getElementById('otpMessage');

        const resetMessage =
            document.getElementById('resetMessage');

        const pageTitle =
            document.getElementById('pageTitle');

        const pageDescription =
            document.getElementById('pageDescription');

        const emailDisplay =
            document.getElementById('emailDisplay');
        
        const otpTimer =
            document.getElementById('otpTimer');

        const resendOtpBtn =
            document.getElementById('resendOtpBtn');


        // =========================================================
        // GET EMAIL FROM URL
        // =========================================================

        const params =
            new URLSearchParams(window.location.search);

        const email =
            params.get('email');


        if (!email) {

            otpMessage.className =
                'mb-5 text-sm rounded-lg p-3 bg-red-100 text-red-700';

            otpMessage.textContent =
                'Email is missing. Please start the password reset process again.';

        } else {

            emailDisplay.textContent =
                email;

        }

        // =========================================================
        // OTP TIMER & RESEND
        // =========================================================

        let otpCountdown = 60;
        let timerInterval = null;

        function updateOtpTimer() {
            if (!otpTimer || !resendOtpBtn) {
                return;
            }

            const minutes = Math.floor(otpCountdown / 60);
            const seconds = otpCountdown % 60;

            otpTimer.textContent =
                `Resend OTP in ${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (otpCountdown <= 0) {
                clearInterval(timerInterval);

                otpTimer.textContent =
                    'You can request a new OTP.';

                resendOtpBtn.disabled = false;
                resendOtpBtn.className =
                    'text-black hover:underline transition-colors';

                return;
            }

            otpCountdown--;
        }

        function startOtpTimer() {
            clearInterval(timerInterval);

            otpCountdown = 60;

            if (resendOtpBtn) {
                resendOtpBtn.disabled = true;
                resendOtpBtn.className =
                    'text-gray-400 cursor-not-allowed';
            }

            updateOtpTimer();

            timerInterval = setInterval(
                updateOtpTimer,
                1000
            );
        }

        if (email) {
            startOtpTimer();
        }

        resendOtpBtn.addEventListener(
            'click',
            async function () {
                if (!email || resendOtpBtn.disabled) {
                    return;
                }

                resendOtpBtn.disabled = true;
                resendOtpBtn.textContent = 'Sending...';

                try {
                    const response = await fetch(
                        '/resend-password-reset',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN':
                                    document.querySelector(
                                        '#verifyOtpForm input[name="_token"]'
                                    ).value
                            },
                            body: JSON.stringify({
                                email: email
                            })
                        }
                    );

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                            'Failed to resend OTP.'
                        );
                    }

                    otpMessage.className =
                        'mb-5 text-sm rounded-lg p-3 bg-green-100 text-green-700';

                    otpMessage.textContent =
                        data.message ||
                        'A new OTP has been sent to your email.';

                    // Reset timer setelah OTP baru berhasil dikirim
                    startOtpTimer();

                } catch (error) {
                    otpMessage.className =
                        'mb-5 text-sm rounded-lg p-3 bg-red-100 text-red-700';

                    otpMessage.textContent =
                        error.message;

                    resendOtpBtn.disabled = false;
                    resendOtpBtn.textContent = 'Resend OTP';

                    resendOtpBtn.className =
                        'text-black hover:underline transition-colors';
                }
            }
        );


        // =========================================================
        // VERIFY OTP
        // =========================================================

        verifyOtpForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();

                if (!email) {
                    return;
                }

                otpMessage.className =
                    'hidden mb-5 text-sm rounded-lg p-3';

                otpMessage.textContent = '';

                verifyButton.disabled = true;

                verifyButton.textContent =
                    'Verifying...';


                try {

                    const response =
                        await fetch(
                            '/verify-password-reset',
                            {
                                method: 'POST',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        document.querySelector(
                                            '#verifyOtpForm input[name="_token"]'
                                        ).value
                                },

                                body:
                                    JSON.stringify({
                                        email: email,

                                        otp:
                                            document.getElementById(
                                                'otp'
                                            ).value
                                    })
                            }
                        );


                    const data =
                        await response.json();


                    if (!response.ok) {

                        throw new Error(
                            data.message ||
                            'OTP verification failed.'
                        );

                    }


                    // -------------------------------------------------
                    // OTP SUCCESS
                    // -------------------------------------------------

                    verifyOtpForm.classList.add('hidden');

                    pageTitle.textContent =
                        'Create New Password';

                    pageDescription.textContent =
                        'Enter your new password below.';

                    otpMessage.className =
                        'hidden mb-5 text-sm rounded-lg p-3';

                    resetPasswordForm.classList.remove('hidden');

                    document
                        .getElementById('password')
                        .focus();


                } catch (error) {

                    otpMessage.className =
                        'mb-5 text-sm rounded-lg p-3 bg-red-100 text-red-700';

                    otpMessage.textContent =
                        error.message;

                } finally {

                    verifyButton.disabled = false;

                    verifyButton.textContent =
                        'Verify OTP';

                }

            }
        );


        // =========================================================
        // RESET PASSWORD
        // =========================================================

        resetPasswordForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                resetMessage.className =
                    'hidden mb-5 text-sm rounded-lg p-3';

                resetMessage.textContent = '';


                const password =
                    document.getElementById(
                        'password'
                    ).value;

                const passwordConfirmation =
                    document.getElementById(
                        'password_confirmation'
                    ).value;


                if (password !== passwordConfirmation) {

                    resetMessage.className =
                        'mb-5 text-sm rounded-lg p-3 bg-red-100 text-red-700';

                    resetMessage.textContent =
                        'Passwords do not match.';

                    return;

                }


                resetButton.disabled = true;

                resetButton.textContent =
                    'Updating Password...';


                try {

                    const response =
                        await fetch(
                            '/reset-password',
                            {
                                method: 'POST',

                                headers: {
                                    'Content-Type':
                                        'application/json',

                                    'Accept':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                        document.querySelector(
                                            '#resetPasswordForm input[name="_token"]'
                                        ).value
                                },

                                body:
                                    JSON.stringify({
                                        password:
                                            password,

                                        password_confirmation:
                                            passwordConfirmation
                                    })
                            }
                        );


                    const data =
                        await response.json();


                    if (!response.ok) {

                        if (data.errors) {

                            const messages =
                                Object.values(
                                    data.errors
                                )
                                .flat()
                                .join(', ');

                            throw new Error(
                                messages ||
                                data.message ||
                                'Password reset failed.'
                            );

                        }

                        throw new Error(
                            data.message ||
                            'Password reset failed.'
                        );

                    }


                    resetMessage.className =
                        'mb-5 text-sm rounded-lg p-3 bg-green-100 text-green-700';

                    resetMessage.textContent =
                        data.message ||
                        'Password updated successfully.';


                    setTimeout(
                        () => {

                            window.location.href =
                                data.redirect ||
                                '/writer/dashboard';

                        },
                        700
                    );


                } catch (error) {

                    resetMessage.className =
                        'mb-5 text-sm rounded-lg p-3 bg-red-100 text-red-700';

                    resetMessage.textContent =
                        error.message;

                    resetButton.disabled =
                        false;

                    resetButton.textContent =
                        'Reset Password';

                }

            }
        );

    </script>

</body>
</html>