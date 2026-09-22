<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Verify Email - Blog Dinamika</title>

    @vite(['resources/css/app.css'])

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* ---------- Card entrance ---------- */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.99);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-anim {
            animation: fadeUp 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* ---------- OTP input ---------- */
        .otp-input {
            letter-spacing: 0.35em;
            text-indent: 0.35em;
        }

        .otp-input::placeholder {
            letter-spacing: 0.35em;
            text-indent: 0.35em;
            color: #d4d4d4;
            font-weight: 500;
        }

        .otp-input:focus {
            border-color: #171717;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(23, 23, 23, 0.08);
        }

        /* ---------- Verify button ---------- */
        .btn-verify:not(:disabled):hover {
            background: #262626;
            box-shadow: 0 12px 28px -10px rgba(0, 0, 0, 0.5);
        }

        .btn-verify:not(:disabled):active {
            transform: scale(0.985);
        }

        .btn-verify:disabled {
            background: #e5e5e5;
            color: #a3a3a3;
            box-shadow: none;
            cursor: not-allowed;
        }

        /* ---------- Spinner ---------- */
        .loading-spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid currentColor;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ---------- Subtle grid texture ---------- */
        .grid-texture {
            background-image:
                linear-gradient(to right, rgba(0, 0, 0, 0.035) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(0, 0, 0, 0.035) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(
                ellipse 70% 60% at 50% 40%,
                #000 30%,
                transparent 75%
            );
            -webkit-mask-image: radial-gradient(
                ellipse 70% 60% at 50% 40%,
                #000 30%,
                transparent 75%
            );
        }

        /* ---------- Resend timer ---------- */
        .resend-timer {
            font-variant-numeric: tabular-nums;
        }
    </style>
</head>

<body class="relative min-h-screen bg-neutral-50 flex items-center justify-center px-4 py-10 antialiased overflow-hidden">

    {{-- Background decoration --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-32 h-[26rem] w-[26rem] rounded-full bg-neutral-200/60 blur-3xl"></div>

        <div class="absolute -bottom-40 -left-32 h-[26rem] w-[26rem] rounded-full bg-neutral-300/50 blur-3xl"></div>

        <div class="absolute inset-0 grid-texture"></div>
    </div>


    {{-- Card --}}
    <main class="relative w-full max-w-[440px] card-anim">

        <div class="rounded-3xl border border-neutral-200/80 bg-white/85 backdrop-blur-xl p-7 sm:p-9 shadow-[0_24px_70px_-28px_rgba(0,0,0,0.28)]">

            {{-- Header --}}
            <div class="flex flex-col items-center text-center mb-7">

                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-neutral-900 text-white shadow-[0_12px_28px_-10px_rgba(0,0,0,0.55)] ring-1 ring-black/5">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                        />
                    </svg>

                </div>

                <h2 class="text-[22px] font-semibold tracking-tight text-neutral-900">
                    Verify your email
                </h2>

                <p class="mt-2 text-[13px] leading-relaxed text-neutral-500">
                    We've sent a 6-digit verification code to
                </p>

                <p
                    id="emailDisplay"
                    class="mt-2.5 max-w-full rounded-xl border border-neutral-200 bg-neutral-50 px-3.5 py-1.5 text-[13px] font-medium text-neutral-900 break-all"
                >
                    your email
                </p>

            </div>


            {{-- Sending status --}}
            <div
                id="sendingStatus"
                class="hidden mb-5 rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-center text-[13px] font-medium text-neutral-500"
            >
                <span class="loading-spinner"></span>
                Sending verification code...
            </div>


            {{-- Success status --}}
            <div
                id="successMessage"
                class="hidden mb-5 rounded-xl border border-emerald-200/80 bg-emerald-50 px-4 py-3 text-center text-[13px] font-medium text-emerald-700"
            >
                ✓ Verification code sent successfully.
            </div>


            {{-- Error --}}
            <div
                id="errorMessage"
                class="hidden mb-5 rounded-xl border border-red-200/80 bg-red-50 px-4 py-3 text-center text-[13px] font-medium text-red-600"
            ></div>


            {{-- OTP Form --}}
            <form id="otpForm">

                @csrf

                <input
                    type="hidden"
                    id="otpEmail"
                    name="email"
                >

                <div class="mb-5">

                    <label
                        for="otpCode"
                        class="mb-2.5 block text-[11px] font-semibold uppercase tracking-[0.12em] text-neutral-500"
                    >
                        Verification Code
                    </label>

                    <input
                        type="text"
                        id="otpCode"
                        name="otp"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="000000"
                        class="otp-input w-full rounded-2xl border border-neutral-200 bg-neutral-50/70 px-4 py-4 text-center text-2xl font-semibold text-neutral-900 outline-none transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled
                        required
                    >

                </div>


                <button
                    type="submit"
                    id="btnVerify"
                    class="btn-verify w-full rounded-2xl bg-neutral-900 py-3.5 text-sm font-semibold tracking-wide text-white shadow-[0_10px_24px_-12px_rgba(0,0,0,0.6)] transition-all duration-200"
                    disabled
                >
                    Verify Email
                </button>

            </form>


            {{-- Divider --}}
            <div class="mt-6 mb-4 flex items-center gap-3">

                <span class="h-px flex-1 bg-neutral-200"></span>

                <span class="text-[10px] font-semibold uppercase tracking-[0.14em] text-neutral-400">
                    Didn't receive it?
                </span>

                <span class="h-px flex-1 bg-neutral-200"></span>

            </div>


            {{-- Resend --}}
            <button
                type="button"
                id="resendOtpBtn"
                class="w-full rounded-2xl border border-neutral-200 bg-white py-3 text-sm font-medium text-neutral-700 transition-all duration-200 hover:border-neutral-300 hover:bg-neutral-50 active:scale-[0.985] disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:bg-white disabled:hover:border-neutral-200"
                disabled
            >
                <span id="resendButtonText">
                    Resend OTP
                </span>
            </button>


            {{-- Back --}}
            <div class="mt-6 border-t border-neutral-100 pt-5">

                <p class="text-center text-[13px]">

                    <a
                        href="/auth"
                        class="inline-flex items-center gap-1.5 font-medium text-neutral-400 transition-colors duration-200 hover:text-neutral-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-3.5 w-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"
                            />
                        </svg>

                        Back to login
                    </a>

                </p>

            </div>

        </div>

    </main>


    <script>

        /*
        |--------------------------------------------------------------------------
        | Elements
        |--------------------------------------------------------------------------
        */

        const otpForm =
            document.getElementById('otpForm');

        const otpEmail =
            document.getElementById('otpEmail');

        const emailDisplay =
            document.getElementById('emailDisplay');

        const otpCode =
            document.getElementById('otpCode');

        const btnVerify =
            document.getElementById('btnVerify');

        const resendOtpBtn =
            document.getElementById('resendOtpBtn');

        const resendButtonText =
            document.getElementById('resendButtonText');

        const sendingStatus =
            document.getElementById('sendingStatus');

        const successMessage =
            document.getElementById('successMessage');

        const errorMessage =
            document.getElementById('errorMessage');


        /*
        |--------------------------------------------------------------------------
        | Configuration
        |--------------------------------------------------------------------------
        */

        const RESEND_COOLDOWN = 60;

        let resendTimer = null;


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        const params =
            new URLSearchParams(window.location.search);

        const emailFromUrl =
            params.get('email');

        const savedEmail =
            localStorage.getItem('otp_email');

        const email =
            emailFromUrl || savedEmail;


        /*
        |--------------------------------------------------------------------------
        | Helper
        |--------------------------------------------------------------------------
        */

        async function parseResponse(response) {

            const text =
                await response.text();

            let data = null;

            try {

                data =
                    text
                        ? JSON.parse(text)
                        : null;

            } catch (error) {

                console.error(
                    'Server returned non-JSON response:',
                    text.substring(0, 500)
                );

                throw new Error(
                    'Server error occurred. Please try again.'
                );
            }


            if (!response.ok) {

                if (data && data.errors) {

                    const messages =
                        Object.values(data.errors)
                            .flat()
                            .join(', ');

                    throw new Error(
                        messages ||
                        data.message ||
                        'Request failed.'
                    );
                }

                throw new Error(
                    data?.message ||
                    'Request failed.'
                );
            }

            return data;
        }


        /*
        |--------------------------------------------------------------------------
        | Resend Countdown
        |--------------------------------------------------------------------------
        */

        function startResendCountdown() {

            clearInterval(resendTimer);

            let remaining =
                RESEND_COOLDOWN;

            resendOtpBtn.disabled = true;

            resendButtonText.textContent =
                `Resend OTP in ${remaining}s`;


            resendTimer = setInterval(() => {

                remaining--;

                if (remaining <= 0) {

                    clearInterval(resendTimer);

                    resendTimer = null;

                    resendOtpBtn.disabled = false;

                    resendButtonText.textContent =
                        'Resend OTP';

                    return;
                }

                resendButtonText.textContent =
                    `Resend OTP in ${remaining}s`;

            }, 1000);
        }


        /*
        |--------------------------------------------------------------------------
        | Initialize
        |--------------------------------------------------------------------------
        */

        if (!email) {

            sendingStatus.classList.add('hidden');

            errorMessage.textContent =
                'Verification email was not found. Please register again.';

            errorMessage.classList.remove('hidden');

            otpCode.disabled = true;

            btnVerify.disabled = true;

            resendOtpBtn.disabled = true;

        } else {

            otpEmail.value = email;

            emailDisplay.textContent = email;

            /*
             * The /register request already waited for Mail::send().
             * Reaching this page means the email sending request
             * completed successfully.
             */

            sendingStatus.classList.add('hidden');

            successMessage.textContent =
                '✓ Verification code sent. Please check your inbox or spam folder.';

            successMessage.classList.remove('hidden');

            otpCode.disabled = false;

            btnVerify.disabled = true;

            /*
             * Start cooldown immediately when the page loads.
             */
            startResendCountdown();

            setTimeout(() => {
                otpCode.focus();
            }, 300);
        }


        /*
        |--------------------------------------------------------------------------
        | OTP Input
        |--------------------------------------------------------------------------
        */

        otpCode.addEventListener('input', function () {

            this.value =
                this.value
                    .replace(/\D/g, '')
                    .slice(0, 6);

            btnVerify.disabled =
                this.value.length !== 6;
        });


        /*
        |--------------------------------------------------------------------------
        | Verify OTP
        |--------------------------------------------------------------------------
        */

        otpForm.addEventListener(
            'submit',
            async function (e) {

                e.preventDefault();

                if (
                    !email ||
                    otpCode.value.length !== 6 ||
                    btnVerify.disabled
                ) {
                    return;
                }

                btnVerify.disabled = true;

                btnVerify.innerHTML = `
                    <span class="loading-spinner"></span>
                    Verifying...
                `;

                errorMessage.classList.add('hidden');

                successMessage.classList.add('hidden');


                try {

                    const response =
                        await fetch('/verify-otp', {

                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    document.querySelector(
                                        'input[name="_token"]'
                                    ).value,
                            },

                            body: JSON.stringify({
                                email: email,
                                otp: otpCode.value,
                            }),
                        });


                    const data =
                        await parseResponse(response);


                    if (data && data.success) {
                        localStorage.removeItem('otp_email');

                        successMessage.textContent =
                            '✓ Email verified successfully. Redirecting...';

                        successMessage.classList.remove('hidden');

                        setTimeout(() => {
                            window.location.href =
                                data.redirect || '/writer/dashboard';
                        }, 1000);

                        return;
                    }


                    throw new Error(
                        data?.message ||
                        'OTP verification failed.'
                    );

                } catch (error) {

                    console.error(
                        'OTP verification error:',
                        error
                    );

                    errorMessage.textContent =
                        error.message ||
                        'An error occurred. Please try again.';

                    errorMessage.classList.remove(
                        'hidden'
                    );

                    btnVerify.disabled = false;

                    btnVerify.innerHTML =
                        'Verify Email';
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Resend OTP
        |--------------------------------------------------------------------------
        */

        resendOtpBtn.addEventListener(
            'click',
            async function () {

                if (
                    !email ||
                    this.disabled
                ) {
                    return;
                }

                const button =
                    this;

                button.disabled = true;

                resendButtonText.innerHTML = `
                    <span class="loading-spinner"></span>
                    Sending...
                `;

                errorMessage.classList.add(
                    'hidden'
                );

                successMessage.classList.add(
                    'hidden'
                );


                try {

                    const response =
                        await fetch('/resend-otp', {

                            method: 'POST',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    document.querySelector(
                                        'input[name="_token"]'
                                    ).value,
                            },

                            body: JSON.stringify({
                                email: email,
                            }),
                        });


                    const data =
                        await parseResponse(response);


                    if (data && data.success) {

                        successMessage.textContent =
                            '✓ A new verification code has been sent.';

                        successMessage.classList.remove(
                            'hidden'
                        );

                        otpCode.value = '';

                        btnVerify.disabled = true;

                        startResendCountdown();

                        return;
                    }


                    throw new Error(
                        data?.message ||
                        'Failed to resend OTP.'
                    );

                } catch (error) {

                    console.error(
                        'Resend OTP error:',
                        error
                    );

                    errorMessage.textContent =
                        error.message ||
                        'Unable to resend OTP.';

                    errorMessage.classList.remove(
                        'hidden'
                    );

                    button.disabled = false;

                    resendButtonText.textContent =
                        'Resend OTP';
                }
            }
        );

    </script>

</body>

</html>