<div class="flex flex-col items-center gap-4 p-6 bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm">
    <h2 class="text-lg font-medium dark:text-[#EDEDEC]">Compteur Livewire</h2>

    <div class="text-4xl font-bold dark:text-[#EDEDEC]">{{ $count }}</div>

    <div class="flex gap-3">
        <button
            wire:click="decrement"
            class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] rounded-sm hover:bg-black dark:hover:bg-white transition-all"
        >
            -
        </button>

        <button
            wire:click="increment"
            class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] rounded-sm hover:bg-black dark:hover:bg-white transition-all"
        >
            +
        </button>
    </div>
</div>
