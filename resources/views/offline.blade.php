<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Offline | E-Barangay Health</title>
  <script src="{{ asset('js/tailwindcss.js') }}"></script>
  <style>
    body { background-color: #f9fafb; }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen text-center p-6">
  <div class="max-w-md w-full">
    <div class="mb-8">
      <div class="w-24 h-24 bg-brand-50 rounded-[2.5rem] flex items-center justify-center text-brand-500 mx-auto mb-6 shadow-inner border border-brand-100 transform -rotate-6">
        <i class="bi bi-wifi-off text-5xl"></i>
      </div>
      <h1 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">You're Offline</h1>
      <p class="text-gray-500 font-medium leading-relaxed">
        It looks like you've lost your internet connection. Don't worry, you can still access some features of the E-Barangay Health System.
      </p>
    </div>
    
    <div class="space-y-4">
      <button onclick="window.location.reload()" class="w-full py-4 bg-brand-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-brand-500/20 hover:bg-brand-700 transition-all active:scale-95">
        Try Again
      </button>
      <a href="/dashboard" class="block w-full py-4 bg-white text-gray-700 border border-gray-200 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-50 transition-all">
        Go to Dashboard
      </a>
    </div>
  </div>
  
  <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
</body>
</html>