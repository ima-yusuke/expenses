<div id="hamburger_icon" class="fixed right-6 top-6 z-50 cursor-pointer bg-white/80 backdrop-blur-sm hover:bg-white p-3 rounded-xl shadow-soft hover:shadow-soft-lg transition-all duration-300 border border-primary-100">
    <svg class="w-6 h-6 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</div>
<div id="close_icon" class="hidden fixed right-6 top-6 z-50 cursor-pointer bg-white/90 backdrop-blur-sm hover:bg-white p-3 rounded-xl shadow-soft-lg transition-all duration-300 border border-primary-200">
    <svg class="w-6 h-6 text-primary-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
</div>
<div class="side_wrapper w-full">
    <div class="side_menu_off pt-8 px-6">
        <div class="mb-8">
            <h5 class="text-sm font-semibold text-white/60 uppercase tracking-wider mb-1">Navigation</h5>
            <div class="h-0.5 w-12 bg-gradient-to-r from-accent-400 to-transparent rounded-full"></div>
        </div>

        <div class="py-4 overflow-y-auto">
            <ul class="space-y-3">
                @cannot('deleteWord')
                    <li class="side_li">
                        <a href="/login" class="flex items-center px-4 py-3 text-white hover:bg-white/10 rounded-xl transition-all duration-300 group">
                            <svg class="w-5 h-5 mr-3 text-accent-400 group-hover:text-accent-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="text-lg font-semibold">ログイン</span>
                        </a>
                    </li>
                @endcannot
                @hasanyrole('membership')
                    <li class="side_li">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-3 text-white hover:bg-white/10 rounded-xl transition-all duration-300 group">
                                <svg class="w-5 h-5 mr-3 text-accent-400 group-hover:text-accent-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span class="text-lg font-semibold">ログアウト</span>
                            </button>
                        </form>
                    </li>
                @endhasanyrole
            </ul>
        </div>
    </div>
</div>
