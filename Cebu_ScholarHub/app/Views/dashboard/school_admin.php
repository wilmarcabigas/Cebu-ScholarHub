<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <!-- Navbar -->
    <nav class="bg-green-600 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="#" class="text-white text-2xl font-semibold">School Admin</a>
            <div>
                <a href="/logout" class="text-white px-4 py-2 rounded bg-red-600 hover:bg-red-700">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto mt-8 px-4">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold text-gray-800">Welcome, School Admin</h1>
            <p class="text-gray-600 mt-4">You can manage scholars in your assigned school and handle billing records.</p>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="/school/scholars" class="block text-center bg-blue-600 text-white p-6 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">
                    <h3 class="text-xl font-semibold">My Scholars</h3>
                    <p class="mt-2">Manage scholar profiles for your school.</p>
                </a>
                <a href="/school/billing" class="block text-center bg-yellow-600 text-white p-6 rounded-lg shadow-lg hover:bg-yellow-700 transition duration-300">
                    <h3 class="text-xl font-semibold">Post Billing</h3>
                    <p class="mt-2">Add and manage billing records for your scholars.</p>
                </a>
                <a href="/school/reports" class="block text-center bg-teal-600 text-white p-6 rounded-lg shadow-lg hover:bg-teal-700 transition duration-300">
                    <h3 class="text-xl font-semibold">My Reports</h3>
                    <p class="mt-2">Generate and view your school-specific reports.</p>
                </a>
            </div>
        </div>
    </div>

</body>
</html>
