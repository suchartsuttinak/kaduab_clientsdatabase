<section class="py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-primary mb-12 text-center">เกี่ยวกับเรา</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">

            <!-- Card 1: ประวัติองค์กร -->
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition text-left h-full">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary text-white rounded-full shrink-0">📖</div>
                    <h3 class="text-xl font-semibold text-primary ml-3">ประวัติความเป็นมา</h3>
                </div>

               <p class="text-gray-600 leading-relaxed text-left break-words"
   style="text-indent: 2em;">
    {{ isset($history) && $history ? $history->content : 'ยังไม่มีข้อมูล' }}
</p>
            </div>

            <!-- Card 2: วัตถุประสงค์ -->
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition text-left h-full">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary text-white rounded-full shrink-0">🎯</div>
                    <h3 class="text-xl font-semibold text-primary ml-3">วัตถุประสงค์</h3>
                </div>

               <p class="text-gray-600 leading-relaxed text-left break-words"
   style="text-indent: 2em;">
    {{ isset($objective) && $objective ? $objective->content : 'ยังไม่มีข้อมูล' }}
</p>
            </div>

            <!-- Card 3: พันธกิจของเรา -->
            <div class="p-6 bg-white rounded-lg shadow hover:shadow-lg transition text-left h-full">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 flex items-center justify-center bg-primary text-white rounded-full shrink-0">❤️</div>
                    <h3 class="text-xl font-semibold text-primary ml-3">พันธกิจของเรา</h3>
                </div>

               <p class="text-gray-600 leading-relaxed text-left break-words"
   style="text-indent: 2em;">
    {{ isset($mission) && $mission ? $mission->content : 'ยังไม่มีข้อมูล' }}
</p>
            </div>

        </div>
    </div>
</section>