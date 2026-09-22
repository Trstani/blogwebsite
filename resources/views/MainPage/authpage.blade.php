<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login / Register - Blog Dinamika</title>

    @vite(['resources/css/app.css'])

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* =========================================================
           AUTH CONTAINER
           ========================================================= */

        .auth-container {
            width: 768px;
            height: 500px;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }


        /* =========================================================
           COMMON PANEL
           ========================================================= */

        .panel {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 0 48px;

            transition:
                left 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15),
                right 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15),
                opacity 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15),
                transform 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15);

            box-sizing: border-box;
        }


        /* =========================================================
           LOGIN PANEL
           ========================================================= */

        .panel-login {
            left: 0;
            right: auto;

            z-index: 2;
            opacity: 1;

            transform: translateX(0);
            pointer-events: auto;
        }


        /* =========================================================
           REGISTER PANEL
           ========================================================= */

        .panel-register {
            left: auto;
            right: 0;

            z-index: 1;
            opacity: 0;

            transform: translateX(100%);
            pointer-events: none;
        }


        /* =========================================================
           REGISTER STATE
           ========================================================= */

        .auth-container.active-register .panel-login {
            left: -50%;
            opacity: 0;

            transform: translateX(0);
            pointer-events: none;
        }

        .auth-container.active-register .panel-register {
            left: auto;
            right: 0;

            opacity: 1;
            transform: translateX(0);

            z-index: 4;
            pointer-events: auto;
        }


        /* =========================================================
           OVERLAY
           ========================================================= */

        .overlay {
            position: absolute;
            top: 0;

            width: 50%;
            height: 100%;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            cursor: pointer;

            transition:
                left 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15),
                right 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15),
                opacity 0.6s cubic-bezier(0.68, -0.15, 0.27, 1.15);

            box-sizing: border-box;
        }

        .overlay-login {
            right: 0;
            left: auto;

            z-index: 5;

            background: #000;
            color: #fff;

            opacity: 1;
        }

        .overlay-register {
            left: -50%;
            right: auto;

            z-index: 5;

            background: #000;
            color: #fff;

            opacity: 0;
        }


        /* =========================================================
           REGISTER OVERLAY STATE
           ========================================================= */

        .auth-container.active-register .overlay-login {
            right: -50%;
            opacity: 0;

            pointer-events: none;
        }

        .auth-container.active-register .overlay-register {
            left: 0;
            opacity: 1;

            pointer-events: auto;
        }


        /* =========================================================
           FORM INPUT
           ========================================================= */

        .form-input:focus {
            border-color: #000;
        }


        /* =========================================================
           BUTTON DISABLED
           ========================================================= */

        .btn-submit:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            opacity: 0.8;
        }


        /* =========================================================
           LOADING SPINNER
           ========================================================= */

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


        /* =========================================================
           RESPONSIVE
           ========================================================= */

        @media (max-width: 800px) {

            .auth-container {
                width: calc(100vw - 32px);
                max-width: 768px;
            }

            .panel {
                padding: 0 32px;
            }

            .overlay {
                padding: 0 20px;
                text-align: center;
            }
        }


        @media (max-width: 640px) {

            .auth-container {
                height: 520px;
            }

            .panel {
                width: 100%;
                padding: 0 28px;
            }

            .overlay {
                display: none;
            }

            .panel-login {
                width: 100%;
                left: 0;
            }

            .panel-register {
                width: 100%;
                right: -100%;
                left: auto;
            }

            .auth-container.active-register .panel-login {
                left: -100%;
            }

            .auth-container.active-register .panel-register {
                right: 0;
            }
        }
    </style>
</head>


