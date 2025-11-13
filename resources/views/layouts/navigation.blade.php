<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                {{-- === START ADMIN LINKS === --}}
                @if(Auth::user() && Auth::user()->hasRole('admin'))

                    {{-- PAQUETE 1: USUARIOS Y ROLES --}}
                    <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                        <x-nav-dropdown title="Usuarios y Roles" :active="request()->routeIs('users.*', 'roles.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </x-slot>
                            <x-dropdown-item :href="route('users.index')" :active="request()->routeIs('users.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </x-slot>
                                {{ __('Usuarios') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </x-slot>
                                {{ __('Roles') }}
                            </x-dropdown-item>
                        </x-nav-dropdown>
                    </div>

                    {{-- PAQUETE 2: GESTIÓN DE PERIODO ACADÉMICO --}}
                    <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                        <x-nav-dropdown title="Periodo Académico" :active="request()->routeIs('docentes.*', 'materias.*', 'aulas.*', 'grupos.*', 'semestres.*', 'horarios.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </x-slot>
                            <x-dropdown-item :href="route('docentes.index')" :active="request()->routeIs('docentes.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </x-slot>
                                {{ __('Docentes') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </x-slot>
                                {{ __('Materias') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('aulas.index')" :active="request()->routeIs('aulas.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </x-slot>
                                {{ __('Aulas') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </x-slot>
                                {{ __('Grupos') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('semestres.index')" :active="request()->routeIs('semestres.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </x-slot>
                                {{ __('Semestres') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('horarios.index')" :active="request()->routeIs('horarios.*', 'horarios.asistencias.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </x-slot>
                                {{ __('Horarios') }}
                            </x-dropdown-item>
                        </x-nav-dropdown>
                    </div>

                    {{-- PAQUETE 3: GESTIÓN DE REPORTES --}}
                    <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                        <x-nav-dropdown title="Reportes" :active="request()->routeIs('audit-logs.*', 'estadisticas.*', 'horarios.import*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </x-slot>
                            <x-dropdown-item :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </x-slot>
                                {{ __('Bitácora') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('horarios.import')" :active="request()->routeIs('horarios.import*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </x-slot>
                                {{ __('Importar Horarios') }}
                            </x-dropdown-item>
                            <x-dropdown-item :href="route('estadisticas.index')" :active="request()->routeIs('estadisticas.*')">
                                <x-slot name="icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </x-slot>
                                {{ __('Estadísticas') }}
                            </x-dropdown-item>
                        </x-nav-dropdown>
                    </div>

                @endif
                {{-- === END ADMIN LINKS === --}}

                {{-- === START CUSTOM ROLE LINKS (based on modules) === --}}
                @if(Auth::user() && !Auth::user()->hasRole('admin') && !Auth::user()->hasRole('docente'))

                    {{-- PAQUETE 1: USUARIOS Y ROLES --}}
                    @if(Auth::user()->hasModule('usuarios') || Auth::user()->hasModule('roles'))
                    <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                        <x-nav-dropdown title="Usuarios y Roles" :active="request()->routeIs('users.*', 'roles.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </x-slot>
                            @if(Auth::user()->hasModule('usuarios'))
                                <x-dropdown-item :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    {{ __('Usuarios') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('roles'))
                                <x-dropdown-item :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                                    {{ __('Roles') }}
                                </x-dropdown-item>
                            @endif
                        </x-nav-dropdown>
                    </div>
                    @endif

                    {{-- PAQUETE 2: GESTIÓN DE PERIODO ACADÉMICO --}}
                    @if(Auth::user()->hasModule('docentes') || Auth::user()->hasModule('materias') || Auth::user()->hasModule('aulas') || Auth::user()->hasModule('grupos') || Auth::user()->hasModule('semestres') || Auth::user()->hasModule('horarios'))
                    <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                        <x-nav-dropdown title="Periodo Académico" :active="request()->routeIs('docentes.*', 'materias.*', 'aulas.*', 'grupos.*', 'semestres.*', 'horarios.*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </x-slot>
                            @if(Auth::user()->hasModule('docentes'))
                                <x-dropdown-item :href="route('docentes.index')" :active="request()->routeIs('docentes.*')">
                                    {{ __('Docentes') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('materias'))
                                <x-dropdown-item :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                                    {{ __('Materias') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('aulas'))
                                <x-dropdown-item :href="route('aulas.index')" :active="request()->routeIs('aulas.*')">
                                    {{ __('Aulas') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('grupos'))
                                <x-dropdown-item :href="route('grupos.index')" :active="request()->routeIs('grupos.*')">
                                    {{ __('Grupos') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('semestres'))
                                <x-dropdown-item :href="route('semestres.index')" :active="request()->routeIs('semestres.*')">
                                    {{ __('Semestres') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('horarios'))
                                <x-dropdown-item :href="route('horarios.index')" :active="request()->routeIs('horarios.*')">
                                    {{ __('Horarios') }}
                                </x-dropdown-item>
                            @endif
                        </x-nav-dropdown>
                    </div>
                    @endif

                    {{-- PAQUETE 3: GESTIÓN DE REPORTES --}}
                    @if(Auth::user()->hasModule('bitacora') || Auth::user()->hasModule('importacion') || Auth::user()->hasModule('estadisticas'))
                    <div class="hidden sm:-my-px sm:ms-6 sm:flex items-center">
                        <x-nav-dropdown title="Reportes" :active="request()->routeIs('audit-logs.*', 'estadisticas.*', 'horarios.import*')">
                            <x-slot name="icon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </x-slot>
                            @if(Auth::user()->hasModule('bitacora'))
                                <x-dropdown-item :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                                    <x-slot name="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </x-slot>
                                    {{ __('Bitácora') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('importacion'))
                                <x-dropdown-item :href="route('horarios.import')" :active="request()->routeIs('horarios.import*')">
                                    <x-slot name="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </x-slot>
                                    {{ __('Importar Horarios') }}
                                </x-dropdown-item>
                            @endif
                            @if(Auth::user()->hasModule('estadisticas'))
                                <x-dropdown-item :href="route('estadisticas.index')" :active="request()->routeIs('estadisticas.*')">
                                    <x-slot name="icon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </x-slot>
                                    {{ __('Estadísticas') }}
                                </x-dropdown-item>
                            @endif
                        </x-nav-dropdown>
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

                {{-- PAQUETE 1: USUARIOS Y ROLES --}}
                <div class="px-4 pt-4 pb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                    Usuarios y Roles
                </div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                    {{ __('Roles') }}
                </x-responsive-nav-link>

                {{-- PAQUETE 2: GESTIÓN DE PERIODO ACADÉMICO --}}
                <div class="px-4 pt-4 pb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                    Periodo Académico
                </div>
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

                {{-- PAQUETE 3: GESTIÓN DE REPORTES --}}
                <div class="px-4 pt-4 pb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                    Reportes
                </div>
                <x-responsive-nav-link :href="route('audit-logs.index')" :active="request()->routeIs('audit-logs.*')">
                    {{ __('Bitácora') }}
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

                {{-- PAQUETE 1: USUARIOS Y ROLES --}}
                @if(Auth::user()->hasModule('usuarios') || Auth::user()->hasModule('roles'))
                    <div class="px-4 pt-4 pb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Usuarios y Roles
                    </div>
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
                @endif

                {{-- PAQUETE 2: GESTIÓN DE PERIODO ACADÉMICO --}}
                @if(Auth::user()->hasModule('docentes') || Auth::user()->hasModule('materias') || Auth::user()->hasModule('aulas') || Auth::user()->hasModule('grupos') || Auth::user()->hasModule('semestres') || Auth::user()->hasModule('horarios'))
                    <div class="px-4 pt-4 pb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Periodo Académico
                    </div>
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
                @endif

                {{-- PAQUETE 3: GESTIÓN DE REPORTES --}}
                @if(Auth::user()->hasModule('estadisticas') || Auth::user()->hasModule('horarios'))
                    <div class="px-4 pt-4 pb-1 text-xs font-semibold tracking-wider text-gray-500 uppercase">
                        Reportes
                    </div>
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
