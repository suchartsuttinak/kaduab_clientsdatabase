<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Landing Page') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans">


    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
<!-- Footer -->
<footer class="bg-gradient-to-r from-indigo-900 via-purple-900 to-indigo-800 text-white py-6 mt-8">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-center md:text-left">

        <!-- Logo / Branding -->
        <div>
            <h3 class="text-xl font-bold mb-3">ระบบของเรา</h3>
            <p class="text-gray-300 text-sm leading-relaxed">
                ระบบที่ช่วยจัดการข้อมูลอย่างมีประสิทธิภาพ  
                เพื่ออนาคตที่โปร่งใสและยั่งยืน
            </p>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-lg font-semibold mb-3">เมนูด่วน</h3>
            <ul class="space-y-1 text-gray-300 text-sm">
                <li><a href="{{ url('/') }}" class="hover:text-yellow-400 transition-colors">หน้าแรก</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-yellow-400 transition-colors">เข้าสู่ระบบ</a></li>
                {{-- <li><a href="{{ route('register') }}" class="hover:text-yellow-400 transition-colors">สมัครสมาชิก</a></li> --}}
            </ul>
        </div>

        <!-- Contact & Social -->
        <div>
            <h3 class="text-lg font-semibold mb-3">ติดต่อเรา</h3>
            <p class="text-gray-300 text-sm">📞 โทร: 088-534-6727</p>
            <p class="text-gray-300 text-sm">✉️ อีเมล: suchartsuttinak@gmail.com</p>
            <div class="flex justify-center md:justify-start gap-4 mt-3">
                <a href="#" class="text-gray-300 hover:text-blue-500 transition-colors">
                    <i class="fab fa-facebook fa-lg"></i>
                </a>
                <a href="#" class="text-gray-300 hover:text-sky-400 transition-colors">
                    <i class="fab fa-twitter fa-lg"></i>
                </a>
                <a href="#" class="text-gray-300 hover:text-pink-500 transition-colors">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-gray-700 mt-6 pt-4 text-center text-gray-400 text-sm">
        <p>&copy; {{ date('Y') }} ระบบของเรา. สงวนลิขสิทธิ์.</p>
    </div>
</footer>
</body>
</html>