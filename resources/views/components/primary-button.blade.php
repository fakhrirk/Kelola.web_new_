<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border-2 border-gradient-to-br rounded-md font-semibold text-xs text-gray-400 uppercase tracking-widest hover:bg-gradient-to-tl from-indigo-900/30 to-gray-900/50 hover:text-white focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
