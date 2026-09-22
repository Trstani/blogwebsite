<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                Forgot Password
            </h1>

            <p class="text-gray-500 mt-2">
                Enter your email address and we'll send you a password reset code.
            </p>
        </div>

        <form id="forgotPasswordForm">

            @csrf

            <div class="mb-5">
                <label
                    for="email"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="email"
                    placeholder="you@example.com"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
                >
            </div>

            <div
                id="message"
                class="hidden mb-5 text-sm rounded-lg p-3"
            ></div>

            <button
                type="submit"
                id="submitButton"
                class="w-full bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 rounded-lg transition"
            >
                Send Reset Code
            </button>

        </form>

        <div class="text-center mt-6">
            <a
                href="/auth"
                class="text-sm text-gray-600 hover:text-gray-700"
            >
                Back to Login
            </a>
        </div>

    </div>

    <script>
        const form = document.getElementById('forgotPasswordForm');
        const message = document.getElementById('message');
        const submitButton = document.getElementById('submitButton');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            message.className = 'hidden mb-5 text-sm rounded-lg p-3';
            message.textContent = '';

            submitButton.disabled = true;
            submitButton.textContent = 'Sending...';

            try {
                const response = await fetch('/forgot-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'input[name="_token"]'
                        ).value
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message || 'Something went wrong.'
                    );
                }

                message.className =
                    'mb-5 text-sm rounded-lg p-3 bg-green-100 text-green-700';

                message.textContent = data.message;

                setTimeout(() => {
                    window.location.href =
                        '/reset-password?email=' +
                        encodeURIComponent(data.email);
                }, 500);

                form.reset();

            } catch (error) {

                message.className =
                    'mb-5 text-sm rounded-lg p-3 bg-red-100 text-red-700';

                message.textContent = error.message;

            } finally {

                submitButton.disabled = false;
                submitButton.textContent = 'Send Reset Code';
            }
        });
    </script>

</body>
</html>