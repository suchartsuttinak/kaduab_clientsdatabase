<section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 px-6 text-center">

      
        {{-- Scholarship Support --}}
<div class="p-8 bg-gray-50 rounded-lg shadow hover:shadow-lg transition">
    <h2 class="text-3xl font-bold text-primary mb-6">
        สนับสนุนทุนการศึกษาเด็ก
    </h2>

    <p class="text-gray-600 mb-8">
        ร่วมเป็นส่วนหนึ่งในการสนับสนุนเด็กและเยาวชน ทั้งด้านทุนการศึกษา ชุดนักเรียน และอุปกรณ์การเรียน
    </p>

    <div class="bg-white p-6 rounded-lg shadow text-left mb-6">
        <h3 class="text-xl font-semibold text-primary mb-4">
            รูปแบบการสนับสนุน
        </h3>

        <ul class="space-y-2 text-gray-700">
            <li>✅ ทุนการศึกษา</li>
            <li>✅ ชุดนักเรียน</li>
            <li>✅ อุปกรณ์การเรียน</li>
        </ul>
    </div>

   <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

    <a href="{{ route('scholarship.create') }}"
       class="group px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600
              text-white font-semibold text-center shadow-md
              hover:shadow-xl hover:-translate-y-0.5
              transition-all duration-300">

        <div class="flex items-center justify-center gap-2">
            <span>💙</span>
            <span>สนใจสนับสนุน</span>
        </div>
    </a>

    <a href="{{ route('scholarship.children.public_report') }}"
       class="group px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600
              text-white font-semibold text-center shadow-md
              hover:shadow-xl hover:-translate-y-0.5
              transition-all duration-300">

        <div class="flex items-center justify-center gap-2">
            <span>🎓</span>
            <span>ผู้ขอรับทุนภาคเรียนนี้</span>
        </div>
    </a>

</div>
</div>

        {{-- Report Issue --}}
        <div class="p-8 bg-gray-50 rounded-lg shadow hover:shadow-lg transition">
            <h2 class="text-3xl font-bold text-primary mb-6">แจ้งเรื่องช่วยเหลือ</h2>

            <p class="text-gray-600 mb-8">
                หากมีปัญหาความเดือดร้อน สามารถแจ้งเราได้
            </p>

         @if(session('success'))
            <div id="issue-success-alert"
                class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700 text-left transition-all duration-500">
                <div class="font-semibold mb-1">
                    แจ้งเรื่องสำเร็จ
                </div>
                <div class="text-sm">
                    {{ session('success') }}
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {

                setTimeout(function(){

                    const alertBox =
                        document.getElementById('issue-success-alert');

                    if(alertBox){
                        alertBox.style.opacity='0';
                        alertBox.style.transform='translateY(-10px)';

                        setTimeout(function(){
                            alertBox.remove();
                        },500);
                    }

                },5000); // 5 วินาที

            });
            </script>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 text-left">
                    <div class="font-semibold mb-1">
                        กรุณาตรวจสอบข้อมูล
                    </div>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('issues.store') }}" method="POST" class="space-y-4 text-left">
                @csrf

                <div>
                    <label class="block text-gray-700 mb-2">ชื่อ-สกุลผู้แจ้ง</label>
                    <input type="text"
                           name="fullname"
                           value="{{ old('fullname') }}"
                           maxlength="255"
                           autocomplete="name"
                           class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30"
                           required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">เบอร์โทรศัพท์</label>
                    <input type="text"
                           name="phone"
                           value="{{ old('phone') }}"
                           maxlength="20"
                           inputmode="tel"
                           autocomplete="tel"
                           class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30"
                           required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">เรื่องที่แจ้ง</label>
                    <textarea name="subject"
                              rows="4"
                              maxlength="5000"
                              class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary/30"
                              required>{{ old('subject') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full px-8 py-3 bg-secondary text-white font-semibold rounded-lg shadow hover:bg-gray-700 transition">
                    แจ้งปัญหา
                </button>
            </form>
        </div>

    </div>
</section>