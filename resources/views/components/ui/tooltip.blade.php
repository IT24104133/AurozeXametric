<div x-data="{open:false}" class="relative inline-block">
  <button type="button" @click.stop="open = !open" @keydown.escape.window="open = false" class="ml-2 w-5 h-5 rounded-full bg-slate-100 text-slate-700 text-xs flex items-center justify-center">!</button>
  <div x-show="open" x-transition class="absolute z-50 mt-2 w-64 bg-white border rounded shadow p-2 text-xs" @click.outside="open = false" style="display:none;">
    {{ $slot }}
  </div>
</div>
