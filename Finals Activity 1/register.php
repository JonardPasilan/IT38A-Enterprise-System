<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MedTrack Registration</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Special+Elite&display=swap');

    .logo-text {
      font-family: 'Special Elite', monospace;
    }
  </style>
</head>
<body class="bg-[#6a0a0a] min-h-screen flex items-center justify-center p-4">

  <div class="flex max-w-4xl w-full rounded-lg overflow-hidden">
    
    <!-- Left Panel (Same as login) -->
    <div class="relative w-[350px] sm:w-[400px] md:w-[450px] rounded-tl-lg rounded-bl-lg"
         style="background: linear-gradient(180deg, #6a0a0a 0%, #a87f7f 100%)">
      <div class="absolute inset-0 bg-gradient-to-b from-[#6a0a0a] to-[#a87f7f] rounded-tl-lg rounded-bl-lg"></div>
      <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
        <h1 class="text-white text-3xl font-normal mb-4 logo-text">MedTrack</h1>
        <img
          src="https://storage.googleapis.com/a1aa/image/9afcf473-6f23-4fb6-67a9-32f379bd482b.jpg"
          alt="Red heart with heartbeat line and stethoscope"
          class="w-[150px] h-[150px] object-contain"
        />
      </div>
      <div class="absolute bottom-0 left-0 w-full h-[100px] bg-black rounded-tl-[100px]"
           style="clip-path: ellipse(100% 100% at 0% 100%)"></div>
    </div>

    <!-- Right Panel (Form) -->
    <form action="#" method="post"
          class="bg-black rounded-tr-lg rounded-br-lg w-[350px] sm:w-[400px] md:w-[450px] p-6 flex flex-col text-white space-y-4 text-sm">

      <div class="text-center">
        <div class="bg-white rounded-full p-3 mb-2 inline-flex items-center justify-center" style="width: 60px; height: 60px;">
          <i class="fas fa-user-plus fa-lg text-black"></i>
        </div>
        <h2 class="text-white text-xl font-bold tracking-wide">REGISTER</h2>
      </div>

      <div class="flex gap-2">
        <div class="w-1/2">
          <label for="firstName" class="block">First Name:</label>
          <input type="text" id="firstName" name="firstName"
                 class="w-full rounded-md py-2 px-3 text-black bg-white focus:outline-none" />
        </div>
        <div class="w-1/2">
          <label for="lastName" class="block">Last Name:</label>
          <input type="text" id="lastName" name="lastName"
                 class="w-full rounded-md py-2 px-3 text-black bg-white focus:outline-none" />
        </div>
      </div>

      <div>
        <label for="email" class="block">Email:</label>
        <input type="email" id="email" name="email"
               class="w-full rounded-md py-2 px-3 text-black bg-white focus:outline-none" />
      </div>

      <div>
        <label for="phone" class="block">Phone Number:</label>
        <input type="tel" id="phone" name="phone"
               class="w-full rounded-md py-2 px-3 text-black bg-white focus:outline-none" />
      </div>

      <div>
        <label for="password" class="block">Password:</label>
        <input type="password" id="password" name="password"
               class="w-full rounded-md py-2 px-3 text-black bg-white focus:outline-none" />
      </div>

      <div>
        <label for="confirmPassword" class="block">Confirm Password:</label>
        <input type="password" id="confirmPassword" name="confirmPassword"
               class="w-full rounded-md py-2 px-3 text-black bg-white focus:outline-none" />
      </div>

      <div class="flex items-center justify-between text-xs mt-1">
        <label class="flex items-center space-x-1">
          <input type="checkbox" class="w-3 h-3" />
          <span>I agree to the terms</span>
        </label>
      </div>

      <button type="submit"
              class="w-full bg-[#b33a3a] text-white font-bold py-2 rounded-md mt-3">
        Register
      </button>

      <div class="text-center text-white mt-2 mb-2">OR</div>

      <a href="login.php"
         class="w-full bg-white text-black font-bold py-2 rounded-md text-center block hover:bg-gray-200 transition">
        Back to Login
      </a>
    </form>
  </div>

</body>
</html>
