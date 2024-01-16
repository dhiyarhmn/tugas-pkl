<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Custom styles */
  </style>
</head>

<body>
  <div class="header">
    <div class="inner-header flex flex-col items-center justify-center h-screen">
      <form class="grid card w-150 px-10 py-8 bg-white" method="post">
        <div class="text-center text-5xl font-medium text-gray-800 mb-5">
          Pengajuan Absensi
        </div>
        <div class="text-center text-xl font-medium text-gray-600 mb-5">
          PT. Daekyung Indah Heavy Industry
        </div>
        <div class="mb-3">
          <label class="block text-gray-800 text-xl font-bold mb-0 pr-4" for="Username">
            Username
          </label>
          <input name="username" class="bg-gray-300 appearance-none border-2 border-gray-400 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-gray-200  focus:border-gray-300" id="inline-full-name" type="text" placeholder="Enter your username">
        </div>
        <div class="mb-4">
          <label class="block text-gray-800 text-xl font-bold mb-0 pr-4" for="password">
            Password
          </label>
          <input name="password" class="bg-gray-300 appearance-none border-2 border-gray-400 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-gray-200 focus:border-gray-300" id="inline-password" type="password" placeholder="Enter your password">
        </div>

        <!-- <?php
    
              ?> -->

        <div class="justify-self-end">
          <button name="submit" class="focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded bg-green-500 hover:bg-green-600" type="submit">
            Login
          </button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>

