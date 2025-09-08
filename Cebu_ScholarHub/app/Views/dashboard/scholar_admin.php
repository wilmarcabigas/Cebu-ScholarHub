<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholar Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white text-2xl font-semibold">Scholar Admin</a>
            <div>
                <a href="/logout" class="text-white px-4 py-2 rounded bg-red-600 hover:bg-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-gray-800">Welcome, Scholar Admin</h1>
            <p class="text-gray-600 mt-4">You have full access to manage scholars and generate reports.</p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="/scholars" class="block text-center bg-blue-600 text-white p-6 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">
                    <h3 class="text-xl font-semibold">Manage Scholars</h3>
                    <p class="mt-2">View and manage student data.</p>
                </a>
                <a href="/reports" class="block text-center bg-green-600 text-white p-6 rounded-lg shadow-lg hover:bg-green-700 transition duration-300">
                    <h3 class="text-xl font-semibold">Generate Reports</h3>
                    <p class="mt-2">Create and export various reports.</p>
                </a>
                <a href="/messages" class="block text-center bg-indigo-600 text-white p-6 rounded-lg shadow-lg hover:bg-indigo-700 transition duration-300">
                    <h3 class="text-xl font-semibold">Messages</h3>
                    <p class="mt-2">Check for new messages and notifications.</p>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
