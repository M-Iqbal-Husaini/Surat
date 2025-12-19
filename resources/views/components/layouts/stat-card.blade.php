@props([
    'title',
    'value' => 0,
    'icon' => '',
    'color' => 'bg-gray-300',
])

<div class="w-full bg-white rounded-2xl px-5 py-5
            border border-slate-200">

    <div class="flex items-center justify-between">

        {{-- TEXT --}}
        <div class="flex flex-col">
            <span class="text-[17px] font-bold text-slate-500">{{ $title }}</span>

            <span class="text-[28px] font-bold text-slate-800 mt-1">
                {{ $value }}
            </span>
        </div>

        {{-- ICON --}}
        <div class="h-16 w-16 rounded-xl {{ $color }}
                    flex items-center justify-center text-white">
            {!! $icon !!}
        </div>

    </div>
</div>