<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div id="auth-container" class="auth-container bg-white">


        {{-- =========================================================
             LOGIN PANEL
             ========================================================= --}}

        <div class="panel panel-login bg-white">

            <h2 class="text-2xl font-bold text-black mb-1">
                Welcome back
            </h2>

            <p class="text-sm text-gray-500 mb-6">
                Sign in to your account
            </p>


            <form method="POST" action="/login">

                @csrf


                <div class="mb-4">

                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-input w-full px-4 py-3 border border-gray-200 rounded-md text-sm outline-none transition-colors bg-white"
                        placeholder="you@example.com"
                        required
                    />

                </div>


                <div class="mb-4">

                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-input w-full px-4 py-3 border border-gray-200 rounded-md text-sm outline-none transition-colors bg-white"
                        placeholder="••••••••"
                        required
                    />

                </div>


                <div class="flex items-center justify-between mb-6">

                    <label class="flex items-center text-sm text-gray-600">

                        <input
                            type="checkbox"
                            name="remember"
                            class="mr-2 rounded"
                        />

                        Remember me

                    </label>

                     <a
                        href="/forgot-password"
                        class="text-sm text-gray-500 hover:text-black transition-colors"
                    >
                        Forgot password?
                    </a>

                </div>


                <button
                    type="submit"
                    class="btn-submit w-full bg-black text-white py-3 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors"
                >
                    Sign In
                </button>

            </form>


            @if ($errors->any())

                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-600">

                    {{ $errors->first() }}

                </div>

            @endif

        </div>


        {{-- =========================================================
             REGISTER PANEL
             ========================================================= --}}

        <div class="panel panel-register bg-white">

            <h2 class="text-2xl font-bold text-black mb-1">
                Create account
            </h2>

            <p class="text-sm text-gray-500 mb-6">
                Fill in your details to register
            </p>


            <form
                id="registerForm"
                method="POST"
                action="/register"
            >

                @csrf


                <div class="mb-4">

                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Username
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-input w-full px-4 py-3 border border-gray-200 rounded-md text-sm outline-none transition-colors bg-white"
                        placeholder="Paul Waker"
                        required
                    />

                </div>


                <div class="mb-4">

                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-input w-full px-4 py-3 border border-gray-200 rounded-md text-sm outline-none transition-colors bg-white"
                        placeholder="you@example.com"
                        required
                    />

                </div>


                <div class="mb-4">

                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-input w-full px-4 py-3 border border-gray-200 rounded-md text-sm outline-none transition-colors bg-white"
                        placeholder="••••••••"
                        required
                    />

                </div>


                <div class="mb-6">

                    <label class="block text-xs font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-input w-full px-4 py-3 border border-gray-200 rounded-md text-sm outline-none transition-colors bg-white"
                        placeholder="••••••••"
                        required
                    />

                </div>


                <button
                    type="submit"
                    id="registerSubmitBtn"
                    class="btn-submit w-full bg-black text-white py-3 rounded-md text-sm font-medium hover:bg-gray-800 transition-colors"
                >
                    Create Account
                </button>

            </form>


            <div
                id="registerError"
                class="mt-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-600"
                style="display: none;"
            ></div>

        </div>


        {{-- =========================================================
             OVERLAY - GO TO REGISTER
             ========================================================= --}}

        <div
            class="overlay overlay-login"
            onclick="toggleAuth()"
        >

            <p class="text-sm mb-2 opacity-80">
                Don't have an account?
            </p>

            <h3 class="text-2xl font-bold mb-4">
                Sign Up
            </h3>

            <button
                type="button"
                class="px-8 py-3 bg-transparent text-white border border-white rounded-md text-sm font-medium hover:bg-white hover:text-black transition-colors"
            >
                Create Account
            </button>

        </div>


        {{-- =========================================================
             OVERLAY - GO BACK TO LOGIN
             ========================================================= --}}

        <div
            class="overlay overlay-register"
            onclick="toggleAuth()"
        >

            <p class="text-sm mb-2 opacity-80">
                Already have an account?
            </p>

            <h3 class="text-2xl font-bold mb-4">
                Sign In
            </h3>

            <button
                type="button"
                class="px-8 py-3 bg-transparent text-white border border-white rounded-md text-sm font-medium hover:bg-white hover:text-black transition-colors"
            >
                Sign In
            </button>

        </div>

    </div>


    <script>

        /* =========================================================
           PANEL SWITCHING
           ========================================================= */

        function toggleAuth() {

            const authContainer =
                document.getElementById('auth-container');

            /*
             * Switch between Login and Register.
             */
            authContainer.classList.toggle('active-register');
        }


        /* =========================================================
           RESPONSE HELPER
           ========================================================= */

        async function parseResponse(response) {

            const text =
                await response.text();

            let data = null;

            try {

                data = text
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

                /*
                 * Laravel validation errors.
                 */
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


        /* =========================================================
           REGISTER FORM
           ========================================================= */

        const registerForm =
            document.getElementById('registerForm');


        registerForm.addEventListener(
            'submit',
            async function (e) {

                e.preventDefault();


                const submitBtn =
                    document.getElementById(
                        'registerSubmitBtn'
                    );


                const errorEl =
                    document.getElementById(
                        'registerError'
                    );


                /*
                 * Prevent duplicate submissions.
                 */
                if (submitBtn.disabled) {
                    return;
                }


                const formData = {

                    name:
                        this.querySelector(
                            'input[name="name"]'
                        ).value.trim(),

                    email:
                        this.querySelector(
                            'input[name="email"]'
                        ).value.trim(),

                    password:
                        this.querySelector(
                            'input[name="password"]'
                        ).value,

                    password_confirmation:
                        this.querySelector(
                            'input[name="password_confirmation"]'
                        ).value,
                };


                /* -------------------------------------------------
                   LOADING STATE
                   ------------------------------------------------- */

                submitBtn.disabled = true;

                submitBtn.innerHTML = `
                    <span class="loading-spinner"></span>
                    Creating account...
                `;

                errorEl.style.display = 'none';
                errorEl.textContent = '';


                try {

                    const response =
                        await fetch(
                            '/register',
                            {
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

                                body:
                                    JSON.stringify(
                                        formData
                                    ),
                            }
                        );


                    const data =
                        await parseResponse(
                            response
                        );


                    /* -------------------------------------------------
                       REGISTRATION SUCCESS
                       ------------------------------------------------- */

                    if (
                        data &&
                        data.success
                    ) {

                        /*
                         * Store the email so the verification page
                         * can recover it if needed.
                         */
                        localStorage.setItem(
                            'otp_email',
                            formData.email
                        );


                        /*
                         * The /register request only returns after
                         * Mail::send() has completed.
                         *
                         * Therefore reaching this point means the
                         * verification email has been handed off
                         * successfully.
                         */

                        submitBtn.innerHTML = `
                            <span class="loading-spinner"></span>
                            Email sent. Opening verification...
                        `;


                        /*
                         * Open the dedicated OTP page.
                         */
                        setTimeout(
                            () => {

                                window.location.href =
                                    '/verify-otp?email=' +
                                    encodeURIComponent(
                                        formData.email
                                    );

                            },
                            500
                        );


                        return;
                    }


                    /*
                     * Backend returned HTTP 200 but
                     * success=false.
                     */
                    throw new Error(
                        data?.message ||
                        'Registration failed.'
                    );


                } catch (error) {

                    console.error(
                        'Registration error:',
                        error
                    );


                    errorEl.textContent =
                        error.message ||
                        'An error occurred. Please try again.';


                    errorEl.style.display =
                        'block';


                    /*
                     * Restore button.
                     */
                    submitBtn.disabled =
                        false;


                    submitBtn.innerHTML =
                        'Create Account';
                }

            }
        );

    </script>

</body>

</html>