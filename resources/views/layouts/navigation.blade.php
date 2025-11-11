<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                {{-- === START ADMIN LINKS === --}}
                {{-- Only show if the logged-in user has the 'admin' role --}}
                @if(Auth::user() && Auth::user()->hasRole('admin'))

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            {{ __('Usuarios') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                            {{ __('Roles') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('docentes.index')" :active="request()->routeIs('docentes.*')">
                            {{ __('Docentes') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')"> {{-- Adjusted active state --}}
                            {{ __('Materias') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('aulas.index')" :active="request()->routeIs('aulas.*')"> {{-- Adjusted active state --}}
                            {{ __('Aulas') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                            {{ __('Grupos') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('semestres.index')" :active="request()->routeIs('semestres.*')">
                            {{ __('Semestres') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('horarios.index')" :active="request()->routeIs('horarios.*', 'horarios.asistencias.*')">
                            {{ __('Horarios') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('horarios.import')" :active="request()->routeIs('horarios.import*')">
                            {{ __('Importar Horarios') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('estadisticas.index')" :active="request()->routeIs('estadisticas.*')">
                            {{ __('Estadísticas') }}
                        </x-nav-link>
                    </div>

                @endif
                {{-- === END ADMIN LINKS === --}}

                {{-- === START CUSTOM ROLE LINKS (based on modules) === --}}
                @if(Auth::user() && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('docente'))

                    @if(Auth::user()->hasModule('usuarios'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('Usuarios') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('roles'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                                {{ __('Roles') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('docentes'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('docentes.index')" :active="request()->routeIs('docentes.*')">
                                {{ __('Docentes') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('materias'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                                {{ __('Materias') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('aulas'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('aulas.index')" :active="request()->routeIs('aulas.*')">
                                {{ __('Aulas') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('grupos'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                                {{ __('Grupos') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('semestres'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('semestres.index')" :active="request()->routeIs('semestres.*')">
                                {{ __('Semestres') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('horarios'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('horarios.index')" :active="request()->routeIs('horarios.*')">
                                {{ __('Horarios') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('horarios'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('horarios.import')" :active="request()->routeIs('horarios.import*')">
                                {{ __('Importar Horarios') }}
                            </x-nav-link>
                        </div>
                    @endif

                    @if(Auth::user()->hasModule('estadisticas'))
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('estadisticas.index')" :active="request()->routeIs('estadisticas.*')">
                                {{ __('Estadísticas') }}
                            </x-nav-link>
                        </div>
                    @endif

                @endif
                {{-- === END CUSTOM ROLE LINKS === --}}
                {{-- === START DOCENTE LINKS === --}}
                @if(Auth::user() && Auth::user()->hasRole('docente'))

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('docente.asistencia')" :active="request()->routeIs('docente.asistencia')">
                            {{ __('Marcar Asistencia') }}
                        </x-nav-link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('docente.estadisticas')" :active="request()->routeIs('docente.estadisticas', 'estadisticas.show')">
                            {{ __('Mis Estadísticas') }}
                        </x-nav-link>
                    </div>

                @endif
                {{-- === END DOCENTE LINKS === --}}

            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                            {{-- Check if user is logged in before accessing name --}}
                            <div>{{ Auth::user() ? Auth::user()->name : 'Guest' }}</div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

             {{-- === START RESPONSIVE ADMIN LINKS === --}}
            @if(Auth::user() && Auth::user()->hasRole('admin'))
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                    {{ __('Roles') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docentes.index')" :active="request()->routeIs('docentes.*')">
                    {{ __('Docentes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                    {{ __('Materias') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('aulas.index')" :active="request()->routeIs('aulas.*')">
                    {{ __('Aulas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                    {{ __('Grupos') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('semestres.index')" :active="request()->routeIs('semestres.*')">
                    {{ __('Semestres') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('horarios.index')" :active="request()->routeIs('horarios.*', 'horarios.asistencias.*')">
                    {{ __('Horarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('horarios.import')" :active="request()->routeIs('horarios.import*')">
                    {{ __('Importar Horarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('estadisticas.index')" :active="request()->routeIs('estadisticas.*')">
                    {{ __('Estadísticas') }}
                </x-responsive-nav-link>
            @endif
             {{-- === END RESPONSIVE ADMIN LINKS === --}}

             {{-- === START RESPONSIVE CUSTOM ROLE LINKS === --}}
            @if(Auth::user() && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('docente'))

                @if(Auth::user()->hasModule('usuarios'))
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('Usuarios') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('roles'))
                    <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                        {{ __('Roles') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('docentes'))
                    <x-responsive-nav-link :href="route('docentes.index')" :active="request()->routeIs('docentes.*')">
                        {{ __('Docentes') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('materias'))
                    <x-responsive-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                        {{ __('Materias') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('aulas'))
                    <x-responsive-nav-link :href="route('aulas.index')" :active="request()->routeIs('aulas.*')">
                        {{ __('Aulas') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('grupos'))
                    <x-responsive-nav-link :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                        {{ __('Grupos') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('semestres'))
                    <x-responsive-nav-link :href="route('semestres.index')" :active="request()->routeIs('semestres.*')">
                        {{ __('Semestres') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('horarios'))
                    <x-responsive-nav-link :href="route('horarios.index')" :active="request()->routeIs('horarios.*')">
                        {{ __('Horarios') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('horarios'))
                    <x-responsive-nav-link :href="route('horarios.import')" :active="request()->routeIs('horarios.import*')">
                        {{ __('Importar Horarios') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasModule('estadisticas'))
                    <x-responsive-nav-link :href="route('estadisticas.index')" :active="request()->routeIs('estadisticas.*')">
                        {{ __('Estadísticas') }}
                    </x-responsive-nav-link>
                @endif

            @endif
             {{-- === END RESPONSIVE CUSTOM ROLE LINKS === --}}

             {{-- === START RESPONSIVE DOCENTE LINKS === --}}
            @if(Auth::user() && Auth::user()->hasRole('docente'))
                <x-responsive-nav-link :href="route('docente.asistencia')" :active="request()->routeIs('docente.asistencia')">
                    {{ __('Marcar Asistencia') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.estadisticas')" :active="request()->routeIs('docente.estadisticas', 'estadisticas.show')">
                    {{ __('Mis Estadísticas') }}
                </x-responsive-nav-link>
            @endif
             {{-- === END RESPONSIVE DOCENTE LINKS === --}}

        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                {{-- Check if user is logged in --}}
                @if(Auth::user())
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                @endif
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
