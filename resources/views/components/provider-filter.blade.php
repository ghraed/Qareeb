@props(['id'])

<div class="relative" x-data="providerFilter" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false" x-on:change="value = $refs.select.value">
    {{ $slot }}
    <button id="{{ $id }}-control" x-ref="trigger" x-cloak x-show="enhanced" type="button" role="combobox" aria-haspopup="listbox" aria-controls="{{ $id }}-options" aria-labelledby="{{ $id }}-label" x-bind:aria-expanded="open" x-bind:aria-activedescendant="open ? '{{ $id }}-option-' + active : null" class="q-input flex items-center justify-between gap-3 text-left" x-on:click="toggle()" x-on:keydown="onKey($event)">
        <span x-text="label"></span>
        <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m5 7.5 5 5 5-5" /></svg>
    </button>
    <ul id="{{ $id }}-options" x-ref="list" x-cloak x-show="open" role="listbox" aria-labelledby="{{ $id }}-label" class="absolute inset-x-0 top-full z-50 mt-2 max-h-64 overflow-y-auto rounded-xl border border-[#eadfc7] bg-[#fffdf8] p-1 shadow-xl">
        <template x-for="(option, index) in options" :key="option.value">
            <li x-bind:id="'{{ $id }}-option-' + index" role="option" x-bind:aria-selected="value === option.value" class="cursor-pointer rounded-lg px-4 py-3 text-[#1f2b23]" x-bind:class="{ 'bg-[#e8dcc2]': active === index, 'font-semibold': value === option.value }" x-on:mouseenter="active = index" x-on:mousedown.prevent x-on:click.stop="choose(index)" x-text="option.label"></li>
        </template>
    </ul>
</div>
