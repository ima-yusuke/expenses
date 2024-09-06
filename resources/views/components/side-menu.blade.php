<i id="hamburger_icon" class="fa-solid fa-bars fixed right-4 md:right-6 top-4 md:top-6 text-2xl md:text-4xl cursor-pointer"></i>
<i id="close_icon" class="hidden fa-solid fa-xmark fixed right-4 md:right-6 top-4 md:top-6 text-2xl md:text-4xl cursor-pointer"></i>
<div class="side_wrapper w-full">
    <div class="side_menu_off pt-4">
        <h5 class="text-base text-white px-5">Menu</h5>

        <div class="py-4 overflow-y-auto">
            <ul class="space-y-4">
                @cannot('deleteWord')
                    <li class="side_li">
                        <a href="/login" class="flex items-center p-2 text-white text-4xl hover:text-gray-100 group">
                            <span class="ms-3">LOGIN</span>
                        </a>
                    </li>
                @endcannot
                @hasanyrole('membership')
                    <li class="side_li">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                        <button type="submit" class="flex items-center p-2 text-white text-4xl hover:text-gray-100 group">
                            <span class="ms-3">LOGOUT</span>
                        </button>
                        </form>
                    </li>
                @endhasanyrole
            </ul>
        </div>
    </div>
</div>
