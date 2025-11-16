<div class="counter">
    <h2 class="counter__title">Compteur Livewire</h2>

    <div class="counter__value">{{ $count }}</div>

    <div class="counter__actions">
        <button wire:click="decrement" class="counter__button">
            -
        </button>

        <button wire:click="increment" class="counter__button">
            +
        </button>
    </div>
</div>
